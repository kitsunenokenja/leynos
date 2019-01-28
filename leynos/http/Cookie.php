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

namespace kitsunenokenja\leynos\http;

/**
 * Cookie
 *
 * Container for a cookie's name and value pair along with optional attributes. Cookies are for conversion into HTTP
 * header content.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Cookie
{
   /**
    * Name of the cookie.
    *
    * @var string
    */
   private $_name;

   /**
    * Value to be stored in the cookie.
    *
    * @var string
    */
   private $_value;

   /**
    * Unix timestamp of the expiry date. 0 is defined as expiring at the end of session.
    *
    * @var int
    */
   private $_expiry = 0;

   /**
    * Server path for which the cookie is valid.
    *
    * @var string
    */
   private $_path;

   /**
    * Domain or subdomain for which the cookie is valid.
    *
    * @var string
    */
   private $_domain;

   /**
    * Indicates whether the cookie is only available over HTTPS.
    *
    * @var bool
    */
   private $_secure = false;

   /**
    * Indicates whether the cookie is only accessible through HTTP i.e. unavailable to browser scripting.
    *
    * @var bool
    */
   private $_http_only = false;

   /**
    * Creates a new Cookie object.
    *
    * @param string $name
    */
   public function __construct(string $name)
   {
      $this->_name = $name;
   }

   /**
    * Returns the name.
    *
    * @return string
    */
   public function getName(): string
   {
      return $this->_name;
   }

   /**
    * Returns the value.
    *
    * @return string
    */
   public function getValue(): ?string
   {
      return $this->_value;
   }

   /**
    * Sets the value.
    *
    * @param string $value
    */
   public function setValue(string $value): void
   {
      $this->_value = $value;
   }

   /**
    * Returns the expiry as a Unix timestamp.
    *
    * @return int
    */
   public function getExpiry(): int
   {
      return $this->_expiry;
   }

   /**
    * Sets the expiry.
    *
    * @param int $expiry Time in Unix timestamp format.
    */
   public function setExpiry(int $expiry): void
   {
      $this->_expiry = $expiry;
   }

   /**
    * Returns the path.
    *
    * @return string
    */
   public function getPath(): ?string
   {
      return $this->_path;
   }

   /**
    * Sets the path.
    *
    * @param string $path
    */
   public function setPath(string $path): void
   {
      $this->_path = $path;
   }

   /**
    * Returns the domain.
    *
    * @return string
    */
   public function getDomain(): ?string
   {
      return $this->_domain;
   }

   /**
    * Sets the domain.
    *
    * @param string $domain
    */
   public function setDomain(string $domain): void
   {
      $this->_domain = $domain;
   }

   /**
    * Returns the flag indicating if the cookie is only available via HTTPS.
    *
    * @return bool
    */
   public function getSecure(): bool
   {
      return $this->_secure;
   }

   /**
    * Sets the flag indicating if the cookie is only available via HTTPS.
    *
    * @param bool $secure
    */
   public function setSecure(bool $secure): void
   {
      $this->_secure = $secure;
   }

   /**
    * Returns the flag indicating if the cookie is only accessible via HTTP.
    *
    * @return bool
    */
   public function getHTTPOnly(): bool
   {
      return $this->_http_only;
   }

   /**
    * Sets the flag indicating if the cookie is only accessible via HTTP.
    *
    * @param bool $http_only
    */
   public function setHTTPOnly(bool $http_only): void
   {
      $this->_http_only = $http_only;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
