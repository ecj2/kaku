<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Theme {

  // @TODO: Add code for retrieving back-end themes.
  // @TODO: Maybe pass a $path variable?

  public function getFileContents($file_name) {

    // Get the front-end theme name.
    $theme_name = $GLOBALS["Utility"]->getTag("front_theme_name");

    $theme_directory = KAKU_ROOT . "/themes/{$theme_name}";

    // Get a list of the files from the theme directory.
    $theme_files = scandir($theme_directory);

    // Remove . and .. from the array.
    unset($theme_files[0]);
    unset($theme_files[1]);

    // Reset the array key count.
    $theme_files = array_values($theme_files);

    $theme_files_without_extension = [];

    for ($i = 0; $i < count($theme_files); ++$i) {

      // Get file names without extensions.
      $theme_files_without_extension[] = substr($theme_files[$i], 0, strrpos($theme_files[$i], "."));
    }

    if (!in_array($file_name, $theme_files_without_extension)) {

      // The theme file does not exist.
      $GLOBALS["Utility"]->displayError("theme file '{$theme_name}/{$file_name}' does not exist");
    }

    // Get the key of the file to match with the theme_files array.
    $key = array_search($file_name, $theme_files_without_extension);

    // Make a copy of the file name without its extension.
    $file_name_no_extension = $file_name;

    // Get the file name with the extension.
    $file_name = $theme_files[$key];

    // Begin a temporary buffer for the theme file.
    ob_start();

    // Require the theme file to automatically parse any PHP code.
    require "{$theme_directory}/{$file_name}";

    $theme_file_contents = ob_get_contents();

    // End and erase the temporary buffer.
    ob_end_clean();

    // Add an action to the contents of the file to allow extensions to manipulate it.
    $GLOBALS["Hook"]->addAction("{$file_name_no_extension}_file_contents", $theme_file_contents);

    return $GLOBALS["Hook"]->doAction("{$file_name_no_extension}_file_contents");
  }
}

?>
