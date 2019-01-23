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
 * TemplateView
 *
 * This abstract serves as a basis of the general View interface for template engine views to extend.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
abstract class TemplateView implements View
{
   /**
    * Relative path to the template to render.
    *
    * @var string
    */
   protected $_template;

   /**
    * Sets the relative path to the template to render.
    *
    * @param string $template
    */
   final public function setTemplate(string $template): void
   {
      $this->_template = $template;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
