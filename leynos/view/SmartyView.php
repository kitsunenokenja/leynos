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

namespace kitsunenokenja\leynos\view;

use Exception;
use Smarty;

/**
 * SmartyView
 *
 * Wrapper for the Smarty template engine. This class is not abstract as it defines a complete definition and can be
 * used without modification. Customisations of this class should be prepared by extending it and defining the inherited
 * methods accordingly.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class SmartyView extends TemplateView
{
   /**
    * Internal reference to the Smarty instance.
    *
    * @var Smarty
    */
   protected $_Smarty;

   /**
    * Initialises Smarty and its settings.
    *
    * @param string $document_root
    */
   final public function __construct(string $document_root)
   {
      $this->_Smarty = new Smarty();
      $this->_initializeSmarty($document_root);
   }

   /**
    * Initialises a Smarty instance. This method is designed to be overridden to facilitate customising settings and
    * parameters for Smarty.
    *
    * @param string $document_root
    */
   protected function _initializeSmarty(string $document_root): void
   {
      $this->_Smarty->setConfigDir("$document_root/templates/config");
      $this->_Smarty->setTemplateDir("$document_root/templates");
      $this->_Smarty->setCompileDir("$document_root/templates_c");
      $this->_Smarty->setCacheDir("$document_root/templates/cache");
   }

   /**
    * Returns the rendering of the template rather than echo it for internal usages.
    *
    * @param array $data
    *
    * @return string
    *
    * @throws TemplateException Thrown if the Smarty engine fails to render the template.
    */
   final public function getRendering(array $data = []): string
   {
      try
      {
         foreach($data as $key => $value)
            $this->_Smarty->assign($key, $value);

         return $this->_Smarty->fetch($this->_template) ?: "";
      }
      catch(Exception $E)
      {
         // Smarty's display is documented to throw both SmartyException and Exception; no need to distinguish here
         throw new TemplateException($E->getMessage(), $E->getCode(), $E);
      }
   }

   /**
    * {@inheritdoc}
    *
    * @throws TemplateException Thrown if the Smarty engine fails to render the template.
    */
   final public function render(array $data = []): void
   {
      echo $this->getRendering($data);
   }
}

# vim: set ts=3 sw=3 tw=120 et :
