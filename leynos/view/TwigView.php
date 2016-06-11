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

use LogicException;
use Twig_Environment;
use Twig_Error;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter; // do not remove the imports for simple filter & function
use Twig_SimpleFunction;

/**
 * TwigView
 *
 * Wrapper for the Twig template engine. Twig requires configuration for basic settings and additional settings such as
 * the filters and functions definitions. Presently, this class can be modified directly, or extended to suit the
 * application's needs; therefore it is not defined as an abstract class.
 * 
 * TODO - Provide proper abstraction by converting this to a true abstract class and allow extensions to define Twig's
 * parameters and options.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class TwigView extends TemplateView
{
   /**
    * Internal reference to the Twig environment.
    *
    * @var Twig_Environment
    */
   protected $_Twig;

   /**
    * Initialises Twig and its settings.
    *
    * @param string $document_root
    *
    * @throws TemplateException Thrown if the function or filter registrations throw an exception.
    */
   public function __construct(string $document_root)
   {
      // Prepare the template engine
      $this->_Twig = new Twig_Environment(
         new Twig_Loader_Filesystem("$document_root/templates"),
         [
            'cache' => "$document_root/templates/cache",
            'strict_variables' => true
         ]
      );

      try
      {
         // Custom settings for Twig
         $this->_registerFunctions();
         $this->_registerFilters();
      }
      catch(LogicException $E)
      {
         throw new TemplateException($E->getMessage(), $E->getCode(), $E);
      }
   }

   /**
    * Registers functions in Twig. Refer to Twig's documentation for the addFunction method.
    *
    * @throws LogicException
    */
   protected function _registerFunctions()
   {

   }

   /**
    * Registers filters in Twig. Refer to Twig's documentation for the addFilter method.
    *
    * @throws LogicException
    */
   protected function _registerFilters()
   {

   }

   /**
    * Returns the rendering of the template rather than echo it for internal usages.
    *
    * @param array $data
    *
    * @return string
    *
    * @throws TemplateException Thrown if the Twig engine fails to render the template.
    */
   final public function getRendering(array $data = []): string
   {
      return $this->_Twig->render($this->_template, $data) ?? "";
   }

   /**
    * {@inheritdoc}
    *
    * @throws TemplateException Thrown if the Twig engine fails to render the template.
    */
   final public function render(array $data = [])
   {
      try
      {
         echo $this->_Twig->render($this->_template, $data);
      }
      catch(Twig_Error $E)
      {
         throw new TemplateException($E->getMessage(), $E->getCode(), $E);
      }
   }
}
