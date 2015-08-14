<?php

class Hook {

  private $actions;
  private $objects;
  private $callback_contents;

  public function __construct() {

    $this->actions = array();
    $this->objects = array();
    $this->callback_contents = array();
  }

  public function doAction($action) {

    if (in_array($action, $this->actions)) {

      $callback_content = $this->callback_contents[$action];

      return $this->callback_contents[$action];
    }
  }

  public function addAction($action, $object, $method = "", $argument = "") {

    if (!in_array($action, $this->actions)) {

      if (is_string($object)) {

        $this->callback_contents[$action] = $object;
      }
      else if (is_object($object)) {

        $callback_content = call_user_func(array($object, $method), $argument);

        $this->callback_contents[$action] = $callback_content;
      }

      $this->actions[$action] = $action;
      $this->objects[$action] = $object;
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
