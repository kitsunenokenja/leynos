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

use UnexpectedValueException;

/**
 * DBCredentials
 *
 * This class simply stores the parameters for a DB connection, not the connection itself. The necessary details for
 * opening a PDO connection are contained by this class.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class DBCredentials
{
   // List of PDO drivers. The driver member must be set to one of these constant values.
   const CUBRID = 0;
   const DB2 = 1;
   const FIREBIRD = 2;
   const INFORMIX = 3;
   const MYSQL = 4; // MariaDB users will also select this driver
   const ODBC = 5; // For ODBC/DB2; refer to documentation regarding selecting this over the DB2 driver.
   const ORACLE = 6;
   const POSTGRES = 7;
   const SQLITE = 8;
   const PDO_4D = 9;

   // The following drivers are for MSSQL/Sybase; refer to the side notes for selecting the appropriate driver.
   const DBLIB = 9;   // For DB using FreeTDS libraries
   const MSSQL = 10;  // For DB using MS SQL libraries (ensure this is intentional to use over the SQLDRV option)!
   const SQLDRV = 12; // For MS SQL Server or SQL Azure DB
   const SYBASE = 11; // For DB using Sybase ct-lib libraries

   /**
    * The DB host to which to connect.
    *
    * @var string
    */
   private $_hostname = "localhost";

   /**
    * The user name for authenticating with the DB.
    *
    * @var string
    */
   private $_username;

   /**
    * The password for authenticating with the DB.
    *
    * @var string
    */
   private $_password;

   /**
    * Name of the database schema to be queried.
    *
    * @var string
    */
   private $_database_schema;

   /**
    * The port number of the host to use for the connection. Port numbers are always numeric but will only be consumed
    * as a string for the DSN, thus it's a string member.
    *
    * @var string
    */
   private $_port;

   /**
    * For database drivers that rely on a file path of the DB itself.
    *
    * @var string
    */
   private $_path;

   /**
    * The PDO driver to use.
    *
    * @var int
    */
   private $_driver;

   /**
    * The DSN (Data Source Name) to be used for instantiating a PDO object. By default this value is empty, and the
    * DSN is formed automatically based on the driver selected. It may be manually defined as a last resort in case
    * the default formation of the DSN is inadequate.
    *
    * @var string
    */
   private $_dsn;

   /**
    * DBCredentials constructor.
    *
    * @param int $driver The PDO driver for these credentials. Refer to the class constants.
    */
   public function __construct(int $driver)
   {
      $this->_driver = $driver;
   }

   /**
    * Returns the host name.
    *
    * @return string
    */
   public function getHostname(): string
   {
      return $this->_hostname;
   }

   /**
    * Sets the host name.
    *
    * @param string $hostname
    */
   public function setHostname(string $hostname): void
   {
      $this->_hostname = $hostname;
   }

   /**
    * Returns the user name.
    *
    * @return string
    */
   public function getUsername(): ?string
   {
      return $this->_username;
   }

   /**
    * Sets the user name.
    *
    * @param string $username
    */
   public function setUsername(string $username): void
   {
      $this->_username = $username;
   }

   /**
    * Returns the password.
    *
    * @return string
    */
   public function getPassword(): ?string
   {
      return $this->_password;
   }

   /**
    * Sets the password.
    *
    * @param string $password
    */
   public function setPassword(string $password): void
   {
      $this->_password = $password;
   }

   /**
    * Returns the database schema.
    *
    * @return string
    */
   public function getDatabaseSchema(): ?string
   {
      return $this->_database_schema;
   }

   /**
    * Sets the database schema.
    *
    * @param string $schema
    */
   public function setDatabaseSchema(string $schema): void
   {
      $this->_database_schema = $schema;
   }

   /**
    * Returns the port.
    *
    * @return string
    */
   public function getPort(): ?string
   {
      return $this->_port;
   }

   /**
    * Sets the port.
    *
    * @param string $port
    */
   public function setPort(string $port): void
   {
      $this->_port = $port;
   }

   /**
    * Sets the file path.
    *
    * @param string $path
    */
   public function setPath(string $path): void
   {
      $this->_path = $path;
   }

   /**
    * Returns the DSN (Data Source Name). This is used to instantiate a PDO object. By default a DSN will be formed
    * based on the selected driver, but it may be manually overridden by calling the DSN setter explicitly for cases
    * where the automated DSN resolution is not sufficient.
    *
    * @return string
    *
    * @throws UnexpectedValueException Thrown if an invalid driver is specified, or if a required parameter for the
    *                                  specified driver is missing.
    */
   public function getDSN(): string
   {
      // If there is a manual DSN defined just return it immediately
      if(!empty($this->_dsn))
         return $this->_dsn;

      // Build and return a DSN based on the driver selection
      switch($this->_driver)
      {
         case self::CUBRID:
            if(empty($this->_database_schema))
               throw new UnexpectedValueException("Missing DB name for Cubrid driver.");

            $dsn = "cubrid:host={$this->_hostname};";

            if(!empty($this->_port))
               $dsn .= "port={$this->_port};";

            return "{$dsn}dbname={$this->_database_schema}";
            break;

         // TODO - Complete DSN generation for other PDO drivers.
         case self::DB2:
            break;

         case self::FIREBIRD:
            break;

         case self::INFORMIX:
            break;

         case self::MYSQL:
            break;

         case self::ODBC:
            break;

         case self::ORACLE:
            break;

         case self::POSTGRES:
            break;

         case self::SQLITE:
            break;

         case self::PDO_4D:
            break;

         case self::DBLIB:
            break;

         case self::MSSQL:
            break;

         case self::SQLDRV:
            break;

         case self::SYBASE:
            break;
      }

      // If the driver is unknown, raise an exception which should emphasise that the configuration is wrong
      throw new UnexpectedValueException("Invalid driver specified for DB credentials!");
   }

   /**
    * Sets the DSN. Only call this setter in case the DSN getter's automatic return value is inadequate.
    *
    * @param string $dsn
    */
   public function setDSN(string $dsn): void
   {
      $this->_dsn = $dsn;
   }
}
