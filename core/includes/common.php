<?php

require "configuration.php";

foreach (glob(dirname(dirname(__DIR__)) . "/core/classes/*.php") as $class) {

  $declared_classes = get_declared_classes();

  // Load the class file.
  require $class;

  $class_difference = array_diff(get_declared_classes(), $declared_classes);

  // Get the name of the freshly-required class.
  $class_name = reset($class_difference);

  // Instantiate the class file.
  $$class_name = new $class_name;
}

if (isset($_GET["path"])) {

  // Break the path components up into an array.
  $path = explode("/", $_GET["path"]);

  for ($i = 0; $i < count($path); ++$i) {

    if ($i % 2) {

      continue;
    }

    if (array_key_exists($i + 1, $path)) {

      // Add onto the array using the first part as the key and the second as the value.
      $path[$path[$i]] = $path[$i + 1];
    }
  }

  // Add the assembled path components to the superglobal.
  $_GET += $path;
}

// Replace the protocol with the appropriate value.
$Hook->addAction("protocol", $Utility->getProtocol());

if (file_exists("core/includes/install.php")) {

  // Install Kaku.
  require "core/includes/install.php";
}

?>
