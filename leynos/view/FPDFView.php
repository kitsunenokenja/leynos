<?php declare(strict_types=1);
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

// The FPDF library is not under any namespace. This wrapper assumes the class definition will be modified to force a
// namespace declaration for it.
use fpdf\FPDF;
use kitsunenokenja\leynos\http\Headers;

/**
 * FPDFView
 *
 * Wrapper for the FPDF library facilitating rendering PDF views with it.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class FPDFView implements BinaryView
{
   /**
    * Reference to the FPDF object containing the output in its buffer.
    *
    * @var FPDF
    */
   private FPDF $_FPDF;

   /**
    * The file name for output.
    *
    * @var string
    */
   private string $_file_name;

   /**
    * Creates the view by preparing with the FPDF object to render.
    *
    * @param FPDF   $FPDF
    * @param string $file_name
    */
   public function __construct(FPDF $FPDF, string $file_name = "pdf")
   {
      $this->_FPDF = $FPDF;
      $this->_file_name = $file_name;
   }

   /**
    * {@inheritdoc}
    */
   public function render(array $data = []): void
   {
      echo $this->_FPDF->Output("", 'S');
   }

   /**
    * {@inheritdoc}
    */
   public function getFileName(): string
   {
      return $this->_file_name;
   }

   /**
    * {@inheritdoc}
    */
   public function getMIMEType(): string
   {
      return Headers::PDF;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
