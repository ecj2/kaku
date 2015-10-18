<?php

// Prevent direct access to this file.
if (!defined("KAKU_INCLUDE")) exit();

class Theme extends Utility {

  private $DatabaseHandle;

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

    $theme_directory = "content/themes/{$theme_name}";

    // Get files from theme directory.
    $theme_files = scandir($theme_directory);

    // Remove . and .. from array.
    unset($theme_files[0]);
    unset($theme_files[1]);

    // Reset the array key count.
    $theme_files = array_values($theme_files);

    $theme_files_without_extension = [];

    for ($i = 0; $i < count($theme_files); ++$i) {

      // Get file names without extensions.
      $theme_files_without_extension[] = substr(

        $theme_files[$i],

        0,

        strrpos(

          $theme_files[$i],

          "."
        )
      );
    }

    if (!in_array($file_name, $theme_files_without_extension)) {

      // Theme file doesn't exist.
      Utility::displayError("{$file_path} does not exist");
    }

    // Get key of file to match with $theme_files array.
    $key = array_search($file_name, $theme_files_without_extension);

    // Get the file name with the extension.
    $file_name = $theme_files[$key];

    // Return contents of theme file.
    return file_get_contents("{$theme_directory}/{$file_name}");
  }

  public function setDatabaseHandle($Handle) {

    $this->DatabaseHandle = $Handle;
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
