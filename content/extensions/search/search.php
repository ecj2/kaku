<?php

// Prevent direct access to this file.
if (!defined("KAKU_EXTENSION")) exit();

$name = "Simple Search";

class Search {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function getTags() {

    return array(

      "search"
    );
  }

  public function getReplacements() {

    return array(

      $this->manageSearch()
    );
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  private function manageSearch() {

    if (isset($_GET["term"])) {

      // Remove whitespace from beginning and end of search term.
      $_GET["term"] = trim($_GET["term"]);

      // Select from posts and pages where something is like the search term.
      $statement = "

        (
          SELECT url, title, description, 'posts' AS table_name
          FROM " . DB_PREF . "posts
          WHERE draft = '0' AND (keywords LIKE ? OR title LIKE ?)
        )

        UNION ALL

        (
          SELECT url, title, description, 'pages' AS table_name
          FROM " . DB_PREF . "pages
          WHERE show_on_search = '1' AND (keywords LIKE ? OR title LIKE ?)
        )

        ORDER BY title ASC
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      $search_term = "%{$_GET["term"]}%";

      // Prevent SQL injections.
      $query->bindParam(1, $search_term);
      $query->bindParam(2, $search_term);
      $query->bindParam(3, $search_term);
      $query->bindParam(4, $search_term);

      $query->execute();

      if (!$query) {

        // Query failed.
        return "An error occurred.";
      }
      else if ($query->rowCount() == 0) {

        // Query returned zero rows.
        $markup = "No results found for \"{$_GET["term"]}\".<br><br>";

        $markup .= "<form method=\"get\">";
        $markup .= "<input type=\"search\" name=\"term\">";
        $markup .= "<input type=\"submit\" value=\"Search\">";
        $markup .= "</form>";

        return $markup;
      }
      else {

        $markup = "";

        if ($query->rowCount() == 1) {

          // One result found.
          $markup .= "{$query->rowCount()} result found ";
        }
        else {

          // Multiple results found.
          $markup .= "{$query->rowCount()} results found ";
        }

        $markup .= "for \"{$_GET["term"]}\":";

        $markup .= "<br><br><form style=\"display: inline;\" ";
        $markup .= "method=\"get\"><input type=\"search\" name=\"term\">";
        $markup .= "<input type=\"submit\" value=\"Search\">";
        $markup .= "</form>";

        while ($result = $query->fetch(PDO::FETCH_OBJ)) {

          // Get a link and description for each search result.

          $markup .= "<br><br><a href=\"{%blog_url%}/";

          if ($result->table_name == "pages") {

            // Result is a page.
            $markup .= "page/{$result->url}\">";
          }
          else {

            // Result is a post.
            $markup .= "post/{$result->url}\">";
          }

          $markup .= "{$result->title}</a><br>";

          if (empty($result->description)) {

            // The resource has no description.
            $markup .= "No description.";
          }
          else {

            // The resource has a description.
            $markup .= "{$result->description}";
          }
        }

        // Display the results.
        return $markup;
      }
    }
    else {

      // Display the search form.

      $markup = "Use the form below to search for posts and pages.<br><br>";

      $markup .= "<form method=\"get\">";
      $markup .= "<input type=\"search\" name=\"term\">";
      $markup .= "<input type=\"submit\" value=\"Search\">";
      $markup .= "</form>";

      return $markup;
    }
  }
}

?>
