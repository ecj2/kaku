<?php

class Hook {

  private $types;
  private $actions;
  private $filters;
  private $objects;
  private $methods;
  private $arguments;

  public function __construct() {

    $this->types = array();
    $this->actions = array();
    $this->objects = array();
    $this->methods = array();
    $this->arguments = array();
  }

  public function doAction($action) {

    if (in_array($action, $this->actions)) {

      if ($this->types[$action] == "string") {

        return $this->objects[$action];
      }
      else {

        return call_user_func(

          array(

            $this->objects[$action],

            $this->methods[$action]
          ),

          $this->arguments[$action]
        );
      }
    }
  }

  public function addAction($action, $object, $method = "", $argument = "") {

    if (!in_array($action, $this->actions)) {

      $this->actions[$action] = $action;
      $this->objects[$action] = $object;
      $this->methods[$action] = $method;
      $this->arguments[$action] = $argument;

      if (is_string($object)) {

        $this->types[$action] = "string";
      }
      else {

        $this->types[$action] = "object";
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
