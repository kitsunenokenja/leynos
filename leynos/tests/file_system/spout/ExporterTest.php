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

namespace kitsunenokenja\leynos\tests\file_system\spout;

use kitsunenokenja\leynos\file_system\spout\Exporter;
use kitsunenokenja\leynos\tests\mocks\TestFormatter;
use PHPUnit\Framework\TestCase;

/**
 * ExporterTest
 *
 * Unit test for the Spout Exporter class. These tests only evaluate that the exporter is delivering correctly rather
 * than evaluate that Spout itself is processing an export properly. Therefore only CSV mode will be triggered to keep
 * the test cases simpler.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class ExporterTest extends TestCase
{
   /**
    * Tests the exporter with a simple static data set for a CSV by checking the same data is in the output.
    */
   public function testCSVExport(): void
   {
      // Prepare a sample 3-column CSV with a header row and two data rows
      $Exporter = new Exporter(new TestFormatter());
      $Exporter->addRow(["Column 1", "Column 2", "Column 3"]);
      $Exporter->addRows([[1, 2, 3], [4, 5, 6]]);

      // Produce the Spout formatted version of the export
      $Exporter->open();
      $Formatter = $Exporter->getFormatter();
      $Formatter->addHeaderRow();
      $Formatter->addRows();
      $Formatter->close();

      // Check the contents to ensure exporter and formatter performed correctly
      $data = file_get_contents($Formatter->getTempFile()->getAbsolutePath());
      $this->assertContains('"Column 1","Column 2","Column 3"', $data);
      $this->assertContains("1,2,3", $data);
      $this->assertContains("4,5,6", $data);
   }
}
