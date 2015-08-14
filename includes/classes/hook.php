<?php

class Hook {

  private $action_types;
  private $actions;
  private $filters;
  private $action_objects;
  private $filter_objects;
  private $action_methods;
  private $filter_methods;
  private $action_arguments;

  public function __construct() {

    $this->action_types = array();
    $this->actions = array();
    $this->filters = array();
    $this->action_objects = array();
    $this->filter_objects = array();
    $this->action_methods = array();
    $this->filter_methods = array();
    $this->action_arguments = array();
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

        for ($i = 0; $i < count($this->filters[$action]); ++$i) {

          $callback = call_user_func(

            array(

              $this->filter_objects[$action][$i],

              $this->filter_methods[$action][$i]
            ),

            $callback
          );
        }

        return $callback;
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

  public function addFilter($action, $object, $method = "") {

    if (!in_array($action, $this->actions)) {

      if (!isset($this->filters[$action])) {

        $this->filters[$action] = array();
      }

      if (!isset($this->filter_objects[$action])) {

        $this->filter_objects[$action] = array();
      }

      if (!isset($this->filter_methods[$action])) {

        $this->filter_methods[$action] = array();
      }

      array_push($this->filters[$action], $action);
      array_push($this->filter_objects[$action], $object);
      array_push($this->filter_methods[$action], $method);
    }
  }

  public function hasAction($action) {

    return array_key_exists($action, $this->actions);
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
