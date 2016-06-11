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
   // Protocol and version used by all headers
   const VERSION = "HTTP/1.1 ";

   // MIME content types
   const HTML = "text/html";
   const JSON = "application/json";
   const CSV  = "text/csv";
   const ODS  = "application/vnd.oasis.opendocument.spreadsheet";
   const XLSX = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
   const PDF  = "application/pdf";

   /**
    * Outputs the header for a cookie.
    *
    * @param Cookie $Cookie
    */
   public function setCookie(Cookie $Cookie)
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
    * Outputs an HTTP 303 See Other redirection header for PRG. This method will end script execution as no more output
    * may follow such a header.
    *
    * @param string $redirect_url
    */
   public function redirect(string $redirect_url)
   {
      header(self::VERSION . "303 See Other");
      header("Location: $redirect_url");
      exit;
   }

   /**
    * Outputs a content type identifying the stream contents to the client.
    *
    * @param string $mime
    */
   public function contentType(string $mime = self::HTML)
   {
      header("Content-Type: $mime");
   }

   /**
    * Outputs the content disposition dictating the file name the client shall download.
    *
    * @param string $filename
    * @param bool   $inline
    */
   public function contentDisposition(string $filename, bool $inline = false)
   {
      $disposition = $inline ? "inline" : "attachment";
      header("Content-Disposition: $disposition; filename=$filename");
   }

   /**
    * Outputs HTTP 403 Forbidden for handling requests that are denied due to insufficient permissions.
    */
   public function forbidden()
   {
      header(self::VERSION . "403 Forbidden");
   }

   /**
    * Outputs HTTP 404 Not Found for handling invalid route requests.
    */
   public function notFound()
   {
      header(self::VERSION . "404 Not Found");
   }

   /**
    * Outputs HTTP 500 Internal Server Error for handling low-level framework failures.
    */
   public function internalServerError()
   {
      header(self::VERSION . "500 Internal Server Error");
   }
}
