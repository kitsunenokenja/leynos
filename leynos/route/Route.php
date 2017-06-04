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

namespace kitsunenokenja\leynos\route;

use kitsunenokenja\leynos\config\Options;
use kitsunenokenja\leynos\memory_store\MemoryStore;

/**
 * Route
 *
 * A route defines what controller sequence to execute as well as specific options for that process.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Route
{
   use OptionsOverrides;

   // HTTP request types for routes
   const GET = 1;
   const POST = 2;

   /**
    * The route name.
    *
    * @var string
    */
   private $_name;

   /**
    * The HTTP request for which method the route is defined e.g. GET/POST.
    *
    * @var int
    */
   private $_request_method = self::GET;

   /**
    * Any additional inputs may be defined.
    *
    * @var array
    */
   private $_inputs = [];

   /**
    * Any symbolic keys for each store whose values to prepare as proper inputs.
    *
    * @var string[][]
    */
   private $_store_inputs_map = [
      MemoryStore::REQUEST      => [],
      MemoryStore::SESSION      => [],
      MemoryStore::LOCAL_STORE  => [],
      MemoryStore::GLOBAL_STORE => []
   ];

   /**
    * The sequence of controllers to execute.
    *
    * @var string[]
    */
   private $_controllers = [];

   /**
    * The file name of the template for HTML responses.
    *
    * @var string
    */
   private $_template;

   /**
    * The permission token required to access the route. If this remains as an empty string, no permission is required.
    *
    * @var string
    */
   private $_permission_token;

   /**
    * Error message to display if a controller returns unsuccessfully.
    *
    * @var string
    */
   private $_error;

   /**
    * If set, a redirect is triggered to this route rather than responding normally.
    *
    * @var string
    */
   private $_redirect_route;

   /**
    * If set, a redirect is triggered to this route rather than responding normally upon failing controller.
    *
    * @var string
    */
   private $_failure_route;

   /**
    * Routine that modifies the global options.
    *
    * @var callable
    */
   private $_OptionsModifier;

   /**
    * Route constructor.
    *
    * @param string $name
    * @param array  $controllers
    */
   public function __construct(string $name, array $controllers)
   {
      $this->_name = $name;
      $this->_controllers = $controllers;
   }

   /**
    * Returns the route name.
    *
    * @return string
    */
   public function getName(): string
   {
      return $this->_name;
   }

   /**
    * Returns the request method.
    *
    * @return int
    */
   public function getRequestMethod(): int
   {
      return $this->_request_method;
   }

   /**
    * Sets the request method.
    *
    * @param int $method
    */
   public function setRequestMethod(int $method): void
   {
      $this->_request_method = $method;
   }

   /**
    * Returns the additional inputs for the route's controllers.
    *
    * @return array
    */
   public function getInputs(): array
   {
      return $this->_inputs;
   }

   /**
    * Adds an input for the route's controllers by key/value pair.
    *
    * @param string $key
    * @param mixed  $value
    */
   public function addInput(string $key, $value): void
   {
      $this->_inputs[$key] = $value;
   }

   /**
    * Returns the store inputs map.
    *
    * @return string[][]
    */
   public function getStoreInputsMap(): array
   {
      return $this->_store_inputs_map;
   }

   /**
    * Adds a key by its corresponding memory store to an internal map for the kernel to process and supply values.
    *
    * @param int    $store The store where to query the key.
    * @param string $key   The key whose value should be provided as an input.
    */
   public function addStoreInput(int $store, string $key): void
   {
      $this->_store_inputs_map[$store][] = $key;
   }

   /**
    * Returns the array of controller class names to instantiate and execute.
    *
    * @return string[]
    */
   public function getControllers(): array
   {
      return $this->_controllers;
   }

   /**
    * Returns the template file name.
    *
    * @return string
    */
   public function getTemplate(): ?string
   {
      return $this->_template;
   }

   /**
    * Sets the template file name.
    *
    * @param string $template
    */
   public function setTemplate(string $template): void
   {
      $this->_template = $template;
   }

   /**
    * Returns the permission token.
    *
    * @return string
    */
   public function getPermissionToken(): ?string
   {
      return $this->_permission_token;
   }

   /**
    * Sets the permission token.
    *
    * @param string $permission_token
    */
   public function setPermissionToken(string $permission_token): void
   {
      $this->_permission_token = $permission_token;
   }

   /**
    * Returns the error string.
    *
    * @return string
    */
   public function getError(): ?string
   {
      return $this->_error;
   }

   /**
    * Returns the redirect route.
    *
    * @return string
    */
   public function getRedirectRoute(): ?string
   {
      return $this->_redirect_route;
   }

   /**
    * Sets the redirect route.
    *
    * @param string $redirect_route
    */
   public function setRedirectRoute(string $redirect_route): void
   {
      $this->_redirect_route = $redirect_route;
   }

   /**
    * Returns the failure route.
    *
    * @return string
    */
   public function getFailureRoute(): ?string
   {
      return $this->_failure_route;
   }

   /**
    * Sets the failure route.
    *
    * @param string $failure_route
    */
   public function setFailureRoute(string $failure_route): void
   {
      $this->_failure_route = $failure_route;
   }

   /**
    * Modifies the global options for the route.
    *
    * @param Options $Options
    */
   public function modifyOptions(Options $Options): void
   {
      if($this->_OptionsModifier !== null)
         call_user_func($this->_OptionsModifier, $Options);
   }

   /**
    * Sets a function as the options modifier for the route.
    *
    * @param callable $func
    */
   public function setOptionsModifier(callable $func): void
   {
      $this->_OptionsModifier = $func;
   }
}
