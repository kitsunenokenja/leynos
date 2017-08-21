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

/**
 * This procedural code simply defines the autoloader logic for PHP Unit to run.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */

spl_autoload_register(
   function($class)
   {
      $file = str_replace(['\\', "kitsunenokenja/leynos"], ['/', "../"], $class) . ".php";
      if(is_file(__DIR__ . "/$file"))
      {
         require_once __DIR__ . "/$file";
      }
   }
);
