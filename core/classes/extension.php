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

  public function loadExtensions() {

    // Get a list of extension directories.
    $directories = glob(KAKU_ROOT . "/extensions/*", GLOB_ONLYDIR);

    foreach ($directories as $directory) {

      // Get the directory name without the path.
      $directory_name = str_replace(KAKU_ROOT . "/extensions/", "", $directory);

      $extension_full_path = $directory . "/" . $directory_name . ".php";

      if (file_exists($extension_full_path)) {

        // Get the names of previously declared classes.
        $declared_classes = get_declared_classes();

        // Load extension source file.
        require $extension_full_path;

        $class_difference = array_diff(get_declared_classes(), $declared_classes);

        // Get the name of the freshly-required class.
        $class_name = reset($class_difference);

        // Determine if the given extension has been activated.
        $statement = "

          SELECT status
          FROM " . DB_PREF . "extensions
          WHERE hash = '" . md5($class_name) . "'
          ORDER BY id DESC
          LIMIT 1
        ";

        $Query = $GLOBALS["Database"]->getHandle()->query($statement);

        if (!$Query || $Query->rowCount() == 0) {

          // Failed to determine activation status.
          continue;
        }

        // Fetch the result as an object.
        $Result = $Query->fetch(PDO::FETCH_OBJ);

        if (!$Result->status) {

          // The extension has not been activated.
          continue;
        }

        // Instantiate the extension.
        $Extension = new $class_name;
      }
    }
  }
}

?>
