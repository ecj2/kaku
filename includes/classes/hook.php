<?php

class Hook {

  private $types;
  private $actions;
  private $methods;
  private $objects;
  private $arguments;
  private $callback_contents;

  public function __construct() {

    $this->types = array();
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

      if ($this->types[$array_position] == "string") {

        return $object;
      }
      else if ($this->types[$array_position] == "object") {

        if (method_exists($object, $method)) {

          return call_user_func(array($object, $method), $argument);
        }
      }
    }
  }

  public function addAction($action, $object, $method = "", $argument = "") {

    if (!in_array($action, $this->actions)) {

      if (is_string($object)) {

        $this->types[] = "string";

        $this->callback_contents[] = $object;
      }
      else if (is_object($object)) {

        $this->types[] = "object";

        $callback_content = call_user_func(array($object, $method), $argument);

        $this->callback_contents[] = $callback_content;
      }

      $this->actions[] = $action;
      $this->methods[] = $method;
      $this->objects[] = $object;
      $this->arguments[] = $argument;
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
