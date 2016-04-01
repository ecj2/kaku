<?php

require "configuration.php";

foreach (glob(KAKU_ROOT . "/core/classes/*.php") as $class) {

  $declared_classes = get_declared_classes();

  // Load the class file.
  require $class;

  $class_difference = array_diff(get_declared_classes(), $declared_classes);

  // Get the name of the freshly-required class.
  $class_name = reset($class_difference);

  // Instantiate each of the class files.
  $$class_name = new $class_name;
}

?>
