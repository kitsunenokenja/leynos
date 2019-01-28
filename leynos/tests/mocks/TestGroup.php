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

use kitsunenokenja\leynos\controller\{ExitState, Slice};
use kitsunenokenja\leynos\memory_store\MemoryStore;
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
      // Basic successful route with a single output. This route also defines an alias.
      $Route = new Route("true", [
         Slice::new(SuccessController::class)->
            outputMap(['output' => "output"])->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $Route->addAlias("true_alias");
      $this->addRoute($Route);

      // Basic route for testing POST requests
      $Route = new Route("true", [
         Slice::new(SuccessController::class)->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $Route->setRequestMethod(Route::POST);
      $this->addRoute($Route);

      // Basic failure route
      $Route = new Route("false", [
         Slice::new(FailureController::class)->
            exitStateMap([new ExitState(ExitState::FAILURE, ExitState::REDIRECT, "/test/error")])
      ]);
      $this->addRoute($Route);

      // Basic successful default route
      $Route = new Route(Route::DEFAULT_ROUTE, [
         Slice::new(SuccessController::class)->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $this->addRoute($Route);

      // Minimal rewrite routing
      $Route = new Route("rewrite", [
         Slice::new()->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::REWRITE, "/test/rewrite_io")])
      ]);
      $Route->addInput("input", "success");
      $this->addRoute($Route);
      $Route = new Route("rewrite_io", [
         Slice::new(IOController::class)->
            outputMap(['output' => "output"])->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $this->addRoute($Route);

      // Basic I/O test
      $Route = new Route("input_map_test", [
         Slice::new(IOController::class)->
            inputMap(['input' => "value"])->
            outputMap(['output' => "output"])->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $this->addRoute($Route);

      // Store map test to channel an input to a store then pipe it to the next slice for output
      $Route = new Route("store_map_test", [
         Slice::new(IOController::class)->
            inputMap(['input' => "value"])->
            storeOutputMap([MemoryStore::GLOBAL_STORE => ["output"]]),
         Slice::new(IOController::class)->
            storeInputMap([MemoryStore::GLOBAL_STORE => [['output' => "input"]]])->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $this->addRoute($Route);

      // Check output mapping via aliasing
      $Route = new Route("output_map_test", [
         Slice::new(IOController::class)->
            inputMap(['input' => "value"])->
            outputMap(['output' => "aliased_output"])->
            storeOutputMap([MemoryStore::GLOBAL_STORE => [['aliased_output' => "output"]]]),
         Slice::new(IOController::class)->
            storeInputMap([MemoryStore::GLOBAL_STORE => [['output' => "input"]]])->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $this->addRoute($Route);

      // Successful chain of 3 slices
      $Route = new Route("success-chain", [
         Slice::new(SuccessController::class),
         Slice::new(SuccessController::class),
         Slice::new(SuccessController::class)->
            outputMap(['output' => "output"])->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER)])
      ]);
      $this->addRoute($Route);

      // Failing chain of 3 slices
      $Route = new Route("failure-chain", [
         Slice::new(SuccessController::class),
         Slice::new(FailureController::class)->
            exitStateMap([new ExitState(ExitState::FAILURE, ExitState::REDIRECT, "/test/error")]),
         Slice::new(SuccessController::class)->
            outputMap(['output' => "output"])->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER)])
      ]);
      $this->addRoute($Route);

      // Auth route for testing granted access via permission token enforcement
      $Route = new Route("auth", [
         Slice::new(SuccessController::class)->
            outputMap(['output' => "output"])->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $Route->setPermissionToken("TEST_TOKEN");
      $this->addRoute($Route);

      // Auth route for testing denied access via permission token enforcement
      $Route = new Route("invalid_auth", [
         Slice::new(SuccessController::class)->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $Route->setPermissionToken("UNKNOWN_TOKEN");
      $this->addRoute($Route);

      // Complex route using the zero-controller slice and I/O mapping
      $Route = new Route("mapped", [
         Slice::new()->
            inputMap(['key1' => true, 'key2' => false])->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $Route->setOutputMap(['key1' => "existing_key", 'key3' => "non_existent_key"]);
      $this->addRoute($Route);

      // Simple route for error-based tests to target and gracefully exit
      $Route = new Route("error", [
         Slice::new(SuccessController::class)->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $this->addRoute($Route);

      // Route calling a faulty controller that throws exception for further error testing
      $Route = new Route("exception", [
         Slice::new(ErrorController::class)->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER, "test")])
      ]);
      $this->addRoute($Route);

      // Route that yields a message rendering in JSON response
      $Route = new Route("message", [
         Slice::new(MessageController::class)->
            exitStateMap([new ExitState(ExitState::SUCCESS, ExitState::RENDER)])
      ]);
      $this->addRoute($Route);
   }
}

# vim: set ts=3 sw=3 tw=120 et :
