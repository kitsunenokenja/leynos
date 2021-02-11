<?php
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

use Exception;
use Predis\Client as RedisClient;

/**
 * Redis
 *
 * Memory store wrapper for the Redis client.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Redis extends MemoryStore
{
   /**
    * Reference to the Redis client.
    *
    * @var RedisClient
    */
   private RedisClient $_Redis;

   /**
    * Connects to the Redis store.
    *
    * @param RedisClient $Redis
    */
   public function __construct(RedisClient $Redis)
   {
      $this->_Redis = $Redis;
   }

   /**
    * {@inheritdoc}
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
   public function setKey(string $key, $value): void
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

# vim: set ts=3 sw=3 tw=120 et :
