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

/**
 * PDFView
 *
 * View extension for PDFs mandating the ability to define the PDF's file name.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
interface PDFView extends View
{
   /**
    * Returns the file name of the PDF file to output.
    *
    * @return string
    */
   public function getFileName(): string;
}
