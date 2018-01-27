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
use kitsunenokenja\leynos\file_system\PostFile;
use kitsunenokenja\leynos\http\{Headers as HTTPHeaders, Request};
use kitsunenokenja\leynos\memory_store\{MemoryStore, Session};
use kitsunenokenja\leynos\message\Message;
use kitsunenokenja\leynos\route\{Group, Route, RoutingException};
use kitsunenokenja\leynos\view\{BinaryView, JSONView, TemplateView, View};
use PDO;

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
    * @var int
    */
   private $_request_method = Route::GET;

   /**
    * The mode in which the framework should respond based on the nature of the request. This mode dictates which view
    * to engage.
    *
    * @var string
    */
   private $_response_mode = "HTML";

   /**
    * The HTTP Accept Language header's contents received from the client. The framework will pass this information
    * along to the application which can elect to make localisation-related decisions based on the contents of this
    * header.
    *
    * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
    *
    * @var string
    */
   private $_accept_language;

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
    * Internal reference to the session store.
    *
    * @var Session
    */
   private $_Session;

   /**
    * Reference to the Request object containing the requested group/route and query string/POST data.
    *
    * @var Request
    */
   private $_Request;

   /**
    * Array of messages generated by controllers.
    *
    * @var Message[]
    */
   private $_Messages = [];

   /**
    * Array of post files.
    *
    * @var PostFile[]
    */
   private $_Files = [];

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
    * References to databases. Controllers access databases via lazy-loading of PDO connections; when they do, PDO
    * instances are filed here with the original credentials alias key preserved. These connections persist within the
    * kernel so that subsequent controllers do not require reopening connections.
    *
    * @var PDO[]
    */
   private $_Databases = [];

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
   private function _boot(): void
   {
      try
      {
         // Explicitly define locale settings for PHP date and currency formatting functions according to config
         date_default_timezone_set($this->_Config->getTimezone());
         setlocale(LC_MONETARY, $this->_Config->getLocale());

         // Obtain server environment variables and free the super-global from memory
         $this->_processServerGlobal();

         // Process the standard files super-global
         $this->_processFilesGlobal();

         // Register additional necessary autoloaders from the config
         $this->_registerAutoloaders();

         // Connect to the framework cache
         $this->_MemStore = $this->_Config->getMemoryStore();
         $this->_MemStore->setNamespace($this->_Config->getCacheNamespace());

         // Resume user session and close it for writing immediately to release the resource lock on the session
         $this->_Session = new Session();
         $this->_Session->close();

         // Try to derive the execution routing path from the request
         $Route = $this->_parseRouteRequest($this->_request_url);

         // If session is required then a valid login must be present
         if($this->_Config->getOptions()->getSessionRequired() && !$this->_Config->isAuthenticated($this->_Session))
         {
            // Ensure there is a route defined
            if($this->_Config->getOptions()->getLoginRoute() === null)
               throw new RoutingException("Session requirement is engaged without a defined login redirection route.");

            // Redirect the unauthenticated user to the login route
            $this->_HTTPHeaders->redirect($this->_Config->getOptions()->getLoginRoute());
         }

         // Check user authorisation
         if($Route->getPermissionToken() !== null && empty($this->_permissions[$Route->getPermissionToken()]))
            throw new UnauthorizedActionException("Unauthorised request from authenticated user.");

         // Prepare a template engine for controllers if the route requires it
         if($this->_Config->getOptions()->getEnableTemplateEngine())
         {
            $this->_TemplateEngine = $this->_Config->getTemplateEngine($this->_document_root);
         }

         // Process the execution path dictated by the route by iterating through its controller list
         $data = [];
         $this->_Messages = $this->_Session->getKey("_Messages") ?? [];
         $status = $this->_executeControllers($Route, $data);

         // Save controller-generated messages. This stack will continue to accumulate until an HTML view is served.
         if($this->_Messages !== [])
         {
            $this->_Session->open();
            $this->_Session->setKey("_Messages", $this->_Messages);
         }

         // Respond via view
         $this->_renderView($Route, $status, $data);
      }
      catch(Exception | Error $E)
      {
         // Respond to bad route requests with a 404
         if($E instanceof RoutingException)
         {
            $this->_HTTPHeaders->notFound();
         }
         // Valid but unauthorised requests respond with a 403
         elseif($E instanceof UnauthorizedActionException)
         {
            $this->_HTTPHeaders->forbidden();
         }
         // All other failures result in 500
         else
         {
            // Register the failure in the log
            trigger_error($E->getMessage(), E_USER_WARNING);

            $this->_HTTPHeaders->internalServerError();
         }

         // This is already a last-resort fallback. If this response still fails with a raised exception, there is
         // nothing more to attempt as the problem is a misconfiguration by the application developer.
         try
         {
            $Route = $this->_parseRouteRequest($this->_Config->getErrorRoute());
            $data = ['Error' => $E];
            $this->_executeControllers($Route, $data);
            $this->_renderView($Route, true, $data);
         }
         catch(Exception $E)
         {
            // This last-resort fallback should never fail, but if it does, log the incident to expose the
            // misconfiguration.
            trigger_error($E->getMessage(), E_USER_ERROR);
         }
      }
   }

   /**
    * Captures useful environment values provided by PHP's $_SERVER super-global and unsets it to free memory.
    */
   private function _processServerGlobal(): void
   {
      $this->_document_root   = $_SERVER['DOCUMENT_ROOT'];
      $this->_request_url     = $_SERVER['REQUEST_URI'];
      $this->_request_method  = $_SERVER['REQUEST_METHOD'] === "POST" ? Route::POST : Route::GET;
      $this->_accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;

      unset($_SERVER);
   }

   /**
    * Processes the $_FILES super-global and populates the internal post files array, then unsets to free memory.
    */
   private function _processFilesGlobal(): void
   {
      // If there are no post files, drop the super-global and return immediately
      if(empty($_FILES))
      {
         unset($_FILES);
         return;
      }

      foreach($_FILES as $key_name => $details)
      {
         // If the details are not an array, the key points to a single file
         if(!is_array($details))
         {
            // Ignore invalid or fraudulent attempts
            if(!is_uploaded_file($details['tmp_name']))
               continue;

            $this->_Files[] = new PostFile(
               $details['name'],
               $details['type'],
               $details['size'],
               $details['tmp_name'],
               $details['error']
            );
         }
         // Otherwise, the key is for a submission of multiple files
         else
         {
            foreach($details['name'] as $index => $name)
            {
               // Ignore invalid or fraudulent attempts
               if(!is_uploaded_file($details['tmp_name']))
                  continue;

               $this->_Files[] = new PostFile(
                  $name,
                  $details['type'][$index],
                  $details['size'][$index],
                  $details['tmp_name'][$index],
                  $details['error'][$index]
               );
            }
         }
      }

      unset($_FILES);
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
    * Parses the route request to resolve which route to execute, then returns it.
    *
    * @param string $route_request
    *
    * @returns Route
    *
    * @throws RoutingException Thrown if the request is malformed or refers to an undefined route.
    */
   private function _parseRouteRequest(string $route_request): Route
   {
      // Translate a root index request to a defined route
      if($route_request === "/")
         $route_request = $this->_Config->getRootIndexRoute();

      // Verify the request is well-formed, and if so, capture the group/route names and response method accordingly
      if(preg_match($this->_Config->getRoutingPattern(), $route_request, $matches) !== 1)
      {
         throw new RoutingException("Invalid request route.");
      }

      // Build a request object from the route request
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
      if($this->_Config->getOptions()->getEnableRoutingCache())
      {
         if(($this->_Group = $this->_MemStore->getKey("route_group_cache:$group")) === null)
         {
            $this->_Group = new $group();
            $this->_MemStore->setKey("route_group_cache:$group", $this->_Group);
         }
      }
      else
      {
         $this->_Group = new $group();
      }

      // Apply group overrides to options
      $this->_overrideOptions($this->_Group->getOverrides());

      // Abort if the requested route isn't defined in the group and there is no default either
      if(($Route = $this->_Group->getRoute($this->_Request->getRoute(), $this->_request_method)) === null &&
         ($Route = $this->_Group->getDefaultRoute($this->_request_method)) === null)
      {
         throw new RoutingException("Undefined route.");
      }

      // Apply route overrides to options and return the route
      $this->_overrideOptions($Route->getOverrides());
      return $Route;
   }

   /**
    * Executes a controller sequence for a route then returns the status of the operation.
    *
    * @param Route $Route The route whose controllers to execute.
    * @param array $data  Array of volatile inputs/outputs that persist between controllers.
    *
    * @return bool
    */
   private function _executeControllers(Route $Route, array &$data): bool
   {
      $success = true;
      $data = array_merge($data, $Route->getInputs());
      foreach(array_merge($this->_Group->globalControllers(), $Route->getControllers()) as $controller)
      {
         // Instantiate the controller
         /** @var Controller $Controller */
         $Controller = new $controller();

         // Pass along the document root and accept language contents
         $Controller->setDocumentRoot($this->_document_root);
         $Controller->setAcceptLanguage($this->_accept_language);

         // Pass the DB credentials and connections to the controller
         $Controller->setPDOSettings($this->_Config->getPDOSettings());
         $Controller->setDatabases($this->_Databases);

         // Pass the HTTP Headers reference to the controller
         $Controller->setHTTPHeaders($this->_HTTPHeaders);

         // Always provide references to request/memory stores
         $Controller->addInput("Session", $this->_Session);
         $Controller->addInput("Request", $this->_Request);
         $Controller->addInput("Files", $this->_Files);
         $Controller->addInput("MemStore", $this->_MemStore);
         $Controller->addInput("TemplateEngine", $this->_TemplateEngine);

         // Supply memory store values based on the provided mapping
         foreach($Route->getStoreInputsMap()[MemoryStore::REQUEST] as $store_key)
         {
            $Controller->addInput($store_key, $this->_Request->__get($store_key));
         }
         foreach($Route->getStoreInputsMap()[MemoryStore::SESSION] as $store_key)
         {
            $Controller->addInput($store_key, $this->_Session->getKey($store_key));
         }
         foreach($Route->getStoreInputsMap()[MemoryStore::GLOBAL_STORE] as $store_key)
         {
            $Controller->addInput($store_key, $this->_MemStore->getKey($store_key));
         }
         foreach($Route->getStoreInputsMap()[MemoryStore::LOCAL_STORE] as $store_key)
         {
            $Controller->addInput(
               $store_key,
               $this->_MemStore->getKey($this->_Config->getUserStoreNamespace() . $store_key)
            );
         }

         // Pass a reference to the data array. This allows chaining outputs from previous controllers as input
         // to another.
         $Controller->addInput("_data", $data);

         // Execute the controller
         $success = $Controller->main();

         // Capture controller outputs and messages
         $data = array_merge($data, $Controller->getOutputs());
         $this->_Messages = array_merge($this->_Messages, $Controller->getMessages());

         // Capture current DB connections for persistence
         $this->_Databases = $Controller->getDatabases();

         // Only accept actual view instances to allow controllers that run after the one that actually outputs one
         // without clobbering it
         if($Controller->getBinaryView() !== null)
            $this->_View = $Controller->getBinaryView();

         // End execution if the controller failed
         if(!$success)
            break;
      }

      // Transform the data output array if an output map has been defined
      if(($map = $Route->getOutputMap()) !== null)
      {
         // For each alias in the map, copy from the current output array to the new one
         $mapped_data = [];
         foreach($map as $source => $destination)
            $mapped_data[$destination] = $data[$source] ?? null;

         // Clobber the old output array
         $data = $mapped_data;
      }

      return $success;
   }

   /**
    * Conclude the execution with a response based on the view to use.
    *
    * @param Route $Route  The route whose view to render.
    * @param bool  $status The success state from the controller execution loop.
    * @param array $data   Accumulation of data for the view to consume.
    *
    * @throws ControllerFailureException Thrown if a controller loop fails without redirection handling.
    * @throws Exception                  General failure for inappropriate attempts to render binary views.
    */
   private function _renderView(Route $Route, bool $status, array $data): void
   {
      // Check for redirects prior to defaulting to rendering a view
      if($status && $Route->getRedirectRoute() !== null)
      {
         $this->_HTTPHeaders->redirect($Route->getRedirectRoute());
         return;
      }

      if(!$status && $Route->getFailureRoute() !== null)
      {
         $this->_HTTPHeaders->redirect($Route->getFailureRoute());
         return;
      }

      // Check for failure without redirects
      if(!$status)
         throw new ControllerFailureException($data['error'] ?? "Kernel: Unknown failure.");

      // Initialise the view engine with respect to the response mode
      switch($this->_response_mode)
      {
         case "HTML":
         default:
            // Flush messages
            if(!empty($this->_Session->getKey("_Messages")))
            {
               $this->_Session->open();
               $this->_Session->setKey("_Messages", null);
            }
            $data['_messages'] = [];
            foreach($this->_Messages as $Message)
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

         case "CSV":
         case "ODS":
         case "XLSX":
         case "PDF":
            if(!$this->_View instanceof BinaryView)
               throw new Exception("Binary view requested but no rendering view was provided.");

            $this->_HTTPHeaders->contentType($this->_View->getMIMEType());
            $this->_HTTPHeaders->contentDisposition($this->_View->getFileName(), true);
            break;
      }
      $this->_View->render($data);
   }

   /**
    * Updates the config's options with the overrides from the route or group.
    *
    * @param bool[] $overrides
    */
   private function _overrideOptions(array $overrides): void
   {
      $Options = $this->_Config->getOptions();
      $Options->setSessionRequired($overrides[Options::SESSION_REQUIRED] ?? $Options->getSessionRequired());
      $Options->setEnableTemplateEngine(
         $overrides[Options::ENABLE_TEMPLATE_ENGINE] ?? $Options->getEnableTemplateEngine()
      );
      $Options->setEnableRoutingCache($overrides[Options::ENABLE_ROUTING_CACHE] ?? $Options->getEnableRoutingCache());
   }
}
