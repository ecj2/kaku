<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Search extends Extension {

  public function __construct() {

    Extension::setName("Simple Search");

    $GLOBALS["Hook"]->addFilter("search", $this, "manageSearch");
  }

  public function manageSearch() {

    if (isset($_GET["keywords"])) {

      // Remove whitespace from the beginning and the end of the search keywords.
      $_GET["keywords"] = trim($_GET["keywords"]);

      // Select posts and pages where something is like the search keywords.
      $statement = "

        SELECT url, title, description
        FROM " . DB_PREF . "content
        WHERE show_on_search = 1
        AND (keywords LIKE ? OR title LIKE ?)
        ORDER BY title ASC
      ";

      $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

      $search_keywords = "%" . $_GET["keywords"] . "%";

      // Prevent SQL injections.
      $Query->bindParam(1, $search_keywords);
      $Query->bindParam(2, $search_keywords);

      $Query->execute();

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select for search");
      }

      if ($Query->rowCount() == 0) {

        // Query returned zero rows.
        $markup = "No results found for \"" . $_GET["keywords"] . "\".<br><br>";

        $markup .= "<form method=\"get\">";
        $markup .= "<input type=\"search\" name=\"keywords\">";
        $markup .= "<input type=\"submit\" value=\"Search\">";
        $markup .= "</form>";

        return $markup;
      }

      $markup = null;

      if ($Query->rowCount() == 1) {

        // One result found.
        $markup .= "{$Query->rowCount()} result found ";
      }
      else {

        // Multiple results found.
        $markup .= "{$Query->rowCount()} results found ";
      }

      $markup .= "for \"" . $_GET["keywords"] . "\":";

      $markup .= "<br><br><form style=\"display: inline;\" ";
      $markup .= "method=\"get\"><input type=\"search\" name=\"keywords\">";
      $markup .= "<input type=\"submit\" value=\"Search\">";
      $markup .= "</form>";

      while ($Result = $Query->fetch(PDO::FETCH_OBJ)) {

        // Get a link and description for each search result.

        $markup .= "<br><br><a href=\"{%blog_url%}/";

        $markup .= $Result->url . "\">";


        $markup .= $Result->title . "</a><br>";

        if (empty(trim($Result->description))) {

          // The resource has no description.
          $markup .= "No description.";
        }
        else {

          // The resource has a description.
          $markup .= "{$Result->description}";
        }
      }

      // Display the results.
      return $markup;
    }
    else {

      // Display the search form.

      $markup = "Use the form below to search for posts and pages.<br><br>";

      $markup .= "<form method=\"get\">";
      $markup .= "<input type=\"search\" name=\"keywords\">";
      $markup .= "<input type=\"submit\" value=\"Search\">";
      $markup .= "</form>";

      return $markup;
    }
  }
}

?>
