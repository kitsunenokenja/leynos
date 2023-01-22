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

namespace kitsunenokenja\leynos\kernel;

use Exception;

/**
 * UnauthorizedActionException
 *
 * Exception for requested actions not authorised for the logged-in user.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class UnauthorizedActionException extends Exception {}

# vim: set ts=3 sw=3 tw=120 et :
