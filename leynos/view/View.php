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

namespace kitsunenokenja\leynos\view;

/**
 * View
 *
 * All views must implement this interface. Views must define the following methods for the framework to use them to
 * render output.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
interface View
{
   /**
    * Renders the output by consuming the variables provided by the array parameter. This method must be binary-safe to
    * support rendering generated payloads such as ODS spreadsheets.
    *
    * @param array $data
    *
    * @return mixed
    */
   public function render(array $data = []);
}
