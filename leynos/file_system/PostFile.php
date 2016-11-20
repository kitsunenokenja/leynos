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

namespace kitsunenokenja\leynos\file_system;

/**
 * PostFile
 *
 * Generic container for usage with replacing data in the traditional $_FILES super-global.
 */
class PostFile
{
   // Standard status for successful uploads
   const OK = UPLOAD_ERR_OK;

   // Failure due to file size exceeding the max file size directive's value
   const ERROR_MAX_SIZE = UPLOAD_ERR_INI_SIZE;

   // Failure due to file size exceeding the max file size declared in the submission form, if applicable
   const ERROR_FORM_SIZE = UPLOAD_ERR_FORM_SIZE;

   // Failure due to partial transmission
   const ERROR_PARTIAL = UPLOAD_ERR_PARTIAL;

   // Failure due to the lack of a file
   const ERROR_NO_FILE = UPLOAD_ERR_NO_FILE;

   // Failure due to missing temporary directory
   const ERROR_MISSING_TEMP_DIR = UPLOAD_ERR_NO_TMP_DIR;

   // Failure due to disk write failure
   const ERROR_WRITE_FAILURE = UPLOAD_ERR_CANT_WRITE;

   // Failure due to stoppage by PHP extension
   const ERROR_EXTENSION = UPLOAD_ERR_EXTENSION;

   /**
    * Original name of the file from the client.
    *
    * @var string
    */
   private $_name;

   /**
    * MIME type of the file.
    *
    * @var string
    */
   private $_type;

   /**
    * Size of the file in bytes.
    *
    * @var int
    */
   private $_size;

   /**
    * Absolute path of the temporary file created.
    *
    * @var string
    */
   private $_temp_name;

   /**
    * Status or error code of the file.
    *
    * @var int
    */
   private $_error;

   /**
    * Creates a new post file object.
    *
    * @param string $name
    * @param string $type
    * @param int    $size
    * @param string $temp_name
    * @param int    $error
    */
   public function __construct(string $name, string $type, int $size, string $temp_name, int $error)
   {
      $this->_name = $name;
      $this->_type = $type;
      $this->_size = $size;
      $this->_temp_name = $temp_name;
      $this->_error = $error;
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
    * Sets the name.
    *
    * @param string $name
    */
   public function setName(string $name)
   {
      $this->_name = $name;
   }

   /**
    * Returns the type.
    *
    * @return string
    */
   public function getType(): string
   {
      return $this->_type;
   }

   /**
    * Sets the type.
    *
    * @param string $type
    */
   public function setType(string $type)
   {
      $this->_type = $type;
   }

   /**
    * Returns the size.
    *
    * @return int
    */
   public function getSize(): int
   {
      return $this->_size;
   }

   /**
    * Sets the size.
    *
    * @param int $size
    */
   public function setSize(int $size)
   {
      $this->_size = $size;
   }

   /**
    * Returns the temporary name.
    *
    * @return string
    */
   public function getTempName(): string
   {
      return $this->_temp_name;
   }

   /**
    * Sets the temporary name.
    *
    * @param string $temp_name
    */
   public function setTempName(string $temp_name)
   {
      $this->_temp_name = $temp_name;
   }

   /**
    * Returns the error/status code.
    *
    * @return int
    */
   public function getError(): int
   {
      return $this->_error;
   }

   /**
    * Sets the error/status code.
    *
    * @param int $error
    */
   public function setError(int $error)
   {
      $this->_error = $error;
   }
}
