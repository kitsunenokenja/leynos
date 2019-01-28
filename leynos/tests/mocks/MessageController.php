<?php
/**
 * Copyright (c) 2019.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\tests\mocks;

use kitsunenokenja\leynos\controller\{Controller, ExitState};
use kitsunenokenja\leynos\message\Message;

/**
 * MessageController
 *
 * This controller always outputs a successful message.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class MessageController extends Controller
{
   /**
    * {@inheritdoc}
    */
   public function main(): int
   {
      $this->_Messages[] = new Message(Message::SUCCESS, "Success message");
      return ExitState::SUCCESS;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
