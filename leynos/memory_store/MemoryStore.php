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

namespace kitsunenokenja\leynos\memory_store;

/**
 * MemoryStore
 *
 * All memory stores must extend this class. Implementations will wrap the memory stores they represent by defining the
 * methods enumerated here.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
abstract class MemoryStore
{
   // For routing group configuration usage
   const REQUEST        = 0;
   const SESSION        = 1;
   const LOCAL_STORE    = 2;
   const GLOBAL_STORE   = 3;
   const VOLATILE_STORE = 4;

   /**
    * String token to use as the namespace for keys.
    *
    * @var string
    */
   protected string $_namespace = "";

   /**
    * Sets the namespace prefix for the memory store.
    *
    * @param string $namespace
    */
   final public function setNamespace(string $namespace): void
   {
      $this->_namespace = $namespace;
   }

   /**
    * Returns the value for the specified key from the memory store.
    *
    * @param string $key
    *
    * @return mixed
    */
   abstract public function getKey(string $key);

   /**
    * Sets a value for the specified key in the memory store. If the key is already defined in the memory store, its
    * value will be overwritten.
    *
    * @param string $key
    * @param mixed  $value
    *
    * @return void
    */
   abstract public function setKey(string $key, $value): void;
}

# vim: set ts=3 sw=3 tw=120 et :
