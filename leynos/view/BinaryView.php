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

namespace kitsunenokenja\leynos\view;

/**
 * BinaryView
 *
 * All data export views must implement this interface. Despite the name, the output does not need to be strictly binary
 * in the sense that text output such as CSVs would also implement this.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
interface BinaryView extends View
{
   /**
    * Returns the MIME type of the payload to be incorporated into the HTTP header sent to the client downloading it.
    *
    * @return string
    */
   public function getMIMEType(): string;

   /**
    * Returns the complete file name with suffix or extension, if applicable, of the payload to output.
    *
    * @return string
    */
   public function getFileName(): string;
}

# vim: set ts=3 sw=3 tw=120 et :
