<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Hook {

  private $actions;
  private $filters;
  private $filter_objects;
  private $filter_methods;
  private $action_contents;

  public function __construct() {

    $this->actions = [];
    $this->filters = [];
    $this->filter_objects = [];
    $this->filter_methods = [];
    $this->action_contents = [];
  }

  public function doAction($action) {

    if (in_array($action, $this->actions)) {

      if (array_key_exists($action, $this->filters)) {

        // Get the contents of the original action.
        $callback = $this->action_contents[$action];

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
        return $this->action_contents[$action];
      }
    }
  }

  public function addAction($action, $contents) {

    if (!in_array($action, $this->actions)) {

      $this->actions[$action] = $action;

      $this->action_contents[$action] = $contents;
    }
  }

  public function addFilter($action, $object, $method) {

    if (!isset($this->filters[$action])) {

      $this->filters[$action] = [];
    }

    if (!isset($this->filter_objects[$action])) {

      $this->filter_objects[$action] = [];
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

  public function removeAction($action) {

    if (in_array($action, $this->actions)) {

      unset($this->actions[$action]);
    }
  }
}

?>
