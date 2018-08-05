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

use kitsunenokenja\leynos\config\groups\SampleGroup;
use kitsunenokenja\leynos\memory_store\{MemoryStore, Redis, Session};
use predis\Client as RedisClient;

/**
 * SampleConfiguration
 *
 * Sample framework configuration class. This is an example configuration file to demonstrate how to use the various
 * options defined by the Config class in order to configure an application to run with Leynos.
 *
 * For further information, refer to the base class.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class SampleConfiguration extends Config
{
   /**
    * Sets the framework configuration options and defines the application routing map.
    */
   public function __construct()
   {
      /*
       * The Options object specifies a collection of miscellaneous options for the framework, such as whether a
       * database connection should be opened by default upon each execution. Call any of the setter methods here to
       * establish the default settings for these options.
       */
      $this->_Options = new Options();

      // Set the default timezone and locale in which PHP will run here. This will take precedence over php.ini
      // defaults. Refer to the PHP manual for further information about values for timezones and locales.
      $this->_timezone = "UTC";
      $this->_locale = "en_US.UTF-8";

      /*
       * The routing map is an array of class names which must all be Group classes. This array defines which groups are
       * available and defined for the application. The individual groups themselves define all routes specific to them
       * within their respective classes. In this example, only one group is defined.
       *
       * Define all routing groups with this array keyed by route group names and values referring to group classes. For
       * example, if the sample group class defines a route named index, and the key here is sample_group, the path to
       * that route would be /sample_group/index.
       */
      $this->_routing_map = ['sample_group' => SampleGroup::class];

      // For applications that will use the session required feature, a route must be defined for login redirects. The
      // alternative is to disable the feature and use application-specific controllers to resolve the request.
      $this->_Options->setLoginRoute("/authentication/login");

      /*
       * Autoloaders is an array of anonymous functions which will all be incorporated into the autoloader registration.
       * These should be routines to aide in resolving namespaces of classes in order to load which class is called upon
       * in the application. Other PHP libraries can have autoloading routines added here as well.
       */
      $this->_Autoloaders = [
         // General logic for non-namespace libs in include path such as Twig
         function($class)
         {
            if(stream_resolve_include_path($file = str_replace(['_', '\0'], ['/', ''], $class) . ".php"))
            {
               /** @noinspection PhpIncludeInspection */
               require $file;
            }
         },
         // Redis
         function($class)
         {
            $class = str_replace('\\', '/', $class);
            $class = preg_replace('/^[Pp]redis/', "predis/src", $class);
            if(stream_resolve_include_path($file = "$class.php"))
            {
               /** @noinspection PhpIncludeInspection */
               require $file;
            }
         },
         // Spout
         function($class)
         {
            if(stream_resolve_include_path($file = str_replace(['\\', "Box/Spout"], ['/', "Spout"], $class) . ".php"))
            {
               /** @noinspection PhpIncludeInspection */
               require $file;
            }
         }
      ];

      // PDO settings array contains a series of alias-keyed credentials objects. The shorthand alias in controllers
      // corresponds to the key 'default' if defined.
      $this->_PDOSettings = ['default' => new PDOSettings("mysql:host=localhost;dbname=db", "username", "password")];

      /*
       * The cache namespace serves as an automatic prefix for all keys for memory stores except for Session. This
       * facilitates creating parallel versions of configurations for multiple environments, without creating key
       * collisions in the memory stores, ensuring memory is segregated by environment in case there are multiple
       * environments on the same PHP server.
       */
      $this->_cache_namespace = "sample_prefix";
   }

   /**
    * {@inheritdoc}
    */
   public function getMemoryStore(): MemoryStore
   {
      return new Redis(new RedisClient());
   }

   /**
    * {@inheritdoc}
    */
   public function isAuthenticated(Session $Session): bool
   {
      /*
       * The application can represent authentication status within session in any way, and is not restricted to storing
       * a primitive value as demonstrated here. For example, retrieving a data structure representing a user's session
       * and calling a function of that object that will return the actual result.
       */
      return $Session->getKey("is_authenticated") === true;
   }

   /**
    * {@inheritdoc}
    */
   public function setUserStoreNamespace(Session $Session): void
   {
      // In the same manner as the authenticated method, any arbitrary data and logic can be applied to resolve the
      // value for the user store namespace prefix.
      $this->_user_store_namespace = (string) $Session->getKey("user_id");
   }
}
