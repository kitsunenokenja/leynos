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
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class SampleGroup extends Group
{
    /**
     * Defines the route group.
     *
     * This is a demo group only. Do not actually configure this group for the application. This file exists to
     * illustrate how routes are defined for groups. Note that to avoid including invalid code in this file, all
     * controller arrays are empty. Use class names to enumerate controller chains.
     *
     * e.g. $Route = new Route("three_controller_chain", [StepA::class, StepB::class, StepC::class]);
     *
     * These examples are based on framework defaults and may not be suitable for a given configuration, such as
     * references to Twig template files and formatting of routing destinations.
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
        $Route = new Route("index", []);
        $Route->setTemplate("index.twig");
        $this->addRoute($Route);

        // Example of the request method call to designate the route as a POST handler rather than the default GET.
        // Ideally, at least one controller would be defined here because it should be processing a POST submission.
        $Route = new Route("post_action", []);
        $Route->setRequestMethod(Route::POST);
        $this->addRoute($Route);

        // Redirection example. Either successful or failure redirection can be set, or both can be set. This is very
        // useful for building PRG patterns.
        $Route = new Route("redirect", []);
        $Route->setRedirectRoute("/group/successful_route");
        $Route->setFailureRoute("/group/failure_route");
        $this->addRoute($Route);

        // This route demonstrates supplying additional inputs for the controllers, which will also be available at the
        // template level when the view renders. Note the route-specific option overriding the group override.
        $Route = new Route("another_route", []);
        $Route->addInput("special_value", "custom");
        $Route->setTemplate("another_route.twig");
        $Route->setSessionRequired(true);
        $this->addRoute($Route);
    }
}
