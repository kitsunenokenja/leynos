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

namespace kitsunenokenja\leynos\tests\mocks;

use kitsunenokenja\leynos\controller\{Controller, ExitState};

/**
 * IOController
 *
 * This controller always outputs the input it receives named "input".
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 *
 * @property mixed $input Arbitrary value that is always outputted by this controller.
 */
class IOController extends Controller
{
   /**
    * {@inheritdoc}
    */
   public function main(): int
   {
      $this->_out = ['output' => $this->input];
      return ExitState::SUCCESS;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
