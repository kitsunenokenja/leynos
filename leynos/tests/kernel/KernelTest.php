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

namespace kitsunenokenja\leynos\tests\kernel;

use Exception;
use kitsunenokenja\leynos\kernel\Kernel;
use kitsunenokenja\leynos\tests\mocks\TestConfig;
use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;

/**
 * KernelTest
 *
 * Unit test for the Kernel class. Various tests are defined to test the different functionality and capabilities of
 * the kernel. Mock framework and routing configurations will be used to simulate requested executions to test.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class KernelTest extends TestCase
{
   /**
    * {@inheritdoc}
    */
   protected function setUp()
   {
      $_SERVER = [
         'DOCUMENT_ROOT'        => __DIR__,
         'REQUEST_METHOD'       => "GET",
         'HTTP_ACCEPT_LANGUAGE' => ""
      ];
   }

   /**
    * Tests a normal route execution with a single successful controller.
    *
    * @runInSeparateProcess
    */
   public function testSingleControllerRoute(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/true";
      $this->expectOutputString("success");
      new Kernel(new TestConfig());
   }

   /**
    * Tests a normal route execution simulating a POST request.
    *
    * @runInSeparateProcess
    */
   public function testPostRoute(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/true";
      $_SERVER['REQUEST_METHOD'] = "POST";
      $this->expectOutputString("success");
      new Kernel(new TestConfig());
   }

   /**
    * Tests route aliasing by calling a route via its alias.
    *
    * @runInSeparateProcess
    */
   public function testRouteAliasing(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/true_alias";
      $this->expectOutputString("success");
      new Kernel(new TestConfig());
   }

   /**
    * Tests the invocation of a default route.
    *
    * @runInSeparateProcess
    */
   public function testDefaultRouteExecution(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/default-route";
      $this->expectOutputString("success");
      new Kernel(new TestConfig());
   }

   /**
    * Tests the internal routing for handling a root index (GET /) request.
    *
    * @runInSeparateProcess
    */
   public function testRootIndexRequest(): void
   {
      $_SERVER['REQUEST_URI'] = "/";
      $this->expectOutputString("success");
      new Kernel(new TestConfig());
   }

   /**
    * Tests the output mapping for a route.
    *
    * @runInSeparateProcess
    */
   public function testMappedOutputRoute(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/mapped/json";
      $this->expectOutputString(json_encode(['existing_key' => true, 'non_existent_key' => null]));
      new Kernel(new TestConfig());
   }

   /**
    * Tests the error routing by calling a route that intentionally fails.
    *
    * @runInSeparateProcess
    */
   public function testErrorRouteExecution(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/false";
      new Kernel(new TestConfig());
      $this->assertEquals(303, http_response_code());
   }

   /**
    * Tests the kernel's exception-handling fallback's exception handling by forcing an invalid error route.
    *
    * @runInSeparateProcess
    */
   public function testFailingFallbackExecution(): void
   {
      $TestConfig = new TestConfig();
      $TestConfig->setErrorRoute("/invalid/route");
      $_SERVER['REQUEST_URI'] = "/test/false";
      try
      {
         new Kernel($TestConfig);
      }
      catch(Exception $E)
      {
         $this->assertFalse(true);
      }
      $this->assertTrue(true);
   }

   /**
    * Tests the handling of an invalid route request.
    *
    * @runInSeparateProcess
    */
   public function testInvalidRoutingPattern(): void
   {
      $_SERVER['REQUEST_URI'] = "/invalid_pattern";
      new Kernel(new TestConfig());
      $this->assertEquals(404, http_response_code());
   }

   /**
    * Tests the handling of an invalid route with a valid request.
    *
    * @runInSeparateProcess
    */
   public function testUndefinedRoute(): void
   {
      $_SERVER['REQUEST_URI'] = "/undefined/undefined";
      new Kernel(new TestConfig());
      $this->assertEquals(404, http_response_code());
   }

   /**
    * Tests the handling of an unknown response mode. The kernel should assume default HTML response.
    *
    * @runInSeparateProcess
    */
   public function testUnknownResponseMode(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/true/unknown";
      $this->expectOutputString("success");
      new Kernel(new TestConfig());
   }

   /**
    * Tests the handling of a route whose controller execution allows an exception to bubble up.
    *
    * @runInSeparateProcess
    */
   public function testUnauthorizedRequest(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/exception";
      $this->expectException(Warning::class);
      new Kernel(new TestConfig());
      $this->assertEquals(500, http_response_code());
   }
}
