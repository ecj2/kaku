<?php

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

      //
      $statement = "

        (
          SELECT url, title, description, 'posts' AS table_name
          FROM " . DB_PREF . "posts
          WHERE draft = '0' AND (tags LIKE ? OR title LIKE ?)
        )

        UNION ALL

        (
          SELECT url, title, description, 'pages' AS table_name
          FROM " . DB_PREF . "pages
          WHERE show_on_search = '1' AND (tags LIKE ? OR title LIKE ?)
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

      if ($query->rowCount() == 0) {

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

          $markup .= "<br><br><a href=\"{%blog_url%}/";

          if ($result->table_name == "pages") {

            //
            $markup .= "page/{$result->url}\">";
          }
          else {

            //
            $markup .= "post/{$result->url}\">";
          }

          $markup .= "{$result->title}</a><br>";

          if (empty($result->description)) {

            $markup .= "No description.";
          }
          else {

            $markup .= "{$result->description}";
          }
        }

        return $markup;
      }
    }
    else {

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
