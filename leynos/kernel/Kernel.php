<?php
/**
 * Copyright (c) 2016.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * http://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\kernel;

use Error;
use Exception;
use kitsunenokenja\leynos\config\{Config, Options};
use kitsunenokenja\leynos\controller\{Controller, ControllerFailureException};
use kitsunenokenja\leynos\http\{Headers as HTTPHeaders, Request};
use kitsunenokenja\leynos\memory_store\{MemoryStore, MemoryStoreException, Session};
use kitsunenokenja\leynos\route\{Group, Route, RoutingException};
use kitsunenokenja\leynos\view\{FPDFView, JSONView, SpreadsheetException, TemplateException, TemplateView, View};
use PDO;
use PDOException;

/**
 * Kernel
 *
 * Kernel functions as the master controller. All request handling executions start and end here.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Kernel
{
   /**
    * Internal copy of the document root.
    *
    * @var string
    */
   private $_document_root;

   /**
    * Internal copy of the requested document from the URL.
    *
    * @var string
    */
   private $_request_url;

   /**
    * Requested HTTP method i.e. GET/POST.
    *
    * @var string
    */
   private $_request_method = "GET";

   /**
    * The mode in which the framework should respond based on the nature of the request. This mode dictates which view
    * to engage.
    *
    * @var string
    */
   private $_response_mode = "HTML";

   /**
    * Hash of permissions that belong to the authenticated user. These are used to authorise users to execute certain
    * actions and influence template rendering.
    *
    * @var bool[]
    */
   private $_permissions = [];

   /**
    * Application configuration object.
    *
    * @var Config
    */
   private $_Config;

   /**
    * Reference to the HTTP headers object.
    *
    * @var HTTPHeaders
    */
   private $_HTTPHeaders;

   /**
    * Reference to the Request object containing the requested group/route and query string/POST data.
    *
    * @var Request
    */
   private $_Request;

   /**
    * Reference to the Group derived from the request for the current execution instance.
    *
    * @var Group
    */
   private $_Group;

   /**
    * Memory store reference that serves as the framework's cache.
    *
    * @var MemoryStore
    */
   private $_MemStore;

   /**
    * Reference to DB.
    *
    * @var PDO
    */
   private $_DB;

   /**
    * Reference to the view renderer.
    *
    * @var View
    */
   private $_View;

   /**
    * Reference to a template engine for controllers. This should be a view for HTML, XML, or some other lengthy
    * formatted string generator.
    *
    * @var TemplateView
    */
   private $_TemplateEngine;

   /**
    * Saves the configuration input and starts up the framework.
    *
    * @param Config $Config
    */
   public function __construct(Config $Config)
   {
      $this->_HTTPHeaders = new HTTPHeaders();
      $this->_Config = $Config;

      $this->_boot();
   }

   /**
    * Initialises the framework.
    */
   private function _boot()
   {
      try
      {
         // Explicitly define locale settings for PHP date and currency formatting functions according to config
         date_default_timezone_set($this->_Config->getTimezone());
         setlocale(LC_MONETARY, $this->_Config->getLocale());

         // Obtain server environment variables and free the super-global from memory
         $this->_processServerGlobal();

         // Register additional necessary autoloaders from the config
         $this->_registerAutoloaders();

         // Connect to the framework cache
         $this->_MemStore = $this->_Config->getMemoryStore();
         $this->_MemStore->setNamespace($this->_Config->getCacheNamespace());

         // Try to derive the execution routing path from the request
         $Route = $this->_parseRequestURL();

         // Open connection to DB for the controllers
         if($this->_Config->getOptions()->getConnectDatabase())
            $this->_connectDatabase();

         // Resume user session and close it for writing immediately to release the resource lock on the session
         $Session = new Session();
         $Session->close();

         // If session is required then a valid login must be present
         if($this->_Config->getOptions()->getSessionRequired())
         {
            /*
             * TODO - Devise programmable handling for authenticated user enforcement. The original code was dependent
             * on Leynos' original creation against an application which is wholly not portable.
             */
         }

         // Check user authorisation
         if($Route->getPermissionToken() !== "" && empty($this->_permissions[$Route->getPermissionToken()]))
            throw new UnauthorizedActionException("Unauthorised request from authenticated user.");

         // Prepare a template engine for controllers if the route requires it
         if($this->_Config->getOptions()->getEnableTemplateEngine())
         {
            $this->_TemplateEngine = $this->_Config->getTemplateEngine($this->_document_root);
         }

         // Process the execution path dictated by the route by iterating through its controller list
         $data = $Route->getInputs();
         $Messages = $Session->getKey("_Messages") ?? [];
         $success = true;
         foreach(array_merge($this->_Group->globalControllers(), $Route->getControllers()) as $controller)
         {
            // Instantiate the controller and pass the document root to it
            /** @var Controller $Controller */
            $Controller = new $controller();
            $Controller->setDocumentRoot($this->_document_root);

            // Pass the DB reference to the controller. If the DB was not connected, this safely passes null.
            $Controller->setDB($this->_DB);

            // Pass the HTTP Headers reference to the controller.
            $Controller->setHTTPHeaders($this->_HTTPHeaders);

            // Always provide references to request/memory stores.
            $Controller->addInput("Session", $Session);
            $Controller->addInput("Request", $this->_Request);
            $Controller->addInput("MemStore", $this->_MemStore);
            $Controller->addInput("TemplateEngine", $this->_TemplateEngine);

            // Pass a reference to the data array. This allows chaining outputs from previous controllers as input
            // to another.
            $Controller->addInput("_data", $data);

            // Execute the controller
            $success = $Controller->main();

            // Capture controller outputs and messages
            $data = array_merge($data, $Controller->getOutputs());
            $Messages = array_merge($Messages, $Controller->getMessages());

            // End execution if the controller failed
            if(!$success)
               break;
         }

         // Save controller-generated messages. This stack will continue to accumulate until an HTML view is served.
         if($Messages !== [])
         {
            $Session->open();
            $Session->setKey("_Messages", $Messages);
         }

         // Check for redirects prior to defaulting to rendering a view
         if($success && $Route->getRedirectRoute() !== "")
            $this->_HTTPHeaders->redirect($Route->getRedirectRoute());

         if(!$success && $Route->getFailureRoute() !== "")
            $this->_HTTPHeaders->redirect($Route->getFailureRoute());

         // Check for failure without redirects
         if(!$success)
            throw new ControllerFailureException($data['error'] ?? "Kernel: Unknown failure.");

         // Initialise the view engine with respect to the response mode
         switch($this->_response_mode)
         {
            case "HTML":
            default:
               // Flush messages
               if(!empty($Session->getKey("_Messages")))
               {
                  $Session->open();
                  $Session->setKey("_Messages", null);
               }
               $data['_messages'] = [];
               foreach($Messages as $Message)
                  $data['_messages'][] = $Message->jsonSerialize();

               // Make user permissions available in the view for content filtering & control
               $data['_permissions'] = $this->_permissions;

               // Prepare the view with the template to be rendered
               $View = $this->_Config->getTemplateEngine($this->_document_root);
               $View->setTemplate($Route->getTemplate());
               $this->_View = $View;
               break;
            case "JSON":
               $this->_View = new JSONView();
               $this->_HTTPHeaders->contentType(HTTPHeaders::JSON);
               break;

            // TODO - Update these cases when the generalised Spout library wrapper is completed for Leynos integration.
            case "CSV":
            case "ODS":
            case "XLSX":
               break;

            case "PDF":
               // TODO - Arbitrary keys for $data array is not that elegant. Replace this with a specific controller
               // extension that provides predictable getters returning these values.
               // TODO - This also should not be a hard-coded FPDF view. Replace with abstraction of PDF view.
               $this->_View = new FPDFView($data['PDF'] ?? null);
               $this->_HTTPHeaders->contentType(HTTPHeaders::PDF);
               $this->_HTTPHeaders->contentDisposition("{$data['file_name']}.pdf", true);
               break;
         }
         $this->_View->render($data);
      }
      // Respond to bad route requests with a 404
      catch(RoutingException $E)
      {
         $this->_HTTPHeaders->notFound();
         $this->_renderError("The requested document could not be found.");
      }
      // Valid but unauthorised requests
      catch(UnauthorizedActionException $E)
      {
         $this->_HTTPHeaders->forbidden();
         $this->_renderError("Permission denied.");
      }
      // Respond to controller failures
      catch(ControllerFailureException $E)
      {
         $this->_HTTPHeaders->internalServerError();
         $this->_renderError($E->getMessage());
      }
      // Template engine failure
      catch(TemplateException $E)
      {
         trigger_error($E->getMessage(), E_USER_WARNING);
         $this->_HTTPHeaders->internalServerError();
         $this->_renderError("Template failure.");
      }
      // Spreadsheet export failure
      catch(SpreadsheetException $E)
      {
         trigger_error($E->getMessage(), E_USER_WARNING);
         $this->_HTTPHeaders->internalServerError();
         $this->_renderError("Export failure.");
      }
      // DB failure
      catch(PDOException $E)
      {
         trigger_error($E->getMessage(), E_USER_WARNING);
         $this->_HTTPHeaders->internalServerError();
         $this->_renderError("Database communication error.");
      }
      // Memory store failures
      catch(MemoryStoreException $E)
      {
         trigger_error($E->getMessage(), E_USER_WARNING);
         $this->_HTTPHeaders->internalServerError();
         $this->_renderError("Internal server error.");
      }
      // All unhandled exceptions will return as a server fault
      catch(Exception $E)
      {
         trigger_error($E->getMessage(), E_USER_WARNING);
         $this->_HTTPHeaders->internalServerError();
         $this->_renderError("Internal server error.");
      }
      // Unhandled fatal errors exit gracefully with a server fault
      catch(Error $E)
      {
         trigger_error($E->getMessage(), E_USER_WARNING);
         $this->_HTTPHeaders->internalServerError();
         $this->_renderError("Internal server error.");
      }
   }

   /**
    * Captures useful environment values provided by PHP's $_SERVER super-global and unsets it to free memory.
    *
    * TODO - Capture more valuable env info e.g. client's locale.
    */
   private function _processServerGlobal()
   {
      $this->_document_root  = $_SERVER['DOCUMENT_ROOT'];
      $this->_request_url    = $_SERVER['REQUEST_URI'];
      $this->_request_method = $_SERVER['REQUEST_METHOD'] === "POST" ? "POST" : "GET";

      unset($_SERVER);
   }

   /**
    * Registers the autoloaders from configuration.
    *
    * @return bool
    */
   private function _registerAutoloaders(): bool
   {
      return spl_autoload_register(
         function($class)
         {
            foreach($this->_Config->getAutoloaders() as $Closure)
            {
               $Closure($class);
            }
         }
      );
   }

   /**
    * Parses the request URL to resolve which route to execute.
    *
    * @returns Route
    *
    * @throws RoutingException Thrown if the request is malformed or refers to an undefined route.
    */
   private function _parseRequestURL(): Route
   {
      // Valid routes have the pattern /group/route(/mode)?
      // TODO - Consider making this regex configurable to allow custom chars or making routes Unicode friendly.
      if(preg_match('#^/(\w+)/(\w+)(?:/(\w+))?(?:\?.*)?$#', $this->_request_url, $matches) !== 1)
      {
         throw new RoutingException("Invalid request route.");
      }

      // Build a Request object from the request URL
      $this->_Request = new Request($matches[1], $matches[2]);
      switch(isset($matches[3]) ? strtolower($matches[3]) : "html")
      {
         case "html":
         default:
            // Default response mode is HTML
            break;
         case "json":
            $this->_response_mode = "JSON";
            break;
         case "csv":
            $this->_response_mode = "CSV";
            break;
         case "ods":
            $this->_response_mode = "ODS";
            break;
         case "xlsx":
            $this->_response_mode = "XLSX";
            break;
         case "pdf":
            $this->_response_mode = "PDF";
            break;
      }

      // Abort if the requested group isn't defined in the map
      if(($group = $this->_Config->getRoutingGroup($this->_Request->getGroup())) === null)
      {
         throw new RoutingException("Undefined routing group.");
      }

      // Check cache for the route group definition
      if(($this->_Group = $this->_MemStore->getKey("route_group_cache:$group")) === null)
      {
         $this->_Group = new $group();
         $this->_MemStore->setKey("route_group_cache:$group", $this->_Group);
      }

      // Apply group overrides to options
      $this->_overrideOptions($this->_Group->getOverrides());

      // Abort if the requested route isn't defined in the group
      if(($Route = $this->_Group->getRoute($this->_Request->getRoute(), $this->_request_method)) === null)
      {
         throw new RoutingException("Undefined route.");
      }

      // Apply route overrides to options and return the route
      $this->_overrideOptions($Route->getOverrides());
      return $Route;
   }

   /**
    * Opens a connection to the DB.
    *
    * @throws PDOException Thrown if the connection to DB fails.
    */
   private function _connectDatabase()
   {
      $Cred = $this->_Config->getDBCredentials();
      $this->_DB = new PDO(
         "{$Cred->getDriver()}:host={$Cred->getHostname()};dbname={$Cred->getDatabaseSchema()}",
         $Cred->getUsername(),
         $Cred->getPassword()
      );
      $this->_DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }

   /**
    * This method is for responding upon exception catching by rendering an error message.
    *
    * @param string $message
    *
    * TODO - Replace this method's behaviour entirely. This approach is not localisation friendly and requires a
    * predetermined template to be defined, even if it wasn't hard-coded here. Replacing this with a proper solution
    * will likely be a last-effort reroute which will die with plain text if it still can't succeed, avoiding infinite
    * redirects to itself.
    */
   private function _renderError(string $message)
   {
      try
      {
         switch($this->_response_mode)
         {
            case "HTML":
            default:
               $View = $this->_Config->getTemplateEngine($this->_document_root);
               // This declaration could be configurable, but because this entire method needs to be refined completely,
               // factoring this out has no long-term merit.
               $View->setTemplate("error.twig");
               $this->_View = $View;
               break;
            case "JSON":
               $this->_View = new JSONView();
               break;
         }
         $this->_View->render(['error' => $message]);
      }
      catch(Exception $E)
      {
         die("Internal server error.");
      }
      catch(Error $E)
      {
         die("Internal server error.");
      }
   }

   /**
    * Updates the config's options with the overrides from the route or group.
    *
    * @param bool[] $overrides
    */
   private function _overrideOptions(array $overrides)
   {
      $Options = $this->_Config->getOptions();
      $Options->setConnectDatabase($overrides[Options::CONNECT_DATABASE] ?? $Options->getConnectDatabase());
      $Options->setSessionRequired($overrides[Options::SESSION_REQUIRED] ?? $Options->getSessionRequired());
      $Options->setEnableTemplateEngine($overrides[Options::ENABLE_TEMPLATE_ENGINE] ?? $Options->getEnableTemplateEngine());
   }
}
