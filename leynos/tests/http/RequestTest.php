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

namespace kitsunenokenja\leynos\tests\http;

use kitsunenokenja\leynos\http\Request;
use PHPUnit\Framework\TestCase;

/**
 * RequestTest
 *
 * Unit test for the HTTP Request class. This class evaluates the sanitisation protections in the Request class.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class RequestTest extends TestCase
{
   /**
    * Tests the key sanitisation by ensuring invalid keys are scrubbed.
    */
   public function testKeySanitizer(): void
   {
      $_REQUEST = ['invalid_key!@#' => 1, 'valid_key' => 2];
      $Request = new Request("group", "route");

      $this->assertEmpty($Request->__get("invalid_key!@#"), "Invalid key check failed.");
      $this->assertNotEmpty($Request->__get("valid_key"), "Valid key check failed.");
   }

   /**
    * Tests the value sanitisation by ensuring XSS attempts are scrubbed.
    */
   public function testValueSanitizer(): void
   {
      $_REQUEST = [
         'markup' => "<p><i>Sample Text</i></p>",
         'nested' => "<<br>script src=\"evil_script.js\"></script>",
         'escape' => "&lt;script src=\"evil_script.js\"&gt;&lt;/script&gt;"
      ];
      $Request = new Request("group", "route");

      $this->assertEquals("Sample Text", $Request->__get("markup"), "General markup purge failed.");
      $this->assertEmpty($Request->__get("nested"), "Nested markup purge failed.");
      $this->assertEmpty($Request->__get("escape"), "Escaped markup purge failed.");
   }
}
