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

namespace kitsunenokenja\leynos\route;

/**
 * Group
 *
 * Routing groups contain a series of related routes.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
abstract class Group
{
   use OptionsOverrides;

   /**
    * The array of routes that consist of the group.
    *
    * @var Route[][]
    */
   protected $_Routes = [Route::GET => [], Route::POST => []];

   /**
    * Adds a route to the group by its name.
    *
    * @param Route $Route
    */
   final public function addRoute(Route $Route)
   {
      $this->_Routes[$Route->getRequestMethod()][$Route->getName()] = $Route;
   }

   /**
    * Returns the route by name.
    *
    * @param string $route
    * @param int    $method
    *
    * @return Route|null
    */
   final public function getRoute(string $route, int $method = Route::GET)
   {
      return $this->_Routes[$method][$route] ?? null;
   }

   /**
    * The array of global controllers is a series of controllers that always execute prior to a specific route's series
    * of controllers for any route within the group.
    *
    * @return string[]
    */
   public function globalControllers(): array
   {
      return [];
   }
}
