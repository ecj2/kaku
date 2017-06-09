<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

function getPosts($Query) {

  $posts = "";

  $post_block_markup = $GLOBALS["Theme"]->getFileContents("post_block");

  if ($Query->rowCount() > 0) {

    $HookCopy = clone $GLOBALS["Hook"];

    while ($Result = $Query->fetch(PDO::FETCH_OBJ)) {

      $GLOBALS["Content"] = new Content($Result->id);

      // Get unique content data.
      $GLOBALS["Content"]->getEpoch();
      $GLOBALS["Content"]->getAuthor();
      $GLOBALS["Content"]->getKeywords();
      $GLOBALS["Content"]->getDescription();
      $GLOBALS["Content"]->getCommentSource();

      // Get additional content data.
      $GLOBALS["Content"]->getColumn("url");
      $GLOBALS["Content"]->getColumn("body");
      $GLOBALS["Content"]->getColumn("type");
      $GLOBALS["Content"]->getColumn("draft");
      $GLOBALS["Content"]->getColumn("title");
      $GLOBALS["Content"]->getColumn("keywords");
      $GLOBALS["Content"]->getColumn("author_id");
      $GLOBALS["Content"]->getColumn("description");
      $GLOBALS["Content"]->getColumn("epoch_created");
      $GLOBALS["Content"]->getColumn("allow_comments");
      $GLOBALS["Content"]->getColumn("show_on_search");

      $TemporaryBuffer = new Buffer();

      $TemporaryBuffer->start();

      echo $post_block_markup;

      $posts .= $TemporaryBuffer->flush();

      $GLOBALS["Hook"] = clone $HookCopy;
    }
  }

  return $posts;
}

function showErrorPage() {

  $_GET["path"] = "404";

  // Get unique content data.
  $GLOBALS["Content"]->getEpoch();
  $GLOBALS["Content"]->getAuthor();
  $GLOBALS["Content"]->getKeywords();
  $GLOBALS["Content"]->getDescription();
  $GLOBALS["Content"]->getCommentSource();

  // Get additional content data.
  $GLOBALS["Content"]->getColumn("url");
  $GLOBALS["Content"]->getColumn("body");
  $GLOBALS["Content"]->getColumn("type");
  $GLOBALS["Content"]->getColumn("draft");
  $GLOBALS["Content"]->getColumn("title");
  $GLOBALS["Content"]->getColumn("keywords");
  $GLOBALS["Content"]->getColumn("author_id");
  $GLOBALS["Content"]->getColumn("description");
  $GLOBALS["Content"]->getColumn("epoch_created");
  $GLOBALS["Content"]->getColumn("allow_comments");
  $GLOBALS["Content"]->getColumn("show_on_search");

  // Viewing a single post or page.
  echo $GLOBALS["Theme"]->getFileContents($GLOBALS["Hook"]->doAction("content_type") == 0 ? "post" : "page");
}

?>
