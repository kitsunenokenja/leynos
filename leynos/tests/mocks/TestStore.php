<?php
/**
 * Copyright (c) 2017.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\tests\mocks;

use kitsunenokenja\leynos\memory_store\MemoryStore;

/**
 * TestStore
 *
 * Memory store for testing.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class TestStore extends MemoryStore
{
   private array $_data = [];

   /**
    * {@inheritdoc}
    */
   public function getKey(string $key)
   {
      return $this->_data[$key] ?? null;
   }

   /**
    * {@inheritdoc}
    */
   public function setKey(string $key, $value): void
   {
      $this->_data[$key] = $value;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
