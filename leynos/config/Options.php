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

namespace kitsunenokenja\leynos\config;

/**
 * Options
 *
 * Simple data structure storing options for the framework to consider. These options are effectively inherited through
 * the route group to the individual route so any given group or route may override any or all options defined globally.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Options
{
   const SESSION_REQUIRED       = 0;
   const ENABLE_TEMPLATE_ENGINE = 1;
   const ENABLE_ROUTING_CACHE   = 2;

   /**
    * Determines if the current execution requires the user to be logged in with a valid session to proceed. Users not
    * logged in requesting routes that require a session will fallback upon the designated login route.
    *
    * @var bool
    */
   private $_session_required = true;

   /**
    * The designated login route for redirecting an unauthenticated user from a route that has session required enabled.
    *
    * @var string
    */
   private $_login_route = null;

   /**
    * Determines whether a template engine should be initialised for usage within controllers. Some controllers require
    * access to template rendering engines like Twig to process string output more cleanly to refrain from generating
    * markup or other lengthy strings directly inline.
    *
    * This feature is rarely used and is disabled by default.
    *
    * @var bool
    */
   private $_enable_template_engine = false;

   /**
    * Determines whether the framework will use the memory store to cache routing group configurations. Generally this
    * should remain enabled for best performance. Disabling this option is ideal for development and testing
    * environments which would otherwise require constant cache flushing without this off.
    *
    * @var bool
    */
   private $_enable_routing_cache = true;

   /**
    * Returns the required session flag.
    *
    * @return bool
    */
   public function getSessionRequired(): bool
   {
      return $this->_session_required;
   }

   /**
    * Sets the required session flag.
    *
    * @param bool $session_required
    */
   public function setSessionRequired(bool $session_required): void
   {
      $this->_session_required = $session_required;
   }

   /**
    * Returns the login route.
    *
    * @return string
    */
   public function getLoginRoute(): ?string
   {
      return $this->_login_route;
   }

   /**
    * Sets the login route.
    *
    * @param string $login_route
    */
   public function setLoginRoute(string $login_route): void
   {
      $this->_login_route = $login_route;
   }

   /**
    * Returns the enable template engine flag.
    *
    * @return bool
    */
   public function getEnableTemplateEngine(): bool
   {
      return $this->_enable_template_engine;
   }

   /**
    * Sets the enable template engine flag.
    *
    * @param bool $enable_template_engine
    */
   public function setEnableTemplateEngine(bool $enable_template_engine): void
   {
      $this->_enable_template_engine = $enable_template_engine;
   }

   /**
    * Returns the enable routing cache flag.
    *
    * @return bool
    */
   public function getEnableRoutingCache(): bool
   {
      return $this->_enable_routing_cache;
   }

   /**
    * Sets the enable routing cache flag.
    *
    * @param bool $enable_routing_cache
    */
   public function setEnableRoutingCache(bool $enable_routing_cache): void
   {
      $this->_enable_routing_cache = $enable_routing_cache;
   }
}
