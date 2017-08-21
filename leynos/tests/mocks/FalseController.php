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
 * FalseController
 *
 * This controller always return false.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class FalseController extends Controller
{
   /**
    * {@inheritdoc}
    */
   public function main(): bool
   {
      return false;
   }
}
