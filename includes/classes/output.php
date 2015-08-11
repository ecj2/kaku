<?php

class Output extends Utility {

  private $search;
  private $replace;

  private $class_name;
  private $class_file;

  private $DatabaseHandle;

  public function __construct() {

    $this->search = array();
    $this->replace = array();

    $this->class_name = array();
    $this->class_file = array();
  }

  public function flushBuffer() {

    // Flush contents of buffer to the page.
    ob_get_flush();
  }

  public function replaceTags() {

    $statement = "

      SELECT body, evaluate
      FROM " . DB_PREF . "tags
      WHERE title = 'recursion_depth'
      ORDER BY id DESC
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or returned zero rows.
      Utility::displayError("failed to get recursion depth");
    }

    $recursion_depth = $query->fetch(PDO::FETCH_OBJ)->body;

    for ($i = 0; $i < $recursion_depth; ++$i) {

      // Get tags from database.
      $statement = "SELECT * FROM " . DB_PREF . "tags ORDER BY id DESC";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or returned zero rows.
        Utility::displayError("failed to replace tags");
      }

      while ($tag = $query->fetch(PDO::FETCH_OBJ)) {

        $buffer_contents = $this->replaceBufferContents(ob_get_contents());

        if (strpos($buffer_contents, $tag->title) !== false) {

          // Replace tag call with value from database.

          if ($tag->evaluate) {

            // Evaluate the tag contents as if it were PHP code.
            $this->addTagReplacement($tag->title, eval($tag->body));
          }
          else {

            // Do not evaluate tag contents as if PHP code.
            $this->addTagReplacement($tag->title, $tag->body);
          }
        }
      }
    }
  }

  public function startBuffer() {

    ob_start(array($this, "replaceBufferContents"));
  }

  public function loadExtensions() {

    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'recursion_depth'
      ORDER BY id DESC
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or returned zero rows.
      Utility::displayError("failed to get recursion depth");
    }

    $recursion_depth = $query->fetch(PDO::FETCH_OBJ)->body;

    for ($i = 0; $i < $recursion_depth; ++$i) {

      // Get extension directories.
      $directories = glob("content/extensions/*", GLOB_ONLYDIR);

      foreach ($directories as $directory) {

        // Get directory name without path.
        $directory_name = str_replace("content/extensions/", "", $directory);

        $extension_full_path = "{$directory}/{$directory_name}.php";

        if (file_exists($extension_full_path)) {

          // Get the names of already declared classes.
          $classes = get_declared_classes();

          // Require extension source file.
          require_once $extension_full_path;

          // Get name of newly required class.
          $class_name = reset(array_diff(get_declared_classes(), $classes));

          if (!in_array($class_name, $this->class_name)) {

            // Save class name and file path.
            array_push($this->class_name, $class_name);
            array_push($this->class_file, $extension_full_path);
          }

          $position = array_search($extension_full_path, $this->class_file);

          $class_name = $this->class_name[$position];

          // Instantiate the extension.
          $Extension = new $class_name;

          if (method_exists($class_name, "setDatabaseHandle")) {

            // Pass the database handle over to the extension.
            $Extension->setDatabaseHandle($this->DatabaseHandle);
          }

          if (method_exists($class_name, "getTags")) {

            if (method_exists($class_name, "getReplacements")) {

              // Identify and replace the tags called by the extension.

              foreach ($Extension->getTags() as $key) {

                array_push($this->search, "{%{$key}%}");
              }

              foreach ($Extension->getReplacements() as $key) {

                array_push($this->replace, $key);
              }
            }
          }
        }
      }
    }
  }

  public function addTagReplacement($tag_title, $replacement) {

    array_push($this->search, "{%{$tag_title}%}");
    array_push($this->replace, $replacement);
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  public function replaceBufferContents($contents) {

    // Compress final output by removing new lines and double spaces.
    return str_replace(

      array(

        "\n",

        "  "
      ),

      "",

      str_replace(

        $this->search,

        $this->replace,

        $contents
      )
    );
  }
}

?>
