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

namespace kitsunenokenja\leynos\http;

/**
 * Request
 *
 * This class captures the requested route and sanitises the $_REQUEST super-global.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Request
{
   /**
    * The group portion of the request identifying which route group to look up.
    *
    * @var string
    */
   private $_group;

   /**
    * The route action portion of the request identifying which action of a set of routes to invoke.
    *
    * @var string
    */
   private $_route;

   /**
    * Internal sanitised storage for all HTTP request parameters.
    *
    * @var array
    */
   private $_data = [];

   /**
    * The constructor parses all data from $_REQUEST and filters out undesirable strings to mitigate XSS/injections.
    *
    * @param string $group
    * @param string $route
    */
   public function __construct(string $group, string $route)
   {
      $this->_group = $group;
      $this->_route = $route;

      // Copy the request contents to internal storage. Although the super-global would always exist, subsequent calls
      // to this routine will have it unset due to internal re-routing triggering this process again.
      $this->_data = $_REQUEST ?? [];

      // Free memory now. These super-globals aren't needed hereafter, and this avoids duplicating request in memory.
      unset($_GET, $_POST, $_REQUEST);

      // Clean up all inbound array keys
      $this->_sanitizeKeys($this->_data);

      // Clean up the values
      $this->_sanitizeValues($this->_data);
   }

   /**
    * Magic getter.
    *
    * @param string $name
    *
    * @return mixed|null
    */
   public function __get(string $name)
   {
      return isset($this->_data[$name]) ? $this->_data[$name] : null;
   }

   /**
    * Magic isset.
    *
    * @param string $name
    *
    * @return bool
    */
   public function __isset(string $name): bool
   {
      return isset($this->_data[$name]);
   }

   /**
    * Returns the requested route group name.
    *
    * @return string
    */
   public function getGroup(): string
   {
      return $this->_group;
   }

   /**
    * Returns the requested route name.
    *
    * @return string
    */
   public function getRoute(): string
   {
      return $this->_route;
   }

   /**
    * Recursive function that expunges injected tags and all non-alphanumeric non-underscore characters from all keys,
    * including all nested arrays.
    *
    * @param array $array
    */
   private function _sanitizeKeys(array &$array): void
   {
      foreach($array as $k => $v)
      {
         if(is_array($v))
         {
            $this->_sanitizeKeys($v);
         }
         else
         {
            // Purge undesirable characters
            $k_safe = preg_replace('/[^\w\d]/', "", $k);
            $array[$k_safe] = $v;
            if($k_safe !== $k)
               unset($array[$k]);
         }
      }
   }

   /**
    * Recursive function that sanitises all values of the array, including all nested arrays. XSS is mitigated here.
    *
    * @param array $array
    */
   private function _sanitizeValues(array &$array): void
   {
      foreach($array as $k => $v)
      {
         if(is_array($v))
         {
            $this->_sanitizeValues($v);
         }
         else
         {
            // Strip HTML escapes and aggressively filter XSS attempts by repeatedly stripping tags until none remain
            for(
               $array[$k] = strip_tags(htmlspecialchars_decode($v));
               $array[$k] !== strip_tags($array[$k]);
               $array[$k] = strip_tags($array[$k])
            );
         }
      }
   }
}
