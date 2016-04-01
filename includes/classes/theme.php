<?php

class Theme extends Utility {

  private $Database;

  public function __construct() {

    //
  }

  public function getFileContents($file_name) {

    // Select the theme name.
    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'theme_name'
      ORDER BY id DESC
    ";

    $query = $this->Database->query($statement);

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
      Utility::displayError("{$theme_directory}/{$file_name} does not exist");
    }

    // Get key of file to match with $theme_files array.
    $key = array_search($file_name, $theme_files_without_extension);

    // Get the file name with the extension.
    $file_name = $theme_files[$key];

    // Begin a temporary buffer for the theme file.
    ob_start();

    // Require the theme file to automatically parse any PHP code.
    require "{$theme_directory}/{$file_name}";

    $theme_file_contents = ob_get_contents();

    // End and erase the temporary buffer.
    ob_end_clean();

    // Return contents of theme file.
    return $theme_file_contents;
  }

  public function setDatabaseHandle($DatabaseHandle) {

    $this->Database = $DatabaseHandle;
  }
}

?>
