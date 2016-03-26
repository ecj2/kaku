<?php

class Output extends Utility {

  private $search;
  private $replace;

  private $class_name;
  private $class_file;

  private $Database;

  public function __construct() {

    $this->search = [];
    $this->replace = [];

    $this->class_name = [];
    $this->class_file = [];
  }

  public function flushBuffer() {

    // Flush contents of buffer to the page.
    ob_get_flush();
  }

  public function replaceTags() {

    global $Hook;

    // Get the tag recursion depth.
    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'recursion_depth'
      ORDER BY id DESC
    ";

    $query = $this->Database->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or returned zero rows.
      Utility::displayError("failed to get recursion depth");
    }

    $recursion_depth = $query->fetch(PDO::FETCH_OBJ)->body;

    for ($i = 0; $i < $recursion_depth; ++$i) {

      // Get tags from database.
      $statement = "SELECT * FROM " . DB_PREF . "tags ORDER BY id DESC";

      $query = $this->Database->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or returned zero rows.
        Utility::displayError("failed to replace tags");
      }

      while ($tag = $query->fetch(PDO::FETCH_OBJ)) {

        $buffer_contents = $this->replaceBufferContents(ob_get_contents());

        if (strpos($buffer_contents, $tag->title) !== false) {

          // Replace tag call with value from database.

          $Hook->addAction("{$tag->title}", $tag->body);

          $this->addTagReplacement(

            $tag->title,

            $Hook->doAction("{$tag->title}")
          );
        }
      }
    }

    // Find all unfilled tags.
    preg_match_all(

      "/\{\%(.*?)\%\}/",

      $this->replaceBufferContents(ob_get_contents()),

      $matches
    );

    for ($i = 0; $i < count($matches[1]); ++$i) {

      // Replace unfilled tags with hook actions (if applicable).

      $Hook->addAction(

        $matches[1][$i],

        "{%" . $matches[1][$i] . "%}"
      );

      $this->addTagReplacement(

        $matches[1][$i],

        $Hook->doAction($matches[1][$i])
      );
    }
  }

  public function startBuffer() {

    ob_start(

      [
        $this,

        "replaceBufferContents"
      ]
    );
  }

  public function loadExtensions() {

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
          $this->class_name[] = $class_name;
          $this->class_file[] = $extension_full_path;
        }

        $position = array_search($extension_full_path, $this->class_file);

        $class_name = $this->class_name[$position];

        // Determine if the given extension has been activated.
        $statement = "

          SELECT activate
          FROM " . DB_PREF . "extensions
          WHERE title = '{$class_name}'
        ";

        $query = $this->Database->query($statement);

        if (!$query || $query->rowCount() == 0) {

          // Failed to determine activation status.
          continue;
        }

        // Fetch result as an object.
        $result = $query->fetch(PDO::FETCH_OBJ);

        // Get activation status.
        $activation_status = $result->activate;

        if (!$activation_status) {

          // Extension has not been activated; skip it.
          continue;
        }

        // Instantiate the extension.
        $Extension = new $class_name;

        if (method_exists($class_name, "setDatabaseHandle")) {

          // Pass the database handle over to the extension.
          $Extension->setDatabaseHandle($this->Database);
        }

        if (method_exists($class_name, "manageHooks")) {

          $Extension->manageHooks();
        }
      }
    }
  }

  public function addTagReplacement($tag_title, $replacement) {

    $this->search[] = "{%{$tag_title}%}";
    $this->replace[] = $replacement;
  }

  public function setDatabaseHandle($DatabaseHandle) {

    $this->Database = $DatabaseHandle;
  }

  public function replaceBufferContents($contents) {

    static $first_pass = true;

    // Replace tags in buffer.
    $contents = str_replace(

      $this->search,

      $this->replace,

      $contents
    );

    if ($first_pass) {

      $first_pass = false;

      $this->replaceTags();
    }

    return $contents;
  }
}

?>
