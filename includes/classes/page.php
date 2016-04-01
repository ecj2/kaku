<?php

class Page extends Utility {

  private $Database;

  public function __construct() {

    //
  }

  public function getBody() {

    return $this->getData("body");
  }

  public function getTitle() {

    return $this->getData("title");
  }

  public function getDescription() {

    $description = $this->getData("description");

    if (strlen($description) == 0) {

      // The page lacks a description.
      $description = "No description.";
    }

    return $description;
  }

  public function setDatabaseHandle($DatabaseHandle) {

    $this->Database = $DatabaseHandle;
  }

  private function getData($column) {

    if (!isset($_GET["page_url"])) {

      // Disallow this method from being used if not viewing a page.
      return;
    }

    // Select the given column.
    $statement = "

      SELECT {$column}
      FROM " . DB_PREF . "pages
      WHERE url = ?
    ";

    $query = $this->Database->prepare($statement);

    // Prevent SQL injections.
    $query->bindParam(1, $_GET["page_url"]);

    $query->execute();

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get page");
    }
    else if ($query->rowCount() == 0) {

      $address = Utility::getRootAddress();

      // Query returned zero rows. Redirect to 404 page.
      header("Location: {$address}/error.php?code=404");
    }
    else {

      // Return the desired data.
      return $query->fetch(PDO::FETCH_OBJ)->$column;
    }
  }
}

?>
