<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class Output {

  private $search;
  private $replace;

  public function __construct() {

    $this->search = [];
    $this->replace = [];
  }

  public function startBuffer() {

    // Start the output buffer.
    ob_start(

      [
        $this,

        "replaceBufferContents"
      ]
    );
  }

  public function flushBuffer() {

    // Flush the contents of the buffer to the page.
    ob_get_flush();
  }

  public function replaceTags() {

    // Select the recursion depth tag.
    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'recursion_depth'
      ORDER BY id DESC
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if (!$Query) {

      // Something went wrong.
      $GLOBALS["Utility"]->displayError("failed to select recursion_depth");
    }

    if ($Query->rowCount() == 0) {

      // The recursion_depth tag does not exit.
      $GLOBALS["Utility"]->displayError("recursion_depth tag does not exist");
    }

    // Get the recursion depth.
    $recursion_depth = $Query->fetch(PDO::FETCH_OBJ)->body;

    for ($i = 0; $i < $recursion_depth; ++$i) {

      // Select tags from the database.
      $statement = "

        SELECT title, body
        FROM " . DB_PREF . "tags
        ORDER BY id DESC
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select tags");
      }

      if ($Query->rowCount() == 0) {

        // The tags do not exist.
        $GLOBALS["Utility"]->displayError("tags do not exist");
      }

      while ($Tag = $Query->fetch(PDO::FETCH_OBJ)) {

        // Get the contents of the buffer.
        $buffer_contents = $this->replaceBufferContents(ob_get_contents());

        if (strpos($buffer_contents, $Tag->title) !== false) {

          // Replace tag calls with values from the database.

          $GLOBALS["Hook"]->addAction(

            $Tag->title,

            $Tag->body
          );

          $this->addTagReplacement(

            $Tag->title,

            $GLOBALS["Hook"]->doAction($Tag->title)
          );
        }
      }
    }

    // Find unfilled tags.
    preg_match_all(

      "/\{\%(.*?)\%\}/",

      $this->replaceBufferContents(ob_get_contents()),

      $matches
    );

    for ($i = 0; $i < count($matches[1]); ++$i) {

      // Replaced unfilled tags with hook actions.

      $GLOBALS["Hook"]->addAction(

        $matches[1][$i],

        "{%" . $matches[1][$i] . "%}"
      );

      $this->addTagReplacement(

        $matches[1][$i],

        $GLOBALS["Hook"]->doAction($matches[1][$i])
      );
    }
  }

  public function loadExtensions() {

    // Get a list of the extension directories.
    $directories = glob(KAKU_ROOT . "/extensions/*", GLOB_ONLYDIR);

    foreach ($directories as $directory) {

      // Get the directory name without the path.
      $directory_name = str_replace(KAKU_ROOT . "/extensions/", "", $directory);

      $extension_full_path = "{$directory}/{$directory_name}.php";

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

          SELECT activate
          FROM " . DB_PREF . "extensions
          WHERE title = '{$class_name}'
        ";

        $Query = $GLOBALS["Database"]->getHandle()->query($statement);

        if (!$Query || $Query->rowCount() == 0) {

          // Failed to determine activation status.
          continue;
        }

        // Fetch the result as an object.
        $Result = $Query->fetch(PDO::FETCH_OBJ);

        // Get the extension's activation status.
        $activation_status = $Result->activate;

        if (!$activation_status) {

          // The extension has not been activated.
          continue;
        }

        // Instantiate the extension.
        $Extension = new $class_name;
      }
    }
  }

  public function addTagReplacement($tag_title, $replacement) {

    // The supplied tag will be replaced later when the buffer is flushed.
    $this->search[] = "{%{$tag_title}%}";
    $this->replace[] = $replacement;
  }

  public function replaceBufferContents($buffer_contents) {

    static $passes = 0;

    // Replace tags in buffer.
    $buffer_contents = str_replace(

      $this->search,

      $this->replace,

      $buffer_contents
    );

    if ($passes < 2) {

      ++$passes;

      // Apply recursion depth to hook-replaced tags.
      $this->replaceTags();
    }

    return $buffer_contents;
  }
}

?>
