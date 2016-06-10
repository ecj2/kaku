<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class Page {

  public function getBody() {

    $this->getData("body");
  }

  public function getTitle() {

    $this->getData("title");
  }

  public function getDescription() {

    $this->getData("description");

    if (strlen(trim($GLOBALS["Hook"]->doAction("page_description"))) == 0) {

      // The page lacks a description.
      $description = "No description.";

      $GLOBALS["Hook"]->removeAction("page_description");

      $GLOBALS["Hook"]->addAction("page_description", $description);
    }
  }

  private function getData($column) {

    // Select the given column.
    $statement = "

      SELECT {$column}
      FROM " . DB_PREF . "pages
      WHERE url = ?
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["page"]);

    $Query->execute();

    if (!$Query) {

      // Something went wrong.
      $GLOBALS["Utility"]->displayError("failed to select page data");
    }

    if ($Query->rowCount() == 0) {

      // Get the absolute URL of where Kaku is installed.
      $root_address = $GLOBALS["Utility"]->getRootAddress();

      // Query returned zero rows. Redirect to 404 page.
      header("Location: {$root_address}/error?code=404");

      exit();
    }
    else {

      // Allow extensions to hook into this column of data.
      $GLOBALS["Hook"]->addAction(

        "page_{$column}",

        $Query->fetch(PDO::FETCH_OBJ)->$column
      );

      // Return the desired data.
      return $GLOBALS["Hook"]->doAction("page_{$column}");
    }
  }
}

?>
