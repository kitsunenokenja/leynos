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
 * JSONView
 *
 * This view returns the incoming data via JSON encoding. JSON responses facilitate AJAX calls and possible API usages.
 *
 * @author Rob Levitsky <kitsunenokenja@protonmail.ch>
 */
class JSONView implements View
{
   /**
    * {@inheritdoc}
    */
   public function render(array $data = [])
   {
      $json = json_encode($data);

      if($json === false)
      {
         trigger_error(json_last_error_msg(), E_USER_WARNING);
         $json = json_encode(['error' => "JSON failure."]);
      }

      echo $json;
   }
}
