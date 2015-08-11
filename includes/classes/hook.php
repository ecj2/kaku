<?php

class Hook {

  private $actions;
  private $methods;
  private $objects;
  private $arguments;

  public function __construct() {

    $this->actions = array();
    $this->methods = array();
    $this->objects = array();
    $this->arguments = array();
  }

  public function doAction($action) {

    if (in_array($action, $this->actions)) {

      $array_position = array_search($action, $this->actions);

      $method = $this->methods[$array_position];
      $object = $this->objects[$array_position];
      $argument = $this->arguments[$array_position];

      if (method_exists($object, $method)) {

        return call_user_func(array($object, $method), $argument);
      }
    }
  }

  public function addAction($action, $object, $method, $argument = null) {

    if (!in_array($action, $this->actions)) {

      array_push($this->actions, $action);
      array_push($this->methods, $method);
      array_push($this->objects, $object);
      array_push($this->arguments, $argument);
    }
  }

  public function removeAction($action) {

    if (in_array($action, $this->actions)) {

      $array_position = array_search($action, $this->actions);

      unset($this->actions[$array_position]);
    }
  }
}

?>
