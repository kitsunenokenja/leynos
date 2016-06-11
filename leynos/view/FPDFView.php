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

namespace kitsunenokenja\leynos\view;

// The FPDF library is not under any namespace. This wrapper assumes the class definition will be modified to force a
// namespace declaration for it.
use fpdf\FPDF;

/**
 * FPDFView
 *
 * Wrapper for the FPDF library facilitating rendering PDF views with it.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class FPDFView implements View
{
   /**
    * Reference to the FPDF object containing the output in its buffer.
    *
    * @var FPDF
    */
   private $_FPDF;

   /**
    * Creates the view by preparing with the FPDF object to render.
    *
    * @param FPDF $FPDF
    */
   public function __construct(FPDF $FPDF)
   {
      $this->_FPDF = $FPDF;
   }

   /**
    * {@inheritdoc}
    */
   public function render(array $data = [])
   {
      echo $this->_FPDF->Output("", 'S');
   }
}
