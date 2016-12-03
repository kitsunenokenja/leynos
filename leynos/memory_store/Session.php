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
 * Session
 *
 * This memory store wraps PHP's $_SESSION super-global.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Session extends MemoryStore
{
   /**
    * Session constructor.
    *
    * @throws MemoryStoreException
    */
   public function __construct()
   {
      $this->open();
   }

   /**
    * Ensures the closure of the session.
    */
   public function __destruct()
   {
      $this->close();
   }

   /**
    * Resumes the session. This should only be performed prior to additional writing.
    *
    * @throws MemoryStoreException
    */
   public function open(): void
   {
      if(session_status() === PHP_SESSION_NONE && !session_start())
      {
         throw new MemoryStoreException("Session failed to start");
      }
   }

   /**
    * Commits the session.
    */
   public function close(): void
   {
      if(session_status() === PHP_SESSION_ACTIVE)
      {
         session_write_close();
      }
   }

   /**
    * Destroys the session.
    */
   public function destroy(): void
   {
      $this->open();
      $_SESSION = [];
      session_destroy();
   }

   /**
    * Regenerates the session ID. This should not be called unless a new login has just been authenticated.
    *
    * @throws MemoryStoreException
    */
   public function regenerate(): void
   {
      if(!session_regenerate_id(true))
         throw new MemoryStoreException("Failed to regenerate session ID");
   }

   /**
    * Returns the name of the session.
    *
    * @return string
    */
   public function getName(): string
   {
      return session_name();
   }

   /**
    * {@inheritdoc}
    */
   public function getKey(string $key)
   {
      return $_SESSION[$key] ?? null;
   }

   /**
    * {@inheritdoc}
    *
    * @throws MemoryStoreException
    */
   public function setKey(string $key, $value): void
   {
      if(session_status() !== PHP_SESSION_ACTIVE)
         throw new MemoryStoreException("Attempting to write to inactive session!");

      $_SESSION[$key] = $value;
   }
}
