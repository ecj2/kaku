<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Template {

  public function getFileContents($file_name, $echo_contents = false, $view = 0) {

    if ($view == 0) {

      // Select the template name for the front end.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'template_name'
        ORDER BY id DESC
      ";
    }
    else if ($view == 1) {

      // Select the template name for the back end.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'admin_template_name'
        ORDER BY id DESC
      ";
    }

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if (!$Query) {

      // Something went wrong.
      $GLOBALS["Utility"]->displayError("failed to select template name");
    }

    if ($Query->rowCount() == 0) {

      if ($view == 0) {

        // The template_name tag does not exist.
        $GLOBALS["Utility"]->displayError("template_name tag does not exist");
      }
      else if ($view == 1) {

        // The admin_template_name tag does not exist.
        $GLOBALS["Utility"]->displayError("admin_template_name tag does not exist");
      }
    }

    // Fetch the result as an object.
    $Result = $Query->fetch(PDO::FETCH_OBJ);

    // Get the template name.
    $template_name = $Result->body;

    // Replace nested tags in the template name.
    $template_name = $GLOBALS["Utility"]->replaceNestedTags($template_name);

    if ($view == 0) {

      $template_directory = "./templates/{$template_name}";
    }
    else if ($view == 1) {

      $template_directory = "./templates/{$template_name}";
    }

    // Get files from the template directory.
    $template_files = scandir($template_directory);

    // Remove . and .. from array.
    unset($template_files[0]);
    unset($template_files[1]);

    // Reset the array key count.
    $template_files = array_values($template_files);

    $template_files_without_extension = [];

    for ($i = 0; $i < count($template_files); ++$i) {

      // Get file names without extensions.
      $template_files_without_extension[] = substr(

        $template_files[$i],

        0,

        strrpos(

          $template_files[$i],

          "."
        )
      );
    }

    if (!in_array($file_name, $template_files_without_extension)) {

      // The template file does not exist.
      $GLOBALS["Utility"]->displayError("{$template_directory}/{$file_name} does not exist");
    }

    // Get the key of the file to match with the template_files array.
    $key = array_search($file_name, $template_files_without_extension);

    // Make a copy of the file name without its extension.
    $file_name_no_extension = $file_name;

    // Get the file name with the extension.
    $file_name = $template_files[$key];

    // Begin a temporary buffer for the template file.
    ob_start();

    // Require the template file to automatically parse any PHP code.
    require "{$template_directory}/{$file_name}";

    $template_file_contents = ob_get_contents();

    // End and erase the temporary buffer.
    ob_end_clean();

    // Add an action to the contents of the file to allow extensions to manipulate it.
    $GLOBALS["Hook"]->addAction(

      "{$file_name_no_extension}_file_contents",

      $template_file_contents
    );

    if ($echo_contents) {

      // Echo contents of template file.
      echo $GLOBALS["Hook"]->doAction("{$file_name_no_extension}_file_contents");
    }
    else {

      // Return contents of template file.
      return $GLOBALS["Hook"]->doAction("{$file_name_no_extension}_file_contents");
    }
  }
}

?>
