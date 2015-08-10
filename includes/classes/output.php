<?php

class Output extends Utility {

  private $search;
  private $replace;

  private $DatabaseHandle;

  private $required_extensions;

  public function __construct() {

    $this->search = array();
    $this->replace = array();

    // List of already required extensions.
    $this->required_extensions = array();
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

        if (file_exists("{$directory}/{$directory_name}.php")) {

          // Get the names of already declared classes.
          $classes = get_declared_classes();

          if (!in_array($directory_name, $this->required_extensions)) {

            // Require extension source file.
            require "{$directory}/{$directory_name}.php";

            // Add extension to the list, so it won't be required again.
            array_push($this->required_extensions, $directory_name);
          }

          // Get name of newly required class.
          $class_name = reset(array_diff(get_declared_classes(), $classes));

          if (class_exists($class_name)) {

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
