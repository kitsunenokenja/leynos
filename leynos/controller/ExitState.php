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

namespace kitsunenokenja\leynos\controller;

use kitsunenokenja\leynos\route\Route;

/**
 * ExitState
 *
 * The exit state is a small data structure representing a response action for the framework to follow when considering
 * the return code from a controller. The default behaviour is to continue execution to the next slice which requires no
 * mapping of exit state.
 *
 * Slices are not required to provide a mapping for all return codes their controllers can emit. Not mapping a return
 * code implies to the framework that it shall advance to the next slice. However, the final slice must define a mapped
 * exit state for all return codes its controller may emit for proper execution by the framework.
 *
 * Exit codes are defined as class constants, and intentionally as integers, easily facilitating the extension of this
 * class by any application to define any new codes. There are no constraints around the pre-defined assortment of
 * return code constants; an application may implement any combination of the framework-provided return codes, or none
 * at all. When extending the class to add new return codes, no new codes should have values under 100 to effectively
 * reserve new codes in the future for the framework.
 *
 * To the framework, there is no differentiation among the return codes in terms of which represent success and which
 * represent failure. Naming conventions are for the benefit of the application developer to identify their meaning. If
 * a non-terminating slice's controller emits an unmapped return code, even if it is a failure code, the framework will
 * advance to the next slice nevertheless. This behaviour is by design and allows fault tolerance for controllers.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class ExitState
{
   /**
    * Render mode is the default response mode. It signals the framework to trigger the view layer.
    */
   const RENDER = 0;

   /**
    * Redirect mode signals the framework to respond with a redirection to another route.
    */
   const REDIRECT = 1;

   /**
    * Rewrite mode transfers control from the current slice sequence execution to another immediately. Typically a
    * traditional redirect is used which involves completing an execution and yielding the redirect to the client, which
    * in turn triggers the next new execution. In some cases, however, it is rather favourable to construct similar
    * routes that internally connect to one another, without resorting to forcing the client to follow a redirect, and
    * without resorting to heavily duplicating routing programming.
    *
    * Note that this behaviour is exclusive to executing slice sequences, and does not reboot the entire route
    * processing logic in the kernel, so there can be dangerous consequences such as effectively bypassing an
    * authorisation protection placed on a given route.
    */
   const REWRITE = 2;

   /**
    * Standard return code for success. This represents a controller has accomplished its objectives and returns with
    * confirmation of success. This default state may be used for any sort of success.
    */
   const SUCCESS = 0;

   /**
    * Standard return code for failure. This represents a controller has failed to accomplish its objective and must
    * exit abnormally with failure as a result. This default state may be used for any sort of failure.
    */
   const FAILURE = 1;

   /**
    * Idle success is for controllers that determine they have no action to take, yet intentionally return successfully.
    * Distinguishing this sort of success from the standard success may be useful for branching behaviour with the exit
    * state map.
    */
   const IDLE_SUCCESS = 2;

   /**
    * Input failure is based on invalid form submissions, generally invalidation of input, but may be based on any
    * source of input that causes a controller to fail. This failure may be an intentional abort or unexpected failure.
    */
   const INPUT_FAILURE = 3;

   /**
    * Database failure is for controllers that must return with error due to I/O failure with a database.
    */
   const DATABASE_FAILURE = 4;

   /**
    * General I/O failure code. Using this code may reflect file system I/O failure or other kinds such as network I/O.
    */
   const IO_FAILURE = 5;

   /**
    * Memory store failure. Useful for controllers that invoke any memory store and fail to perform I/O successfully,
    * requiring the controller to abort.
    */
   const MEMORY_FAILURE = 6;

   /**
    * The exit state return code to which the mode/target pair will be associated.
    *
    * @var int
    */
   private int $_state;

   /**
    * The Route class constant designating which sort of action is to be taken. This must be either a render or redirect
    * action.
    *
    * @see Route
    *
    * @var int
    */
   private int $_mode;

   /**
    * If the mode is redirect, the target must be an action route. Otherwise, it is a template name for rendering. The
    * target may be null when a template name is not applicable, normally for non-markup response modes.
    *
    * @var string
    */
   private ?string $_target;

   /**
    * Creates an exit state object.
    *
    * @param int    $state  The exit state return code to be assigned.
    * @param int    $mode   The route mode action being bound.
    * @param string $target The action route for redirect mode, or optionally the template name for render mode.
    */
   public function __construct(int $state, int $mode, string $target = null)
   {
      $this->_state = $state;
      $this->_mode = $mode;
      $this->_target = $target;
   }

   /**
    * Returns the exit state return code.
    *
    * @return int
    */
   public function getState(): int
   {
      return $this->_state;
   }

   /**
    * Returns the route mode.
    *
    * @return int
    */
   public function getMode(): int
   {
      return $this->_mode;
   }

   /**
    * Returns the target string, which is either an action route for redirect or template name for rendering.
    *
    * @return string
    */
   public function getTarget(): ?string
   {
      return $this->_target;
   }
}

# vim: set ts=3 sw=3 tw=120 et :
