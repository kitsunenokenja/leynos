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

namespace kitsunenokenja\leynos\controller;

use kitsunenokenja\leynos\memory_store\MemoryStore;

/**
 * Slice
 *
 * The controller slice is the encapsulating container for a controller. This container enables the chaining of
 * controllers for routes, and exposes functionality to the framework to engage and resolve execution of the controller
 * within.
 *
 * This class bears redundant methods to facilitate two styles of usage. The traditional methods are available for
 * setting values during route assembly, as well as a fluent interface initiated by the static "new" method which is
 * ideal for more brief expression during assembly. Route definitions may be assembled via either style or both styles.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Slice
{
   /**
    * Instance of the controller object.
    *
    * @var Controller
    */
   private ?Controller $_Controller = null;

   /**
    * Input map array containing key/value pairs keyed by the aliases.
    *
    * @var array
    */
   private array $_input_map = [];

   /**
    * Store input map array contains arrays of aliases keyed by memory store keys indicating which values from each
    * respective memory store will be provided to the controller as inputs. The values within the arrays can be either
    * strings for the variable names within the corresponding store, or a single-element hash mapping the actual name to
    * an alias.
    *
    * @var array
    */
   private array $_store_input_map = [
      MemoryStore::REQUEST => [],
      MemoryStore::SESSION => [],
      MemoryStore::LOCAL_STORE => [],
      MemoryStore::GLOBAL_STORE => [],
      MemoryStore::VOLATILE_STORE => []
   ];

   /**
    * The output map allows the outputs of a controller to be aliased when handed off back to the framework, and
    * effectively protects outputs from leaking down the pipeline by intentionally omitting them from this map.
    *
    * @var string[]
    */
   private ?array $_output_map = null;

   /**
    * Store output map array contains arrays, keyed by memory stores, of variable names or key/value pairs to alias
    * variables to be designated as obtainable output from the controller.
    *
    * @var array
    */
   private array $_store_output_map = [
      MemoryStore::REQUEST => [],
      MemoryStore::SESSION => [],
      MemoryStore::LOCAL_STORE => [],
      MemoryStore::GLOBAL_STORE => [],
      MemoryStore::VOLATILE_STORE => []
   ];

   /**
    * Array of Exit State objects that define response logic for any given exit state from the controller. To preserve
    * the flexibility of a completely open-ended controller execution, an empty exit state map is valid.
    *
    * @var ExitState[]
    */
   private array $_exit_state_map = [];

   /**
    * Creates a new slice object.
    *
    * @param string $controller The controller class name to instantiate.
    */
   public function __construct(string $controller = null)
   {
      if($controller !== null)
         $this->_Controller = new $controller();
   }

   /**
    * Returns a new slice object.
    *
    * @param string $controller The controller class name to instantiate.
    *
    * @return Slice
    */
   public static function new(string $controller = null): Slice
   {
      return new Slice($controller);
   }

   /**
    * Returns the controller instance encapsulated by the slice.
    *
    * @return Controller
    */
   public function getController(): ?Controller
   {
      return $this->_Controller;
   }

   /**
    * Returns the input map.
    *
    * @return array
    */
   public function getInputMap(): array
   {
      return $this->_input_map;
   }

   /**
    * Defines the input map which provides a series of aliased values to the controller.
    *
    * @param array $map
    */
   public function setInputMap(array $map): void
   {
      $this->_input_map = $map;
   }

   /**
    * Defines the input map which provides a series of aliased values to the controller.
    *
    * @param array $map
    *
    * @return Slice
    */
   public function inputMap(array $map): Slice
   {
      $this->_input_map = $map;
      return $this;
   }

   /**
    * Returns the store input map.
    *
    * @return array
    */
   public function getStoreInputMap(): array
   {
      return $this->_store_input_map;
   }

   /**
    * Defines the store input map which provides aliased values from memory stores to the controller.
    *
    * @param array $map
    */
   public function setStoreInputMap(array $map): void
   {
      $this->_flattenMap($map);
      $this->_store_input_map = array_replace($this->_store_input_map, $map);
   }

   /**
    * Defines the store input map which provides aliased values from memory stores to the controller.
    *
    * @param array $map
    *
    * @return Slice
    */
   public function storeInputMap(array $map): Slice
   {
      $this->_flattenMap($map);
      $this->_store_input_map = array_replace($this->_store_input_map, $map);
      return $this;
   }

   /**
    * Returns the output map.
    *
    * @return string[]
    */
   public function getOutputMap(): ?array
   {
      return $this->_output_map;
   }

   /**
    * Defines the output map that dictates the aliased values to be extracted by the framework.
    *
    * @param string[] $map
    */
   public function setOutputMap(array $map): void
   {
      $this->_output_map = $map;
   }

   /**
    * Defines the output map that dictates the aliased values to be extracted by the framework.
    *
    * @param string[] $map
    *
    * @return Slice
    */
   public function outputMap(array $map): Slice
   {
      $this->_output_map = $map;
      return $this;
   }

   /**
    * Returns the store output map.
    *
    * @return array
    */
   public function getStoreOutputMap(): array
   {
      return $this->_store_output_map;
   }

   /**
    * Defines the store output map indicating to the framework which key/value pairs to store in the designated memory
    * stores.
    *
    * @param array $map
    */
   public function setStoreOutputMap(array $map): void
   {
      $this->_flattenMap($map);
      $this->_store_output_map = array_replace($this->_store_output_map, $map);
   }

   /**
    * Defines the store output map indicating to the framework which key/value pairs to store in the designated memory
    * stores.
    *
    * @param array $map
    *
    * @return Slice
    */
   public function storeOutputMap(array $map): Slice
   {
      $this->_flattenMap($map);
      $this->_store_output_map = array_replace($this->_store_output_map, $map);
      return $this;
   }

   /**
    * Returns the exit state map.
    *
    * @return ExitState[]
    */
   public function getExitStateMap(): array
   {
      return $this->_exit_state_map;
   }

   /**
    * Defines the map directing how the framework should respond to the termination of execution of the controller based
    * on the exit state it returns.
    *
    * @param array $map
    */
   public function setExitStateMap(array $map): void
   {
      $this->_exit_state_map = $map;
   }

   /**
    * Defines the map directing how the framework should respond to the termination of execution of the controller based
    * on the exit state it returns.
    *
    * @param array $map
    *
    * @return Slice
    */
   public function exitStateMap(array $map): Slice
   {
      $this->_exit_state_map = $map;
      return $this;
   }

   /**
    * Normalises a given store map by flattening it into a hash. All default-keyed strings become keyed to themselves by
    * name, and single element hashes are broken out. This allows the kernel to process the store maps faster, and due
    * to caching this procedure is not constantly repeated.
    *
    * Unaliased map entries effectively become aliased and the original integer keys are dropped along the way.
    *
    * @param array &$store_map
    */
   private function _flattenMap(array &$store_map): void
   {
      foreach($store_map as $store => &$store_map)
      {
         foreach($store_map as $index => $entry)
         {
            !is_array($entry) ? $store_map[$entry] = $entry : $store_map[key($entry)] = current($entry);
            unset($store_map[$index]);
         }
      }
   }
}

# vim: set ts=3 sw=3 tw=120 et :
