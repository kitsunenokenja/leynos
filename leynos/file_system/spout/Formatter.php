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

use Box\Spout\Common\Exception\{IOException as SpoutIOException, UnsupportedTypeException};
use Box\Spout\Common\Type as SpreadsheetType;
use Box\Spout\Writer\Style\{Style, StyleBuilder};
use Box\Spout\Writer\{WriterFactory, WriterInterface};
use kitsunenokenja\leynos\file_system\{IOException, TempFile};

/**
 * Formatter
 *
 * Base formatter class for spreadsheet output.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
abstract class Formatter
{
   /**
    * Internal reference to the Spout spreadsheet writer.
    *
    * @var WriterInterface
    */
   protected WriterInterface $_Writer;

   /**
    * Temporary file used for writing the generated spreadsheet output. The contents of this file will be streamed to
    * the client to output the completed spreadsheet.
    *
    * @var TempFile
    */
   protected TempFile $_Temp;

   /**
    * Resource handle to the source file containing the plain CSV to be formatted into the final spreadsheet.
    *
    * @var resource
    */
   protected $_Source;

   /**
    * The first row of the source CSV will be a list of headers whose order implies the mapping sequences of the
    * fields. This first row is stored in this array.
    *
    * @var string[]
    */
   protected array $_headers = [];

   /**
    * Creates the formatter.
    *
    * @throws IOException              Thrown if the source file cannot be opened.
    * @throws UnsupportedTypeException Thrown if the Spout writer cannot be created.
    */
   public function __construct()
   {
      $this->_Temp = new TempFile();
      $this->_Writer = WriterFactory::create(SpreadsheetType::CSV);
   }

   /**
    * Sets the Spout writer to use for generating a spreadsheet. This writer object dictates the format of the
    * spreadsheet and is required for the formatter class.
    *
    * @param WriterInterface $Writer
    */
   public function setWriter(WriterInterface $Writer): void
   {
      $this->_Writer = $Writer;
   }

   /**
    * Returns the temporary file containing the generated spreadsheet.
    *
    * @return TempFile
    */
   public function getTempFile(): TempFile
   {
      return $this->_Temp;
   }

   /**
    * Opens the source for input and Spout writer for output.
    *
    * @param string $path Path to the CSV containing the data to be formatted.
    *
    * @throws IOException      Thrown if the source file cannot be opened.
    * @throws SpoutIOException Thrown if Spout cannot prepare the temporary file for output.
    */
   public function open(string $path): void
   {
      // Open the source data file
      if(($this->_Source = fopen($path, 'r')) === false)
         throw new IOException("Failed to open source export data from disk");

      // Save the first record as the header list
      $this->_headers = array_flip(fgetcsv($this->_Source));

      $this->_Writer->openToFile($this->_Temp->getAbsolutePath());
   }

   /**
    * Closes the Spout writer and source file.
    */
   public function close(): void
   {
      $this->_Writer->close();
      fclose($this->_Source);
   }

   /**
    * Adds a formatted header to the spreadsheet. This should only be called once as the initial row.
    *
    * @return void
    */
   abstract public function addHeaderRow(): void;

   /**
    * Adds the data as spreadsheet rows.
    */
   public function addRows(): void
   {
      while(($record = fgetcsv($this->_Source)) !== false)
         $this->_Writer->addRowWithStyle($this->_formatRecord($record), $this->_buildStyle($record));
   }

   /**
    * Formats a CSV record for its final representation in the generated spreadsheet. Any view-layer manipulations
    * to the data being exported should be done via this callback.
    *
    * @param array $record
    *
    * @return array
    */
   protected function _formatRecord(array $record): array
   {
      // Default behaviour is to do nothing and return the record as-is
      return $record;
   }

   /**
    * Builds a style for the row based on the contents of the record.
    *
    * @param array $record
    *
    * @return Style
    */
   protected function _buildStyle(array $record): Style
   {
      return (new StyleBuilder())->build();
   }
}

# vim: set ts=3 sw=3 tw=120 et :
