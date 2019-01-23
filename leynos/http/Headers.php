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

namespace kitsunenokenja\leynos\http;

/**
 * Headers
 *
 * This class provides methods that generate HTTP headers for the output stream. The variety of headers include HTTP
 * status codes reflecting errors or authentication-based rejection as well as facilitating redirects for PRG.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Headers
{
   // MIME content types
   const HTML = "text/html";
   const JSON = "application/json";
   const CSV  = "text/csv";
   const ODS  = "application/vnd.oasis.opendocument.spreadsheet";
   const XLSX = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
   const PDF  = "application/pdf";

   /**
    * The HTTP protocol of the current execution's context. The protocol in use will be reflected in response headers in
    * kind. For example, requests over HTTP/2.0 will receive response headers specifying HTTP/2.0 for the protocol.
    *
    * @var string
    */
   private $_protocol;

   /**
    * Outputs the header for a cookie.
    *
    * @param Cookie $Cookie
    */
   public function setCookie(Cookie $Cookie): void
   {
      setcookie(
         $Cookie->getName(),
         $Cookie->getValue(),
         $Cookie->getExpiry(),
         $Cookie->getPath(),
         $Cookie->getDomain(),
         $Cookie->getSecure(),
         $Cookie->getHTTPOnly()
      );
   }

   /**
    * Sets the server protocol and version.
    *
    * @param string $protocol
    */
   public function setProtocol(string $protocol): void
   {
      $this->_protocol = $protocol;
   }

   /**
    * Outputs an HTTP 303 See Other redirection header for PRG. This method will end script execution as no more output
    * may follow such a header.
    *
    * @param string $redirect_url
    */
   public function redirect(string $redirect_url): void
   {
      header("{$this->_protocol} 303 See Other");
      header("Location: $redirect_url");
   }

   /**
    * Outputs a content type identifying the stream contents to the client.
    *
    * @param string $mime
    */
   public function contentType(string $mime = self::HTML): void
   {
      header("Content-Type: $mime");
   }

   /**
    * Outputs the content disposition dictating the file name the client shall download.
    *
    * @param string $filename
    * @param bool   $inline
    */
   public function contentDisposition(string $filename, bool $inline = false): void
   {
      $disposition = $inline ? "inline" : "attachment";
      header("Content-Disposition: $disposition; filename=$filename");
   }

   /**
    * Outputs HTTP 403 Forbidden for handling requests that are denied due to insufficient permissions.
    */
   public function forbidden(): void
   {
      header("{$this->_protocol} 403 Forbidden");
   }

   /**
    * Outputs HTTP 404 Not Found for handling invalid route requests.
    */
   public function notFound(): void
   {
      header("{$this->_protocol} 404 Not Found");
   }

   /**
    * Outputs HTTP 500 Internal Server Error for handling low-level framework failures.
    */
   public function internalServerError(): void
   {
      header("{$this->_protocol} 500 Internal Server Error");
   }
}

# vim: set ts=3 sw=3 tw=120 et :
