<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Hook {

  private $actions;
  private $filters;

  public function __construct() {

    $this->actions = [];
    $this->filters = [];
  }

  public function doAction($action_title) {

    if (isset($this->actions[$action_title])) {

      if (isset($this->filters[$action_title])) {

        // Get the original contents of the action.
        $action_contents = $this->actions[$action_title];

        for ($i = 0; $i < count($this->filters[$action_title]); ++$i) {

          $arguments = [];

          $arguments[0] = $action_contents;

          $arguments = array_merge($arguments, $this->filters[$action_title][$i]["arguments"]);

          // Pass the action contents to filter methods to be manipulated.
          $action_contents = call_user_func_array(

            [

              $this->filters[$action_title][$i]["object"],

              $this->filters[$action_title][$i]["method"]
            ],

            $arguments
          );
        }

        // Return the modified contents of the action.
        return $action_contents;
      }
      else {

        // Return the original contents of the action.
        return $this->actions[$action_title];
      }
    }
  }

  public function addAction($action_title, $action_contents) {

    if (!isset($this->actions[$action_title])) {

      // Add the action only if it does not already exist.
      $this->actions[$action_title] = $action_contents;
    }
  }

  public function addFilter() {

    $action_title = func_get_arg(0);
    $filter_object = func_get_arg(1);
    $filter_method = func_get_arg(2);

    $arguments = [];

    for ($i = 3; $i < func_num_args(); ++$i) {

      $arguments[] = func_get_arg($i);
    }

    $this->filters[$action_title][] = [

      "object" => $filter_object,

      "method" => $filter_method,

      "arguments" => $arguments
    ];
  }

  public function removeAction($action_title) {

    if (isset($this->actions[$action_title])) {

      unset($this->actions[$action_title]);
    }
  }

  public function removeFilter($action_title) {

    if (isset($this->filters[$action_title])) {

      unset($this->filters[$action_title]);
    }
  }
}

?>
