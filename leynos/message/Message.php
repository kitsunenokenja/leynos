<?php
/**
 * Copyright (c) 2016.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\message;

use JsonSerializable;

/**
 * Message
 *
 * Container for framework messages. Controllers can push messages onto an internal stack managed by the framework
 * which will be passed to the view of the following request, at which point they should be flushed. These messages
 * allow passing notifications between requests for pages using traditional POST submissions rather than AJAX.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class Message implements JsonSerializable
{
   /**
    * Notice type is general and neutral. This is for informational messages.
    */
   const NOTICE = 1;

   /**
    * Success type is an indication that a process has properly completed e.g. the last POST that was processed was
    * successfully completed.
    */
   const SUCCESS = 2;

   /**
    * Failure type indicates any failure or error. Messages of this type ought to notify the user of warnings, errors,
    * failures, unexpected issues, etc.
    */
   const FAILURE = 3;

   /**
    * The type of message. This identifier suggests the nature of the message, and how it should be handled.
    *
    * @var int
    */
   private int $_type;

   /**
    * The contents of the message to be shown.
    *
    * @var string
    */
   private string $_message;

   /**
    * Creates a new message object.
    *
    * @param int    $type    The type must be one of the constant values in this class.
    * @param string $message
    */
   public function __construct(int $type, string $message)
   {
      $this->_type    = $type;
      $this->_message = $message;
   }

   /**
    * Returns the type.
    *
    * @return int
    */
   public function getType(): int
   {
      return $this->_type;
   }

   /**
    * Returns the message.
    *
    * @return string
    */
   public function getMessage(): string
   {
      return $this->_message;
   }

   /**
    * {@inheritdoc}
    */
   public function jsonSerialize()
   {
      return [
         'type'    => $this->_type,
         'message' => $this->_message
      ];
   }
}

# vim: set ts=3 sw=3 tw=120 et :
