<?php

class Theme extends Utility {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function getFileContents($file_name) {

    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'theme_name'
      ORDER BY id DESC
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or theme_name doesn't exist.
      Utility::displayError("failed to get theme name");
    }

    // Fetch the result as an object.
    $row = $query->fetch(PDO::FETCH_OBJ);

    // Get the theme name.
    $theme_name = $row->body;

    $file_path = "content/themes/{$theme_name}/{$file_name}";

    if (file_exists($file_path)) {

      // Return contents of theme file.
      return file_get_contents($file_path);
    }
    else {

      // Theme file doesn't exist.
      Utility::displayError("{$file_path} does not exist");
    }
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  public function getNavigationItems() {

    $statement = "

      SELECT uri, title, target
      FROM " . DB_PREF . "links
      ORDER BY id ASC
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get navigation items");
    }

    if ($query->rowCount() > 0) {

      $markup = "<ul>";

      while ($link = $query->fetch(PDO::FETCH_OBJ)) {

        // Create a list item for each link.
        $markup .= "<li>";
        $markup .= "<a href=\"{$link->uri}\" target=\"{$link->target}\">";
        $markup .= "{$link->title}</a>";
        $markup .= "</li>";
      }

      $markup .= "</ul>";

      return $markup;
    }

    return;
  }
}

?>
