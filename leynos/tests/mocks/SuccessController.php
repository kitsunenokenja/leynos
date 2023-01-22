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

namespace kitsunenokenja\leynos\tests\mocks;

use kitsunenokenja\leynos\controller\{Controller, ExitState};

/**
 * SuccessController
 *
 * This controller always returns success.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class SuccessController extends Controller
{
   /**
    * {@inheritdoc}
    */
   public function main(): int
   {
      $this->_out = ['output' => "success"];
      return ExitState::SUCCESS;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
