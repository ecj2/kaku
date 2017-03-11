<?php

require "core/includes/common.php";

// Get unique content data.
$Content->getAuthor();
$Content->getEpochs();
$Content->getKeywords();
$Content->getDescriptions();
$Content->getCommentSource();

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

if (!empty($_GET["path"]) && substr($_GET["path"], 0, 4) == "page") {

  // Viewing posts by a pagination range.
  echo $Theme->getFileContents("range");

  // Populate the pagination range with its associated posts.
  $Content->getPostBlocks();

  return;
}

if (!empty($_GET["path"])) {

  // Viewing a single post or page.
  echo $Theme->getFileContents($Hook->doAction("content_type") == 0 ? "post" : "page");

  return;
}

// Viewing recent posts.
echo $Theme->getFileContents("recent");

// Populate the index with recent posts.
$Content->getPostBlocks();

?>
