<?php
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
 * FailureController
 *
 * This controller always return failure.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class FailureController extends Controller
{
   /**
    * {@inheritdoc}
    */
   public function main(): int
   {
      return ExitState::FAILURE;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
