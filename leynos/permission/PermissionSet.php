<?php declare(strict_types=1);
/**
 * Copyright (c) 2019.
 *
 * This file belongs to the Leynos project, an open-source project distributed
 * under the GPL license. This license is included as part of the project and
 * is also available at the following web page:
 *
 * https://www.gnu.org/licenses/gpl.html
 */

namespace kitsunenokenja\leynos\permission;

/**
 * PermissionSet
 *
 * The permission set is simply a container for a hash table that stores an arbitrary number of permission tokens.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class PermissionSet
{
   /**
    * Hash table containing the permissions tokens. Only granted permissions appear in the set.
    *
    * @var bool[]
    */
   private array $_permissions = [];

   /**
    * Return the set of permissions.
    *
    * @return string[]
    */
   public function getPermissions(): array
   {
      return array_keys($this->_permissions);
   }

   /**
    * Indicates whether a particular permission is set.
    *
    * @return bool
    */
   public function hasPermission(string $token): bool
   {
      return !empty($this->_permissions[$token]);
   }

   /**
    * Enables a single permission in the set.
    *
    * @param Options $Options
    */
   public function enablePermission(string $token): void
   {
      $this->_permissions[$token] = true;
   }

   /**
    * Disables a function as the options modifier for the route.
    *
    * @param callable $func
    */
   public function disablePermission(string $token): void
   {
      unset($this->_permissions[$token]);
   }
}

# vim: set ts=3 sw=3 tw=120 et :
