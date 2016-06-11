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

/**
 * DBCredentials
 *
 * This class simply stores the parameters for a DB connection, not the connection itself.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class DBCredentials
{
   /**
    * The DB host to which to connect.
    *
    * @var string
    */
   private $_hostname;

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
    * The PDO driver to use.
    *
    * @var string
    */
   private $_driver = "mysql";

   /**
    * DBCredentials constructor.
    *
    * @param string $hostname
    * @param string $username
    * @param string $password
    * @param string $_database_schema
    */
   public function __construct(string $hostname, string $username, string $password, string $_database_schema)
   {
      $this->_hostname = $hostname;
      $this->_username = $username;
      $this->_password = $password;
      $this->_database_schema = $_database_schema;
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
    * Returns the user name.
    *
    * @return string
    */
   public function getUsername(): string
   {
      return $this->_username;
   }

   /**
    * Returns the password.
    *
    * @return string
    */
   public function getPassword(): string
   {
      return $this->_password;
   }

   /**
    * Returns the database schema.
    *
    * @return string
    */
   public function getDatabaseSchema(): string
   {
      return $this->_database_schema;
   }

   /**
    * Returns the PDO driver to use.
    *
    * @return string
    */
   public function getDriver(): string
   {
      return $this->_driver;
   }

   /**
    * Sets the PDO driver to use.
    *
    * @param string $driver
    */
   public function setDriver(string $driver)
   {
      $this->_driver = $driver;
   }
}
