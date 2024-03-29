<?php declare(strict_types=1);
/**
 * Copyright (c) 2017.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\tests\kernel;

use Exception;
use kitsunenokenja\leynos\kernel\Kernel;
use kitsunenokenja\leynos\message\Message;
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
   protected function setUp(): void
   {
      // Forge plausible environment values for both the kernel and PHPUnit to run.
      $_SERVER = [
         'DOCUMENT_ROOT'        => __DIR__,
         'REQUEST_METHOD'       => "GET",
         'HTTP_ACCEPT_LANGUAGE' => null,
         'SCRIPT_NAME'          => __FILE__,
         'SERVER_PROTOCOL'      => "HTTP/2"
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
    * Tests the invocation of a 3-slice route.
    *
    * @runInSeparateProcess
    */
   public function testSliceChain(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/success-chain";
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
    * Tests an internally rewritten slice.
    *
    * @runInSeparateProcess
    */
   public function testRewrite(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/rewrite";
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
      $this->expectOutputString(
         json_encode(['existing_key' => true, 'non_existent_key' => null, '_messages' => []])
      );
      new Kernel(new TestConfig());
   }

   /**
    * Tests the basic usage of an input map, having a value pass through a controller.
    *
    * @runInSeparateProcess
    */
   public function testInputMap(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/input_map_test";
      $this->expectOutputString("value");
      new Kernel(new TestConfig());
   }

   /**
    * Tests the I/O for store maps.
    *
    * @runInSeparateProcess
    */
   public function testStoreMap(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/store_map_test";
      $this->expectOutputString("value");
      new Kernel(new TestConfig());
   }

   /**
    * Tests the output map via aliasing.
    *
    * @runInSeparateProcess
    */
   public function testOutputMap(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/output_map_test";
      $this->expectOutputString("value");
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
    * Tests the proper response of an early exit from a failing slice mid-chain.
    *
    * @runInSeparateProcess
    */
   public function testFailureChain(): void
   {
      $TestConfig = new TestConfig();
      $_SERVER['REQUEST_URI'] = "/test/failure-chain";
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
    * Tests the enforcement of permissions by ensuring the presence of a permissions leads to permitted route access.
    *
    * @runInSeparateProcess
    */
   public function testEnabledPermission(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/auth";
      $this->expectOutputString("success");
      new Kernel(new TestConfig());
   }

   /**
    * Tests the enforcement of permissions by ensuring the presence of a permissions leads to unauthorised route access.
    *
    * @runInSeparateProcess
    */
   public function testDisabledPermission(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/invalid_auth";
      new Kernel(new TestConfig());
      $this->assertEquals(403, http_response_code());
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
      $this->expectWarning(Warning::class);
      new Kernel(new TestConfig());
      $this->assertEquals(500, http_response_code());
   }

   /**
    * Tests message rendering in JSON mode.
    *
    * @runInSeparateProcess
    */
   public function testMessages(): void
   {
      $_SERVER['REQUEST_URI'] = "/test/message/json";
      $Message = new Message(Message::SUCCESS, "Success message");
      $this->expectOutputString(json_encode(['_messages' => [$Message->jsonSerialize()]]));
      new Kernel(new TestConfig());
   }
}

# vim: set ts=3 sw=3 tw=120 et :
