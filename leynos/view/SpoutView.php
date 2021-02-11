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

namespace kitsunenokenja\leynos\view;

use Box\Spout\Common\Type as SpreadsheetType;
use Box\Spout\Writer\WriterFactory;
use Exception;
use kitsunenokenja\leynos\file_system\spout\{Exporter, Formatter};
use kitsunenokenja\leynos\http\Headers;

/**
 * SpoutView
 *
 * Renders the output as a spreadsheet file for download.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class SpoutView implements BinaryView
{

   /**
    * Internal reference to the Formatter.
    *
    * @var Formatter
    */
   private Formatter $_Formatter;

   /**
    * The type of the spreadsheet. This must be a valid type defined by Spout's spreadsheet types.
    *
    * @var string
    */
   private string $_spreadsheet_type = SpreadsheetType::CSV;

   /**
    * The name of the file attributed to the export.
    *
    * @var string
    */
   private string $_filename;

   /**
    * Creates the view by preparing Spout with the specified spreadsheet type.
    *
    * @param Exporter $Exporter         Instance of a Spout exporter model.
    * @param string   $spreadsheet_type Type of spreadsheet as defined by Spout's types.
    *
    * @throws SpreadsheetException Thrown if the spreadsheet generation fails.
    */
   public function __construct(Exporter $Exporter, string $spreadsheet_type = SpreadsheetType::CSV)
   {
      try
      {
         // Set the type and filename for the framework to consume when rendering the view
         $this->_spreadsheet_type = $spreadsheet_type;
         $this->_filename = $Exporter->getFilename();

         // Prepare writing the formatted version of the export data
         $this->_Formatter = $Exporter->getFormatter();
         $this->_Formatter->setWriter(WriterFactory::create($spreadsheet_type));

         // Open the source data from the exporter and feed it through the formatter. This will produce the final
         // formatted version ready for the view response.
         $Exporter->open();
         $this->_Formatter->addHeaderRow();
         $this->_Formatter->addRows();
         $this->_Formatter->close();
      }
      catch(Exception $E)
      {
         throw new SpreadsheetException($E->getMessage());
      }
   }

   /**
    * {@inheritdoc}
    */
   public function getMIMEType(): string
   {
      switch($this->_spreadsheet_type)
      {
         case SpreadsheetType::CSV:
         default:
            return Headers::CSV;

         case SpreadsheetType::ODS:
            return Headers::ODS;

         case SpreadsheetType::XLSX:
            return Headers::XLSX;
      }
   }

   /**
    * {@inheritdoc}
    */
   public function getFileName(): string
   {
      return $this->_filename;
   }

   /**
    * {@inheritdoc}
    */
   public function render(array $data = [])
   {
      /*
       * This final I/O operation intentionally has no error checking!
       *
       * By this point the header information has already been echoed and the binary payload to be delivered has already
       * been written successfully. This should never fail, but if somehow it does, it's too late to gracefully bail
       * with a clean deliverable error anyway, so allow a broken stream to reach the client for such an edge case.
       */
      fpassthru(fopen($this->_Formatter->getTempFile()->getAbsolutePath(), 'r'));
   }
}

# vim: set ts=3 sw=3 tw=120 et :
