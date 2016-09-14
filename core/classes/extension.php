<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Extension {

  private $name;

  public function __construct() {

    $this->name = "Untitled";
  }

  public function getName() {

    return $this->name;
  }

  public function setName($name) {

    $this->name = $name;
  }
}

?>
