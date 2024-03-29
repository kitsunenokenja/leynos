<?php declare(strict_types=1);
/**
 * Copyright (c) 2016.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\route;

use kitsunenokenja\leynos\config\Options;

/**
 * OptionsOverrides
 *
 * This trait provides the members for inherited options overrides.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
trait OptionsOverrides
{
   /**
    * Array tracking which overrides are engaged.
    *
    * @var bool[]
    */
   protected array $_overrides = [];

   /**
    * Returns the options overrides.
    *
    * @return bool[]
    */
   public function getOverrides(): array
   {
      return $this->_overrides;
   }

   /**
    * Sets the override for the session required option.
    *
    * @param bool $setting
    */
   public function setSessionRequired(bool $setting): void
   {
      $this->_overrides[Options::SESSION_REQUIRED] = $setting;
   }

   /**
    * Sets the override for the enable template engine option.
    *
    * @param bool $setting
    */
   public function setEnableTemplateEngine(bool $setting): void
   {
      $this->_overrides[Options::ENABLE_TEMPLATE_ENGINE] = $setting;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
