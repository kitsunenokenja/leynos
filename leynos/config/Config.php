<?php
/**
 * Copyright (c) 2016.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\config;

use Closure;
use kitsunenokenja\leynos\memory_store\{MemoryStore, Session};
use kitsunenokenja\leynos\permission\PermissionSet;
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
   protected Options $_Options;

   /**
    * Closures to register autoloaders. The application's and 3rd party library autoloader functions should be set.
    *
    * @var Closure[]
    */
   protected array $_Autoloaders = [];

   /**
    * The timezone string for the PHP environment.
    *
    * @var string
    */
   protected string $_timezone = "UTC";

   /**
    * The locale string for the PHP environment.
    *
    * @var string
    */
   protected string $_locale = "en_US.UTF-8";

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
   protected string $_routing_pattern = '#^/(\w+)/(\w+)(?:/(\w+))?(?:\?.*)?$#';

   /**
    * The only route that violates the routing pattern yet must be valid is the request for root index i.e. GET /
    * requests. This option allows an implicit re-route to a well-formed route for handling such requests.
    *
    * Using this option is highly recommended if the web server daemon is not performing a rewrite itself to direct root
    * index requests already.
    *
    * @var string
    */
   protected string $_root_index_route = "/main/index";

   /**
    * The error route for re-routing to handle internal failures. This should be a defined route as the default fallback
    * for errors or failures otherwise not handled. This route must be a definitive execution path that cannot throw
    * exceptions to ensure sane responses. An error route that raises yet another exception will bubble up unhandled
    * intentionally as the framework's way to indicate misconfiguration.
    *
    * The exception that triggers re-routing to this error route will be supplied as input to facilitate customised
    * responses based on the nature of the failure that occurred.
    *
    * @var string
    */
   protected string $_error_route = "/error/error";

   /**
    * Class name of the template view implementation for the framework to use to render templates. The default Twig view
    * is set to ensure a functional view layer out of the box.
    *
    * @see TemplateView
    *
    * @var string
    */
   protected string $_template_engine_class = TwigView::class;

   /**
    * The string token to use as the namespace for cache keys. This allows multiple environments on the same server to
    * use the same memory store services without collisions.
    *
    * @var string
    */
   protected string $_cache_namespace = "";

   /**
    * The string token to use as the namespace for user-exclusive keys in the memory store. This allows safe storage of
    * values specific to a user in the general memory store, without being accessible to other users, without polluting
    * session space with those values.
    *
    * If this remains an empty string, then there will be no difference between user and global access to the memory
    * store for keys.
    *
    * @var string
    */
   protected string $_user_store_namespace = "";

   /**
    * Settings for opening DB connections. Keys of this array serve as aliases for each set of settings. The key
    * 'default' will be used for the shorthand alias in the controllers.
    *
    * @var PDOSettings[]
    */
   protected array $_PDOSettings = [];

   /**
    * This array contains all the routing groups that define the application.
    *
    * @var string[]
    */
   protected array $_routing_map = [];

   /**
    * Returns the framework options.
    *
    * @return Options
    */
   final public function getOptions(): ?Options
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
    * Returns the root index route.
    *
    * @return string
    */
   final public function getRootIndexRoute(): string
   {
      return $this->_root_index_route;
   }

   /**
    * Returns the error route string.
    *
    * @return string
    */
   final public function getErrorRoute(): string
   {
      return $this->_error_route;
   }

   /**
    * Returns the memory store that the framework kernel will engage.
    *
    * @return MemoryStore
    */
   abstract public function getMemoryStore(): MemoryStore;

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
    * Returns the user store namespace.
    *
    * @return string
    */
   final public function getUserStoreNamespace(): string
   {
      return $this->_user_store_namespace;
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

   /**
    * Determine a user store namespace using arbitrary data from the session store and set that namespace accordingly.
    * A typical definition of this method would retrieve a unique identifier from session and use it as the namespace
    * prefix.
    *
    * @param Session $Session
    */
   abstract public function setUserStoreNamespace(Session $Session): void;

   /**
    * Returns the set of permissions defined for the user of the current session. The framework expects any established
    * session to be able to yield the permission set for the user in order to enforce route access restrictions.
    *
    * @param Session $Session
    *
    * @return PermissionSet
    */
   abstract public function getPermissionSet(Session $Session): PermissionSet;
}

# vim: set ts=3 sw=3 tw=120 et :
