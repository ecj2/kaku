<?php

if (!$Database->checkTableExistence("extension_truncate")) {

  // Create the extension_truncate table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "extension_truncate (

      lure TEXT NOT NULL
    )"
  )) {

    array_push(

      $errors,

      "failed to create " . DB_PREF . "extension_truncate table"
    );
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

    array_push(

      $errors,

      "failed to insert into " . DB_PREF . "extension_truncate"
    );
  }
}

?>
