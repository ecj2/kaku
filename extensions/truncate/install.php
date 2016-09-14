<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

if (!$Database->checkTableExistence("extension_truncate")) {

  // Create the extension_truncate table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "extension_truncate (

      lure TEXT NOT NULL
    )"
  )) {

    // Failed to create the extension_truncate table.
    $errors[] = "failed to create " . DB_PREF . "extension_truncate table";
  }

  // Set a default value for the lure.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "extension_truncate (

      lure
    )
    VALUES (

      'Read more...'
    )"
  )) {

    // Failed to set a default value for the lure.
    $errors[] = "failed to insert into " . DB_PREF . "extension_truncate";
  }
}

?>
