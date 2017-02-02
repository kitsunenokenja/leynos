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

use Closure;
use kitsunenokenja\leynos\memory_store\{MemoryStore, Redis, Session};
use kitsunenokenja\leynos\view\{TemplateView, TwigView};

/**
 * Config
 *
 * The abstract default configuration class. Framework configurations must be extensions of this class. Config supplies
 * the Kernel with parameters with which to start up.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
abstract class Config
{
   /**
    * Global options for the framework. Routing groups and routes can override any or all of the options within.
    *
    * @var Options
    */
   protected $_Options;

   /**
    * Closures to register autoloaders. The application's and 3rd party library autoloader functions should be set.
    *
    * @var Closure[]
    */
   protected $_Autoloaders = [];

   /**
    * The timezone string for the PHP environment.
    *
    * @var string
    */
   protected $_timezone = "UTC";

   /**
    * The locale string for the PHP environment.
    *
    * @var string
    */
   protected $_locale = "en_US.UTF-8";

   /**
    * The routing pattern to be enforced by the framework and to identify a valid request for resolving which action to
    * execute.
    *
    * The default pattern follows the pattern:
    *
    *    /group/action/format?query_string...
    *
    * where the trailing /format is optional as it defaults to standard HTML view when omitted, and the character sets
    * for all matching groups captures standard ASCII as defined by PCRE's \w set. The purpose of allowing this pattern
    * to be customised is to provide the flexibility to define a pattern suitable for the intended application, and to
    * allow other desired characters to match such as Unicode.
    *
    * Customisation of this pattern must observe the following two rules:
    *    1) Two capture groups must be defined to acknowledge the group and action names within the request.
    *    2) A third capture group must be defined that identifies the type of view being requested. Although this
    *       parameter is optional, as it is in the default, it is not necessary to be defined as optional, but doing so
    *       is recommended.
    *
    * Not defining the third set as optional will cause the application to require defining the requested view format
    * explicitly for all requests. In addition, while not strictly required for the framework to operate, it is highly
    * recommended to preserve the query string check at the end as included in the default pattern. Failure to preserve
    * this portion of the pattern will cause all requests using a query string to fail and likely break the application.
    *
    * @var string
    */
   protected $_routing_pattern = '#^/(\w+)/(\w+)(?:/(\w+))?(?:\?.*)?$#';

   /**
    * Class name of the memory store implementation for the framework to use. The default is Redis to ensure a sane
    * default configuration.
    *
    * @see MemoryStore
    *
    * @var string
    */
   protected $_memory_store_class = Redis::class;

   /**
    * Class name of the template view implementation for the framework to use to render templates. The default Twig view
    * is set to ensure a functional view layer out of the box.
    *
    * @see TemplateView
    *
    * @var string
    */
   protected $_template_engine_class = TwigView::class;

   /**
    * The string token to use as the namespace for cache keys. This allows multiple environments on the same server to
    * use the same memory store services without collisions.
    *
    * @var string
    */
   protected $_cache_namespace = "";

   /**
    * Settings for opening DB connections. Keys of this array serve as aliases for each set of settings. The key
    * 'default' will be used for the shorthand alias in the controllers.
    *
    * @var PDOSettings[]
    */
   protected $_PDOSettings = [];

   /**
    * This array contains all the routing groups that define the application.
    *
    * @var string[]
    */
   protected $_routing_map = [];

   /**
    * Returns the framework options.
    *
    * @return Options
    */
   final public function getOptions(): Options
   {
      return $this->_Options;
   }

   /**
    * Returns the autoloaders.
    *
    * @return Closure[]
    */
   final public function getAutoloaders(): array
   {
      return $this->_Autoloaders;
   }

   /**
    * Returns the timezone string.
    *
    * @return string
    */
   final public function getTimezone(): string
   {
      return $this->_timezone;
   }

   /**
    * Returns the locale string.
    *
    * @return string
    */
   final public function getLocale(): string
   {
      return $this->_locale;
   }

   /**
    * Returns the routing pattern string.
    *
    * @return string
    */
   final public function getRoutingPattern(): string
   {
      return $this->_routing_pattern;
   }

   /**
    * Returns the memory store of the configured type.
    *
    * @return MemoryStore
    */
   final public function getMemoryStore(): MemoryStore
   {
      $class = $this->_memory_store_class;
      return new $class();
   }

   /**
    * Returns the template engine of the configured type.
    *
    * @param string $document_root
    *
    * @return TemplateView
    */
   final public function getTemplateEngine(string $document_root): TemplateView
   {
      $class = $this->_template_engine_class;
      return new $class($document_root);
   }

   /**
    * Returns the cache namespace.
    *
    * @return string
    */
   final public function getCacheNamespace(): string
   {
      return $this->_cache_namespace;
   }

   /**
    * Returns the PDO settings.
    *
    * @return PDOSettings[]
    */
   final public function getPDOSettings(): array
   {
      return $this->_PDOSettings;
   }

   /**
    * Returns the routing group from the map.
    *
    * @param string $group
    *
    * @return string|null
    */
   final public function getRoutingGroup(string $group): ?string
   {
      return $this->_routing_map[$group] ?? null;
   }

   /**
    * Returns whether the user is authenticated.
    *
    * @param Session $Session
    *
    * @return bool
    */
   abstract public function isAuthenticated(Session $Session): bool;
}
