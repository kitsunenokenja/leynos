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

namespace kitsunenokenja\leynos\tests\mocks;

use kitsunenokenja\leynos\file_system\spout\Formatter;

/**
 * TestFormatter
 *
 * Generic formatter concretion for testing.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class TestFormatter extends Formatter
{
   /**
    * {@inheritdoc}
    */
   public function addHeaderRow(): void
   {
      $this->_Writer->addRow(["Column 1", "Column 2", "Column 3"]);
   }
}

# vim: set ts=3 sw=3 tw=120 et :
