<?php

class Hook {

  private $actions;
  private $methods;
  private $objects;
  private $arguments;
  private $callback_contents;

  public function __construct() {

    $this->actions = array();
    $this->methods = array();
    $this->objects = array();
    $this->arguments = array();
    $this->callback_contents = array();
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

      $callback_content = call_user_func(array($object, $method), $argument);

      array_push($this->callback_contents, $callback_content);
    }
  }

  public function hasAction($action) {

    if (in_array($action, $this->actions)) {

      return true;
    }

    return false;
  }

  public function getCallback($action) {

    if (in_array($action, $this->actions)) {

      $array_position = array_search($action, $this->actions);

      return $this->callback_contents[$array_position];
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
