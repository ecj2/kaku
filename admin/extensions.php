<?php

// Allow access to include files.
define("KAKU_INCLUDE", true);

session_start();

if (!isset($_SESSION["username"])) {

  header("Location: ./login.php");
}

require "../includes/configuration.php";

require "../includes/classes/utility.php";
require "../includes/classes/database.php";

require "../includes/classes/hook.php";
require "../includes/classes/output.php";

global $Hook;

$Hook = new Hook;

$Output = new Output;
$Utility = new Utility;
$Database = new Database;

$Database->connect();

$Output->setDatabaseHandle($Database->getHandle());

$Output->startBuffer();

$statement = "

  SELECT body
  FROM " . DB_PREF . "tags
  WHERE title = 'admin_theme_name'
  ORDER BY id DESC
";

$query = $Database->getHandle()->query($statement);

if (!$query || $query->rowCount() == 0) {

  // Query failed or returned zero rows.
  $Utility->displayError("failed to get admin theme name");
}

// Get the admin theme name.
$theme_name = $query->fetch(PDO::FETCH_OBJ)->body;

if (!file_exists("content/themes/{$theme_name}/template.html")) {

  // Theme template does not exist.
  $Utility->displayError("theme template file does not exist");
}

// Display the theme contents.
echo file_get_contents("content/themes/{$theme_name}/template.html");

$page_body = "";

$page_title = "Extensions";

// Get extension directories.
$directories = glob("../content/extensions/*", GLOB_ONLYDIR);

if (count($directories > 0)) {

  $page_body .= "<table class=\"extensions\">";

  $page_body .= "<tr>";
  $page_body .= "<th>Title</th>";
  $page_body .= "<th>Action</th>";
  $page_body .= "</tr>";

  foreach ($directories as $directory) {

    // Get directory name without path.
    $directory_name = str_replace("../content/extensions/", "", $directory);

    $extension_full_path = "{$directory}/{$directory_name}.php";

    if (file_exists($extension_full_path)) {

      // Get the names of already declared classes.
      $classes = get_declared_classes();

      // Require extension source file.
      require_once $extension_full_path;

      // Get name of newly required class.
      $class_name = reset(array_diff(get_declared_classes(), $classes));

      if (strlen($class_name) == 0) {

        continue;
      }

      $statement = "

        SELECT activate
        FROM " . DB_PREF . "extensions
        WHERE title = '{$class_name}'
      ";

      $query = $Database->getHandle()->query($statement);

      $activation_status = false;

      if (!$query) {

        // Query failed.
      }
      else if ($query->rowCount() == 0) {

        // Extension does not exist in database.
      }
      else {

        // Fetch result as object.
        $result = $query->fetch(PDO::FETCH_OBJ);

        // Get activation status.
        $activation_status = $result->activate;
      }

      $page_body .= "<tr>";

      if (isset($name)) {

        $page_body .= "<td>{$name}</td>";

        unset($name);
      }
      else {

        $page_body .= "<td>{$class_name}</td>";
      }

      if ($activation_status) {

        $page_body .= "
          <td>
            <a href=\"deactivate_extension.php?title={$class_name}\">
              Deactivate
            </a>
          </td>
        ";
      }
      else {

        $page_body .= "
          <td>
            <a href=\"activate_extension.php?title={$class_name}\">
              Activate
            </a>
          </td>
        ";
      }

      $page_body .= "</tr>";
    }
  }

  $page_body .= "</table>";
}
else {

  $page_body = "There aren't any extensions.";
}

$Output->addTagReplacement(

  "page_body",

  $page_body
);

$Output->addTagReplacement(

  "page_title",

  $page_title
);

$Output->replaceTags();

$Output->flushBuffer();

$Database->disconnect();

?>
