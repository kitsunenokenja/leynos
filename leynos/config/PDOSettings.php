<?php
/**
 * Copyright (c) 2017.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\config;

use PDO;
use PDOException;

/**
 * PDOSettings
 *
 * A straightforward container that captures instantiation options for PDO. This tiny wrapper allows deferring the
 * actual instantiation of PDO until it's needed, rather than opening a live connection on every execution.
 *
 * This class does not extend or replace PDO.
 */
class PDOSettings
{
   /**
    * Data source name connection string.
    *
    * @var string
    */
   private string $_dsn;

   /**
    * Username for DB credentials.
    *
    * @var string
    */
   private string $_username;

   /**
    * Password for DB credentials.
    *
    * @var string
    */
   private string $_password;

   /**
    * Optional parameters for the DB connection.
    *
    * @var array
    */
   private array $_options;

   /**
    * Attribute/value pairs to be applied to the PDO instance.
    *
    * @var array
    */
   private array $_attributes = [];

   /**
    * Creates a PDO settings object storing the parameters for PDO for later use.
    *
    * @param string $dsn      Data source name connection string for PDO.
    * @param string $username Username to use for the PDO connection.
    * @param string $password Password to use for the PDO connection.
    * @param array  $options  Options for the PDO connection where applicable.
    *
    * @see https://secure.php.net/manual/en/pdo.construct.php
    */
   public function __construct(string $dsn, string $username = null, string $password = null, array $options = [])
   {
      $this->_dsn = $dsn;
      $this->_username = $username;
      $this->_password = $password;
      $this->_options = $options;
   }

   /**
    * Returns a PDO instance using the settings within.
    *
    * @return PDO
    *
    * @throws PDOException Thrown if the connection cannot be established.
    */
   public function getPDO(): PDO
   {
      $PDO = new PDO($this->_dsn, $this->_username, $this->_password, $this->_options);

      // Exception-based error mode is the framework default
      $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Apply configuration-supplied attribute options. By doing this after the default error mode setting above, the
      // configuration is able to override which error mode to use.
      foreach($this->_attributes as $attribute => $value)
         $PDO->setAttribute($attribute, $value);

      return $PDO;
   }

   /**
    * Store attribute/value pairs to be applied to the PDO instance.
    *
    * @param int   $attribute
    * @param mixed $value
    */
   public function setAttribute(int $attribute, $value): void
   {
      $this->_attributes[$attribute] = $value;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
