<?php

class Hook {

  private $action_types;
  private $filter_types;
  private $actions;
  private $filters;
  private $action_objects;
  private $filter_objects;
  private $action_methods;
  private $filter_methods;
  private $action_arguments;
  private $filter_arguments;

  public function __construct() {

    $this->action_types = array();
    $this->filter_types = array();
    $this->actions = array();
    $this->filters = array();
    $this->action_objects = array();
    $this->filter_objects = array();
    $this->action_methods = array();
    $this->filter_methods = array();
    $this->action_arguments = array();
    $this->filter_arguments = array();
  }

  public function doAction($action) {

    if (in_array($action, $this->actions)) {

      if ($this->hasFilter($action)) {

        $callback = null;

        if ($this->action_types[$action] == "string") {

          $callback = $this->action_objects[$action];
        }
        else {

          $callback = call_user_func(

            array(

              $this->action_objects[$action],

              $this->action_methods[$action]
            ),

            $this->action_arguments[$action]
          );
        }

        return call_user_func(

          array(

            $this->filter_objects[$action],

            $this->filter_methods[$action]
          ),

          $callback
        );
      }
      else {

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

  public function addFilter($action, $object, $method = "", $argument = "") {

    if (!in_array($action, $this->actions)) {

      $this->filters[$action] = $action;
      $this->filter_objects[$action] = $object;
      $this->filter_methods[$action] = $method;
      $this->filter_arguments[$action] = $argument;

      if (is_string($object)) {

        $this->filter_types[$action] = "string";
      }
      else {

        $this->filter_types[$action] = "object";
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

  public function removeFilter($filter) {

    if (in_array($filter, $this->filters)) {

      unset($this->filters[$filter]);
    }
  }
}

?>
