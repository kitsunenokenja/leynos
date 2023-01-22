<?php declare(strict_types=1);
/**
 * Copyright (c) 2016.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

/**
 * This procedural code is the bootstrap script that launches the framework. All requests that should be handled by this
 * framework should be redirected to this script via the web server daemon.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */

use kitsunenokenja\leynos\config\SampleConfiguration;
use kitsunenokenja\leynos\kernel\Kernel;

// This autoloader registration is intended for the framework. Additional autoloaders should be registered via
// configuration.
spl_autoload_register(
   function($class)
   {
      $file = str_replace('\\', '/', $class) . ".php";
      if(stream_resolve_include_path($file) !== false || is_file($file))
      {
         /** @noinspection PhpIncludeInspection */
         require_once $file;
      }
      elseif(is_file(__DIR__ . "/$file"))
      {
         require_once __DIR__ . "/$file";
      }
   }
);

// Boot the kernel. Replace the sample configuration with the application's genuine configuration instance.
new Kernel(new SampleConfiguration());
