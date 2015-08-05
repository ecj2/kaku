<?php

class Page extends Utility {

  private $DatabaseHandle;

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

    return $this->getData("description");
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  private function getData($column) {

    if (!isset($_GET["page_url"])) {

      // Disallow this method from being used if not viewing a page.
      return;
    }

    $statement = "

      SELECT {$column}
      FROM " . DB_PREF . "pages
      WHERE url = ?
    ";

    $query = $this->DatabaseHandle->prepare($statement);

    // Prevent SQL injections.
    $query->bindParam(1, $_GET["page_url"]);

    $query->execute();

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get page");
    }
    else if ($query->rowCount() == 0) {

      $address = Utility::getRootAddress();

      // Query returned zero rows.
      header("Location: {$address}/error.php?code=404");
    }
    else {

      // Get the desired data.
      return $query->fetch(PDO::FETCH_OBJ)->$column;
    }
  }
}

?>
