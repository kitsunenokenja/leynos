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

namespace kitsunenokenja\leynos\config\groups;

use kitsunenokenja\leynos\config\Options;
use kitsunenokenja\leynos\route\{Group, Route};

/**
 * SampleGroup
 *
 * Demonstration of a routing group definition. For further details & options, see the Route & Group classes.
 *
 * This file is for reference purposes only, and, intentionally, is invalid code. Do not actually configure this group
 * for the application.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class SampleGroup extends Group
{
   /**
    * Defines the route group.
    *
    * These examples are based on framework defaults and may not be suitable for a given configuration, such as
    * references to Twig template files and formatting of routing destinations. Only the short syntax for slices is
    * shown here.
    *
    * Also note that no special designations are used for specific request modes. Any route could legitimately be
    * requested for a JSON response rather than HTML, for instance. However, request method is segregated. In other
    * words, a route for a GET action and a route with an identical name for a POST action are two uniquely different
    * routes. In the case of actual route naming collision, the most recent declaration prevails.
    */
   public function __construct()
   {
      // Example of setting an override for a routing group. See the Options class for a list of available overrides.
      // In this example the group is defined with all routes not requiring an authenticated session to execute.
      $this->_overrides[Options::SESSION_REQUIRED] = false;

      // The most basic example. This route has no controllers to execute, and simply defines a template file to be
      // rendered.
      $Route = new Route("index", [
         Slice::new()->
            exitStateMap([
               new ExitState(ExitState::SUCCESS, Route::RENDER, "index.twig")
            ])
      ]);
      $this->addRoute($Route);

      /*
       * Example of the request method call to designate the route as a POST handler rather than the default GET.
       * A single slice is defined here for the controller chain as a minimal example where an input array is fed for
       * processing and the PRG pattern is applied to conclude processing.
       */
      $Route = new Route("post_action", [
         Slice::new(PostHandler::class)->
            storeInputMap([
               MemoryStore::REQUEST => ["form_data"]
            ])->
            exitStateMap([
               new ExitState(ExitState::SUCCESS, Route::REDIRECT, "/index/index"),
               new ExitState(ExitState::FAILURE, Route::REDIRECT, "/error/error")
            ])
      ]);
      $Route->setRequestMethod(Route::POST);
      $this->addRoute($Route);

      // This route demonstrates supplying additional inputs for the controllers, which will also be available at the
      // template level under an alias when the view renders, numerous exit state handlers, and setting a custom option.
      $Route = new Route("another_route", [
         Slice::new(DataLoader::class)->
            inputMap([
               "special_value" => "custom"
            ])->
            outputMap([
               "special_value" => "value"
            ])->
            exitStateMap([
               new ExitState(ExitState::SUCCESS, Route::RENDER, "another_route.twig"),
               new ExitState(ExitState::FAILURE, Route::REDIRECT, "/error/generic_error"),
               new ExitState(ExitState::DATABASE_FAILURE, Route::REDIRECT, "/error/db_error")
            ])
      ]);
      $Route->setSessionRequired(true);
      $this->addRoute($Route);
   }
}

# vim: set ts=3 sw=3 tw=120 et :
