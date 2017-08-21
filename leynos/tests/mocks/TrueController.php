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

use kitsunenokenja\leynos\controller\Controller;

/**
 * TrueController
 *
 * This controller always succeeds and returns true.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class TrueController extends Controller
{
   /**
    * {@inheritdoc}
    */
   public function main(): bool
   {
      $this->_out = ['output' => "success"];
      return true;
   }
}
