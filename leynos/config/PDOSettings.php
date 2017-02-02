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
   private $_dsn;

   /**
    * Username for DB credentials.
    *
    * @var string
    */
   private $_username;

   /**
    * Password for DB credentials.
    *
    * @var string
    */
   private $_password;

   /**
    * Optional parameters for the DB connection.
    *
    * @var array
    */
   private $_options;

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
      $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $PDO;
   }
}
