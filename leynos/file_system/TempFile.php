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

namespace kitsunenokenja\leynos\file_system;

/**
 * TempFile
 *
 * Encapsulation of a temporary file.
 *
 * The SPL offers a temporary file object but because it uses memory for the first 2MB by default, its inherited methods
 * do not return a real path even when the data is written to disk. The SPL version of a file object may be usable but
 * persistence could be troublesome. This class creates truly temporary files that will not persist after script
 * termination and is able to return the real path from the system's native filesystem location of temporary data.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class TempFile
{
   /**
    * Resource handle for the temporary file.
    *
    * @var resource
    */
   private $_TempFile;

   /**
    * Path to the temporary file.
    *
    * @var string
    */
   private $_absolute_path;

   /**
    * Creates a temporary file.
    *
    * @throws IOException Thrown if a temporary file could not be opened.
    */
   public function __construct()
   {
      if(($this->_TempFile = tmpfile()) === false)
         throw new IOException();

      $this->_absolute_path = stream_get_meta_data($this->_TempFile)['uri'];
   }

   /**
    * Cleans up by deleting the temporary file.
    */
   public function __destruct()
   {
      fclose($this->_TempFile);
   }

   /**
    * Returns the resource handle to the temporary file.
    *
    * @return resource
    */
   public function getTempFile()
   {
      return $this->_TempFile;
   }

   /**
    * Returns the path to the temporary file.
    *
    * @return string
    */
   public function getAbsolutePath(): string
   {
      return $this->_absolute_path;
   }

   /**
    * Appends a record to the temporary CSV file.
    *
    * @param array $record
    *
    * @throws IOException Thrown if writing to temporary file fails.
    */
   public function putCSV(array $record): void
   {
      if((fputcsv($this->_TempFile, $record)) === false)
         throw new IOException();
   }
}

