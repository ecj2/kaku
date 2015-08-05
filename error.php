<?php

require "includes/classes/utility.php";

$Utility = new Utility;

$root_address = $Utility->getRootAddress();

if (!isset($_GET["code"])) {

  // Redirect to index.
  header("Location: {$root_address}");
}

switch ($_GET["code"]) {

  case 404:

    // Redirect to 404 page.
    header("Location: {$root_address}/page/page-not-found");
  break;

  default:

    // Redirect to index.
    header("Location: {$root_address}");
  break;
}

?>
