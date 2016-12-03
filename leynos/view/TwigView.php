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

/**
 * TwigView
 *
 * Wrapper for the Twig template engine. Twig requires configuration for basic settings and additional settings such as
 * the filters and functions definitions. This class is not abstract as it defines a complete definition and can be used
 * without modification. Customisations of this class should be prepared by extending it and defining the inherited
 * methods accordingly.
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
   final public function __construct(string $document_root)
   {
      $this->_initializeTwig($document_root);

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
    * Initialises a Twig instance. This method is designed to be overridden to facilitate customising settings and
    * parameters for Twig.
    *
    * @param string $document_root
    */
   protected function _initializeTwig(string $document_root): void
   {
      // Prepare the template engine
      $this->_Twig = new Twig_Environment(
         new Twig_Loader_Filesystem("$document_root/templates"),
         [
            'cache' => "$document_root/templates/cache",
            'strict_variables' => true
         ]
      );
   }

   /**
    * Registers functions in Twig. Refer to Twig's documentation for the addFunction method.
    *
    * @throws LogicException
    */
   protected function _registerFunctions(): void
   {
      // Default behaviour is nothing. Custom extensions are to define this method if it is required.
   }

   /**
    * Registers filters in Twig. Refer to Twig's documentation for the addFilter method.
    *
    * @throws LogicException
    */
   protected function _registerFilters(): void
   {
      // Default behaviour is nothing. Custom extensions are to define this method if it is required.
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
   final public function render(array $data = []): void
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
