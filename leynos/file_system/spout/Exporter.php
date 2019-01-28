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

namespace kitsunenokenja\leynos\file_system\spout;

use kitsunenokenja\leynos\file_system\{IOException, TempFile};

/**
 * Manager
 *
 * Exports are managed via this class. The export process flows as follows:
 *
 *    1) Controller uses this model to write a plain CSV to temporary file on disk. The first line of this file will
 *       contain a series of associative array-friendly names which is a self-identifying mapping sequence for the
 *       formatter that will read the file.
 *    2) A formatter is engaged by the view at response time to decide which format the spreadsheet will be and writes
 *       to another temporary file the formatted version of the original produced in the first step. Field references
 *       must match whatever the controller attributed via the first line in the plain CSV.
 *    3) The contents of the formatted temporary file are streamed to the client and the two temporary files are
 *       automatically freed implicitly upon script termination.
 *
 * Note: Even if the requested spreadsheet format is CSV, the content still needs to be processed as there may be
 * display manipulations to perform and/or the addition/omission of fields that the original plain CSV may or may not
 * have.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Exporter
{
   /**
    * Name of the spreadsheet export that will be sent to the client for download.
    *
    * @var string
    */
   private $_filename = "export";

   /**
    * Temporary file which will store the plain CSV data to be formatted.
    *
    * @var TempFile
    */
   private $_Source;

   /**
    * Reference to the formatter that will process the source data.
    *
    * @var Formatter
    */
   private $_Formatter;

   /**
    * Creates an export manager.
    *
    * @param Formatter $Formatter
    *
    * @throws IOException Thrown if a temporary file could not be opened.
    */
   public function __construct(Formatter $Formatter)
   {
      $this->_Source = new TempFile();
      $this->_Formatter = $Formatter;
   }

   /**
    * Returns the filename of the export that will be sent to the client for download.
    *
    * @return string
    */
   public function getFilename(): string
   {
      return $this->_filename;
   }

   /**
    * Sets the filename of the export that will be sent to the client for download.
    *
    * @param string $filename
    */
   public function setFilename(string $filename): void
   {
      $this->_filename = $filename;
   }

   /**
    * Returns the Formatter.
    *
    * @return Formatter
    */
   public function getFormatter(): Formatter
   {
      return $this->_Formatter;
   }

   /**
    * Appends a row to the source file.
    *
    * @param array $row
    *
    * @throws IOException Thrown if writing to temporary file fails.
    */
   public function addRow(array $row): void
   {
      $this->_Source->putCSV($row);
   }

   /**
    * Appends multiple rows to the source file.
    *
    * @param array $rows
    *
    * @throws IOException Thrown if writing to temporary file fails.
    */
   public function addRows(array $rows): void
   {
      foreach($rows as $row)
         $this->_Source->putCSV($row);
   }

   /**
    * Opens the formatter with the prepared source file.
    *
    * @throws IOException Thrown upon I/O failure.
    */
   public function open(): void
   {
      $this->_Formatter->open($this->_Source->getAbsolutePath());
   }
}

# vim: set ts=3 sw=3 tw=120 et :
