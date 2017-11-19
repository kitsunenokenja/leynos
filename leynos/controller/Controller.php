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

namespace kitsunenokenja\leynos\controller;

use kitsunenokenja\leynos\config\PDOSettings;
use kitsunenokenja\leynos\http\Headers;
use kitsunenokenja\leynos\message\Message;
use kitsunenokenja\leynos\view\BinaryView;
use PDO;
use PDOException;

/**
 * Controller
 *
 * Base class for all controllers for the framework.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
abstract class Controller
{
   /**
    * Array containing inputs provided by the environment for consumption by the controller.
    *
    * @var array
    */
   protected $_in = [];

   /**
    * Array containing output generated by the controller, commonly the assortment of data to be consumed during
    * display rendering.
    *
    * @var array
    */
   protected $_out = [];

   /**
    * Stack of messages generated by the controller.
    *
    * @var Message[]
    */
   protected $_Messages = [];

   /**
    * Internal copy of the settings array. This is required for the lazy-loading of PDO connections for first-time
    * references.
    *
    * @var PDOSettings[]
    */
   protected $_PDOSettings = [];

   /**
    * References to databases persisted by the environment.
    *
    * @var PDO[]
    */
   protected $_Databases = [];

   /**
    * Reference to HTTP Headers provided by the environment. This allows controllers to contribute appropriate headers
    * such as cookies. Using this to generate status code headers is not advised since the framework anticipates making
    * such decisions based upon controller exit states.
    *
    * @var Headers
    */
   protected $_HTTPHeaders;

   /**
    * Internal copy of the document root. The kernel will set this value enabling controllers that perform file system
    * I/O manipulations to properly identify paths.
    *
    * @var string
    */
   protected $_document_root;

   /**
    * Internal copy of the HTTP Accept Language header passed down from the framework. Having this value allows a
    * controller to make decisions with regard to localisation.
    *
    * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
    *
    * @var string
    */
   protected $_accept_language;

   /**
    * An instance of a view for facilitating binary output. This member bridges the gap between the controller's task of
    * preparing output data, and the framework having a data set handed off to push to the client.
    *
    * @var BinaryView
    */
   protected $_BinaryView;

   /**
    * Magic get that wraps access to the _in array.
    *
    * @param string $key
    *
    * @return mixed
    */
   final public function __get(string $key)
   {
      return $this->_in[$key] ?? $this->_in['_data'][$key] ?? null;
   }

   /**
    * Magic isset that wraps access to the _in array.
    *
    * @param string $key
    *
    * @return bool
    */
   final public function __isset(string $key): bool
   {
      return isset($this->_in[$key]) || isset($this->_in['_data'][$key]);
   }

   /**
    * Merges a value into the inputs by key.
    *
    * @param string $key
    * @param mixed  $value
    */
   final public function addInput(string $key, $value): void
   {
      $this->_in[$key] = $value;
   }

   /**
    * Returns the outputs generated by the controller.
    *
    * @return array
    */
   final public function getOutputs(): array
   {
      return $this->_out;
   }

   /**
    * Merges keyed values into the outputs.
    *
    * @param array $values
    */
   final protected function _addOutputs(array $values): void
   {
      $this->_out = array_merge($this->_out, $values);
   }

   /**
    * Returns the message stack.
    *
    * @return Message[]
    */
   final public function getMessages(): array
   {
      return $this->_Messages;
   }

   /**
    * Returns the array of DB connections.
    *
    * @return PDO[]
    */
   final public function getDatabases(): array
   {
      return $this->_Databases;
   }

   /**
    * Sets the array of DB connections.
    *
    * @param PDO[] $Databases
    */
   final public function setDatabases(array $Databases): void
   {
      $this->_Databases = $Databases;
   }

   /**
    * Returns the DB connection of the requested alias. If the connection is not established, it will be opened first.
    * This getter manages opening connections as needed as well as handling the default DB reference. Derivations should
    * not access the PDO reference array directly.
    *
    * An exception is raised if the alias is undefined or the connection cannot be opened successfully.
    *
    * @param string $alias The alias whose DB connection to retrieve.
    *
    * @return PDO
    *
    * @throws PDOException Thrown if the DB connection could not be established or if the requested DB alias is invalid.
    */
   final protected function _getDB(string $alias = "default"): PDO
   {
      // If the connection is already available, return it immediately
      if(!empty($this->_Databases[$alias]))
      {
         return $this->_Databases[$alias];
      }
      // If the connection is not available, but defined, open it then return it
      elseif(!empty($this->_PDOSettings[$alias]))
      {
         $this->_Databases[$alias] = $this->_PDOSettings[$alias]->getPDO();
         return $this->_Databases[$alias];
      }

      // Abort if the alias is not defined by the application configuration
      throw new PDOException("Undefined DB alias requested.");
   }

   /**
    * Sets the array of PDO settings. Only the environment should call this when the controller is being prepared.
    *
    * @param PDOSettings[] $PDOSettings
    */
   final public function setPDOSettings(array $PDOSettings): void
   {
      $this->_PDOSettings = $PDOSettings;
   }

   /**
    * Sets the reference to HTTP Headers. Only the environment should pass this in when preparing to execute the
    * controller.
    *
    * @param Headers $HTTPHeaders
    */
   final public function setHTTPHeaders(Headers $HTTPHeaders): void
   {
      $this->_HTTPHeaders = $HTTPHeaders;
   }

   /**
    * Sets the document root.
    *
    * @param string $document_root
    */
   final public function setDocumentRoot(string $document_root): void
   {
      $this->_document_root = $document_root;
   }

   /**
    * Sets the accept language header's contents.
    *
    * @param string $accept_language
    */
   final public function setAcceptLanguage(string $accept_language = null): void
   {
      $this->_accept_language = $accept_language;
   }

   /**
    * Returns the view instance for binary output.
    *
    * @return BinaryView
    */
   final public function getBinaryView(): ?BinaryView
   {
      return $this->_BinaryView;
   }

   /**
    * Main function of the controller that is called by the framework to begin the controller's execution.
    *
    * @return bool
    */
   abstract public function main(): bool;
}
