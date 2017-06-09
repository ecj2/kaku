<?php

require "common.php";

// Get extension directories.
$directories = glob("../extensions/*", GLOB_ONLYDIR);

if (isset($_GET["code"]) && isset($_GET["message"])) {

  if ($_GET["code"] == 0) {

    // Failure notice.
    $body .= "<span class=\"failure\">Notice: ";
  }
  else if ($_GET["code"] == 1) {

    // Success notice.
    $body .= "<span class=\"success\">Notice: ";
  }

  // Encode { and } to prevent them from being replaced by the output buffer.
  $body .= str_replace(["{", "}"], ["&#123;", "&#125;"], $_GET["message"]) . ".</span>";
}

if (count($directories) > 0) {

  $body .= "

    Extensions are displayed below.<br><br>

    <table class=\"extensions two-column\">
      <tr>
        <th>Title</th>
        <th>Action</th>
      </tr>
  ";

  foreach ($directories as $directory) {

    // Get directory name without path.
    $directory_name = str_replace("../extensions/", "", $directory);

    $extension_full_path = "{$directory}/{$directory_name}.php";

    if (file_exists($extension_full_path)) {

      // Get the names of already declared classes.
      $classes = get_declared_classes();

      // Require extension source file.
      require $extension_full_path;

      $classes = array_diff(get_declared_classes(), $classes);

      // Get name of newly required class.
      $class_name = reset($classes);
    }

    $statement = "

        SELECT status
        FROM " . DB_PREF . "extensions
        WHERE hash = '" . md5($class_name) . "'
        ORDER BY id DESC
        LIMIT 1
      ";

      $Query = $Database->getHandle()->query($statement);

      $activation_status = false;

      if (!$Query) {

        // Failed to get activation status
        $Utility->displayError("failed to select extension activation status");
      }

      if ($Query->rowCount() > 0) {

        // Get activation status.
        $activation_status = $Query->fetch(PDO::FETCH_OBJ)->status;
      }

      $body .= "<tr>";

      $Extension = new $class_name;

      $name = $Extension->getName();

      if (strlen(trim($name)) == 0) {

        // Use class name.
        $body .= "<td>{$class_name}</td>";
      }
      else {

        // Use given name.
        $body .= "<td>{$name}</td>";
      }

      $message = "";

      if (file_exists("{$directory}/edit.php")) {

        $message = "<a href=\"{%blog_url%}/admin/edit_extension.php?title=";
        $message .= str_replace("../extensions/", "", $directory) . "\">Edit</a> -
        ";
      }

      if ($activation_status) {

        $message .= "

          <a href=\"{%blog_url%}/admin/toggle_extension.php?code=1&title={$class_name}\">Dectivate</a>
        ";
      }
      else {

        $message .= "

          <a href=\"{%blog_url%}/admin/toggle_extension.php?code=0&title={$class_name}\">Activate</a>
        ";
      }

      $body .= "<td>{$message}</td>";

      $body .= "</tr>";
  }

  $body .= "</table>";
}
else {

  $body .= "There are no extensions to display.";
}

$replace[] = "Extensions";
$replace[] = $body;

echo str_replace($search, $replace, $theme);

echo $Buffer->flush();

?>
