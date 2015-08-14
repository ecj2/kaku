<?php

class Hook {

  private $action_types;
  private $actions;
  private $filters;
  private $action_objects;
  private $action_methods;
  private $action_arguments;

  public function __construct() {

    $this->action_types = array();
    $this->actions = array();
    $this->action_objects = array();
    $this->action_methods = array();
    $this->action_arguments = array();
  }

  public function doAction($action) {

    if (in_array($action, $this->actions)) {

      if ($this->action_types[$action] == "string") {

        return $this->action_objects[$action];
      }
      else {

        return call_user_func(

          array(

            $this->action_objects[$action],

            $this->action_methods[$action]
          ),

          $this->action_arguments[$action]
        );
      }
    }
  }

  public function addAction($action, $object, $method = "", $argument = "") {

    if (!in_array($action, $this->actions)) {

      $this->actions[$action] = $action;
      $this->action_objects[$action] = $object;
      $this->action_methods[$action] = $method;
      $this->action_arguments[$action] = $argument;

      if (is_string($object)) {

        $this->action_types[$action] = "string";
      }
      else {

        $this->action_types[$action] = "object";
      }
    }
  }

  public function hasAction($action) {

    if (in_array($action, $this->actions)) {

      return true;
    }
    else {

      return false;
    }
  }

  public function hasFilter($filter) {

    return array_key_exists($filter, $this->filters);
  }

  public function removeAction($action) {

    if (in_array($action, $this->actions)) {

      unset($this->actions[$action]);
    }
  }
}

?>
