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

use Exception;
use Predis\Client;

/**
 * Redis
 *
 * Memory store wrapper for the Redis client.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Redis implements MemoryStore
{
   /**
    * Reference to the Redis client.
    *
    * @var Client
    */
   private $_Redis;

   /**
    * String token to use as the namespace for keys.
    *
    * @var string
    */
   private $_namespace = "";

   /**
    * Connects to the Redis store.
    *
    * @param string $namespace
    */
   public function __construct(string $namespace)
   {
      $this->_Redis = new Client();
      $this->_namespace = $namespace;
   }

   /**
    * {@inheritdoc
    * 
    * @throws MemoryStoreException Thrown upon memory store I/O failure.
    */
   public function getKey(string $key)
   {
      try
      {
         return ($value = $this->_Redis->get($this->_namespace . $key)) !== null ? unserialize($value) : null;
      }
      catch(Exception $E)
      {
         throw new MemoryStoreException($E->getMessage(), $E->getCode(), $E);
      }
   }

   /**
    * {@inheritdoc}
    *
    * @throws MemoryStoreException Thrown upon memory store I/O failure.
    */
   public function setKey(string $key, $value)
   {
      try
      {
         $this->_Redis->set($this->_namespace . $key, serialize($value));
      }
      catch(Exception $E)
      {
         throw new MemoryStoreException($E->getMessage(), $E->getCode(), $E);
      }
   }
}
