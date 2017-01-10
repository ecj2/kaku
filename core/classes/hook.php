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

          // Pass the action contents to filter methods to be manipulated.
          $action_contents = call_user_func(

            [

              $this->filters[$action_title][$i]["object"],

              $this->filters[$action_title][$i]["method"]
            ],

            $action_contents
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

  public function addFilter($action_title, $filter_object, $filter_method) {

    $this->filters[$action_title][] = [

      "object" => $filter_object,

      "method" => $filter_method
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
