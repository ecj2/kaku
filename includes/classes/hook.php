<?php

class Hook {

  private $types;
  private $actions;
  private $objects;
  private $methods;
  private $arguments;
  private $callback_contents;

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

      if (is_string($object)) {

        $this->types[$action] = "string";
      }
      else {

        $this->types[$action] = "object";
      }

      $this->actions[$action] = $action;
      $this->objects[$action] = $object;
      $this->methods[$action] = $method;
      $this->arguments[$action] = $argument;
    }
  }

  public function hasAction($action) {

    if (in_array($action, $this->actions)) {

      return true;
    }

    return false;
  }

  public function removeAction($action) {

    if (in_array($action, $this->actions)) {

      unset($this->actions[$action]);
    }
  }
}

?>
