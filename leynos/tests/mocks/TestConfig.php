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

namespace kitsunenokenja\leynos\tests\mocks;

use kitsunenokenja\leynos\config\{Config, Options};
use kitsunenokenja\leynos\memory_store\MemoryStore;
use kitsunenokenja\leynos\memory_store\Session;

/**
 * TestConfig
 *
 * Configuration with sample entries for testing purposes only.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class TestConfig extends Config
{
   /**
    * Mock authentication flag for testing.
    *
    * @var bool
    */
   private $_auth = true;

   /**
    * Creates a new config instance.
    */
   public function __construct()
   {
      $this->_root_index_route = "/test/true";
      $this->_error_route = "/test/error";
      $this->_routing_map = ['test' => TestGroup::class];
      $this->_template_engine_class = TestView::class;
      $this->_Options = new Options();
   }

   /**
    * Sets the error route.
    *
    * @param string $error_route
    */
   public function setErrorRoute(string $error_route): void
   {
      $this->_error_route = $error_route;
   }

   /**
    * Sets the mock authentication flag.
    *
    * @param bool $auth
    */
   public function setAuthenticated(bool $auth): void
   {
      $this->_auth = $auth;
   }

   /**
    * {@inheritdoc}
    */
   public function getMemoryStore(): MemoryStore
   {
      return new TestStore();
   }

   /**
    * {@inheritdoc}
    */
   public function isAuthenticated(Session $Session): bool
   {
      return $this->_auth;
   }

   /**
    * {@inheritdoc}
    */
   public function setUserStoreNamespace(Session $Session): void
   {
      return;
   }
}
