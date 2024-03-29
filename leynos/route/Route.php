<?php declare(strict_types=1);
/**
 * Copyright (c) 2016.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\route;

use kitsunenokenja\leynos\config\Options;
use kitsunenokenja\leynos\controller\Slice;
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

   // Wildcard token for default routes which handle otherwise undefined route names
   const DEFAULT_ROUTE = "*";

   /**
    * The route name.
    *
    * @var string
    */
   private string $_name;

   /**
    * The HTTP request for which method the route is defined e.g. GET/POST.
    *
    * @var int
    */
   private int $_request_method = self::GET;

   /**
    * Any additional inputs may be defined.
    *
    * @var array
    */
   private array $_inputs = [];

   /**
    * Any symbolic keys for each store whose values to prepare as proper inputs.
    *
    * @var string[][]
    */
   private array $_store_inputs_map = [
      MemoryStore::REQUEST      => [],
      MemoryStore::SESSION      => [],
      MemoryStore::LOCAL_STORE  => [],
      MemoryStore::GLOBAL_STORE => []
   ];

   /**
    * Array of aliases mapping actual output names to new values. The definition of an output map will override the
    * default behaviour of supplying all available outputs to the view by providing only what the map dictates.
    *
    * @var array
    */
   private ?array $_output_map = null;

   /**
    * The sequence of controller slices to execute.
    *
    * @var Slice[]
    */
   private array $_slices = [];

    /**
     * Set of aliases for the route. Any number of aliases can be defined for the route and will function identically
     * to calling the route by its original name.
     *
     * @var string[]
     */
   private array $_aliases = [];

   /**
    * The permission token required to access the route. If this remains empty, no permission is required.
    *
    * @var string
    */
   private ?string $_permission_token = null;

   /**
    * Routine that modifies the global options.
    *
    * @var callable
    */
   private $_OptionsModifier;

   /**
    * Creates a route object.
    *
    * @param string  $name
    * @param Slice[] $Slices
    */
   public function __construct(string $name, array $slices = [])
   {
      $this->_name = $name;
      $this->_slices = $slices;
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
    * Returns the output map.
    *
    * @return array
    */
   public function getOutputMap(): ?array
   {
      return $this->_output_map;
   }

   /**
    * Sets the output map.
    *
    * @param array $output_map
    */
   public function setOutputMap(array $output_map): void
   {
      $this->_output_map = $output_map;
   }

   /**
    * Returns the array of controller slices whose controllers to instantiate and execute.
    *
    * @return Slice[]
    */
   public function getSlices(): array
   {
      return $this->_slices;
   }

   /**
    * Returns the set of aliases for the route.
    *
    * @return string[]
    */
   public function getAliases(): array
   {
      return $this->_aliases;
   }

   /**
    * Adds an alias for the route.
    *
    * @param string $alias
    */
   public function addAlias(string $alias): void
   {
      $this->_aliases[] = $alias;
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

# vim: set ts=3 sw=3 tw=120 et :
