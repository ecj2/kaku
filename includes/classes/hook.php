<?php

class Hook {

  private $actions;
  private $methods;
  private $objects;

  public function __construct() {

    $this->actions = array();
    $this->methods = array();
    $this->objects = array();
  }

  public function addAction($action, $object, $method) {

    if (!in_array($action, $this->actions)) {

      array_push($this->actions, $action);
      array_push($this->methods, $method);
      array_push($this->objects, $object);
    }
  }

  public function doAction($action) {

    if (in_array($action, $this->actions)) {

      $array_position = array_search($action, $this->actions);

      $method = $this->methods[$array_position];
      $object = $this->objects[$array_position];

      if (method_exists($object, $method)) {

        $method = get_class($object) . "::" . $method;

        // @TODO: return for output buffer.
        call_user_func($method);
      }
    }
  }
}

?>
