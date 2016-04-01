<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class Hook {

  private $actions;
  private $filters;

  private $action_objects;
  private $filter_objects;
  private $filter_methods;

  public function __construct() {

    $this->actions = [];
    $this->filters = [];

    $this->action_objects = [];
    $this->filter_objects = [];
    $this->filter_methods = [];
  }

  public function doAction($action) {

    if (in_array($action, $this->actions)) {

      if (array_key_exists($action, $this->filters)) {

        // Get the contents of the original action.
        $callback = $this->action_objects[$action];

        for ($i = 0; $i < count($this->filters[$action]); ++$i) {

          // Pass the callback to filter methods to be manipulated.
          $callback = call_user_func(

            [

              $this->filter_objects[$action][$i],

              $this->filter_methods[$action][$i]
            ],

            $callback
          );
        }

        // Return the modified contents of the action.
        return $callback;
      }
      else {

        // Return the original contents of the action.
        return $this->action_objects[$action];
      }
    }
  }

  public function addAction($action, $object) {

    if (!in_array($action, $this->actions)) {

      $this->actions[$action] = $action;

      $this->action_objects[$action] = $object;
    }
  }

  public function addFilter($action, $object, $method) {

    if (!isset($this->filters[$action])) {

      $this->filters[$action] = [];
    }

    if (!isset($this->filter_objects[$action])) {

      $this->filter_objects[$action] = [];
    }

    if (!isset($this->filter_methods[$action])) {

      $this->filter_methods[$action] = [];
    }

    $this->filters[$action][] = $action;

    $this->filter_objects[$action][] = $object;
    $this->filter_methods[$action][] = $method;
  }
}

?>
