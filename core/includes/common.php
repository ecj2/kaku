<?php

require "config.php";

$classes = glob(KAKU_ROOT . "/core/classes/*.php");

// Sort classes in reverse alphabetical order.
rsort($classes);

foreach ($classes as $class) {

  $declared_classes = get_declared_classes();

  require $class;

  $class_difference = array_diff(get_declared_classes(), $declared_classes);

  $class_name = reset($class_difference);

  // Instantiate each class as an object, and name the object using the class name.
  $$class_name = new $class_name;
}

$Buffer->start();

require KAKU_ROOT . "/core/includes/functions.php";

if (file_exists(KAKU_ROOT . "/core/includes/install.php")) {

  // Install Kaku.
  require KAKU_ROOT . "/core/includes/install.php";
}

// Clear these tags if they go unused.
$Hook->addAction("body_content", "");
$Hook->addAction("head_content", "");

// Replace the protocol with the appropriate value.
$Hook->addAction("protocol", $Utility->getProtocol());

?>
