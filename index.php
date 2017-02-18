<?php

require "core/includes/common.php";

// Get unique content data.
$Content->getAuthor();
$Content->getKeywords();
$Content->getEpochCreated();

// Get additional content data.
$Content->getColumn("id");
$Content->getColumn("url");
$Content->getColumn("body");
$Content->getColumn("type");
$Content->getColumn("draft");
$Content->getColumn("title");
$Content->getColumn("keywords");
$Content->getColumn("author_id");
$Content->getColumn("description");
$Content->getColumn("epoch_edited");
$Content->getColumn("epoch_created");
$Content->getColumn("allow_comments");
$Content->getColumn("show_on_search");

if (!empty($_GET["path"])) {

  if (substr($_GET["path"], 0, 4) == "page") {

    // Populate the pagination range with its associated posts.
    $Content->getPostBlocks();

    // View posts by a pagination range.
    echo $Theme->getFileContents("range", true);
  }
  else {

    if ($Hook->doAction("content_type") == 0) {

      // Viewing a post.
      echo $Theme->getFileContents("post", true);
    }
    else {

      // Viewing a page.
      echo $Theme->getFileContents("page", true);
    }
  }
}
else {

  // Populate the index with recent posts.
  $Content->getPostBlocks();

  // Viewing recent posts.
  echo $Theme->getFileContents("recent", true);
}

?>
