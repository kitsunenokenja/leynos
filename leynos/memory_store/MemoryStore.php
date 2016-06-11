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

namespace kitsunenokenja\leynos\memory_store;

/**
 * MemoryStore
 *
 * All memory stores must implement this interface. Implementations will wrap the memory stores they represent by
 * defining the methods enumerated here.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
interface MemoryStore
{
   /**
    * Returns the value for the specified key from the memory store.
    *
    * @param string $key
    *
    * @return mixed
    */
   public function getKey(string $key);

   /**
    * Sets a value for the specified key in the memory store. If the key is already defined in the memory store, its
    * value will be overwritten.
    *
    * @param string $key
    * @param mixed  $value
    *
    * @return void
    */
   public function setKey(string $key, $value);
}
