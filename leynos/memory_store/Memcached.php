<?php
/**
 * Copyright (c) 2017.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * http://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\memory_store;

use Memcached as MemcachedClient;

/**
 * Memcached
 *
 * Memory store wrapper for memcached.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Memcached extends MemoryStore
{
   /**
    * Reference to memcached.
    *
    * @var MemcachedClient
    */
   private $_Memcached;

   /**
    * Connects to the Memcached store.
    *
    * @param MemcachedClient $Memcached
    */
   public function __construct(MemcachedClient $Memcached)
   {
      $this->_Memcached = $Memcached;
   }

   /**
    * {@inheritdoc}
    *
    * @throws MemoryStoreException Thrown upon memory store I/O failure.
    */
   public function getKey(string $key)
   {
      if(($value = $this->_Memcached->get($this->_namespace . $key)) === false)
      {
         if($this->_Memcached->getResultCode() !== MemcachedClient::RES_NOTFOUND)
            throw new MemoryStoreException();
         else
            return null;
      }

      return $value !== null ? unserialize($value) : null;
   }

   /**
    * {@inheritdoc}
    *
    * @throws MemoryStoreException Thrown upon memory store I/O failure.
    */
   public function setKey(string $key, $value): void
   {
      if(!$this->_Memcached->set($this->_namespace . $key, serialize($value)))
         throw new MemoryStoreException();
   }
}

# vim: set ts=3 sw=3 tw=120 et :
