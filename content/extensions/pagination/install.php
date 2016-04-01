<?php

if (!$Database->checkTableExistence("extension_pagination")) {

  // Create the extension_pagination table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "extension_pagination (

      next_page_text TEXT NOT NULL,

      previous_page_text TEXT NOT NULL
    )"
  )) {

    array_push(

      $errors,

      "failed to create " . DB_PREF . "extension_pagination table"
    );
  }

  // Set default values.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "extension_pagination (

      next_page_text,

      previous_page_text
    )
    VALUES (

      'Older posts',

      'Newer posts'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert into " . DB_PREF . "extension_pagination"
    );
  }
}

?>
