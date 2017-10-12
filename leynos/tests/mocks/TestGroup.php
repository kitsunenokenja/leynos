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

use kitsunenokenja\leynos\route\{Group, Route};

/**
 * TestGroup
 *
 * Sample routing group for testing.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class TestGroup extends Group
{
   /**
    * Defines test routes for this routing group.
    */
   public function __construct()
   {
      $Route = new Route("true", [TrueController::class]);
      $Route->setTemplate("test");
      $Route->addAlias("true_alias");
      $this->addRoute($Route);

      $Route = new Route("true", [TrueController::class]);
      $Route->setRequestMethod(Route::POST);
      $Route->setTemplate("test");
      $this->addRoute($Route);

      $Route = new Route("false", [FalseController::class]);
      $Route->setFailureRoute("/test/error");
      $Route->setTemplate("test");
      $this->addRoute($Route);

      $Route = new Route(Route::DEFAULT_ROUTE, [TrueController::class]);
      $Route->setTemplate("test");
      $this->addRoute($Route);

      $Route = new Route("auth", [TrueController::class]);
      $Route->setPermissionToken("TEST_TOKEN");
      $Route->setTemplate("test");
      $this->addRoute($Route);

      $Route = new Route("mapped", []);
      $Route->addInput("key1", true);
      $Route->addInput("key2", false);
      $Route->setOutputMap([
         'key1' => "existing_key",
         'key3' => "non_existent_key"
      ]);
      $this->addRoute($Route);

      $Route = new Route("error", [TrueController::class]);
      $Route->setTemplate("test");
      $this->addRoute($Route);

      $Route = new Route("exception", [ErrorController::class]);
      $Route->setTemplate("test");
      $this->addRoute($Route);
   }
}
