<?php

require "core/includes/common.php";

$Output->loadExtensions();

if (file_exists("install.php")) {

  // Install Kaku.
  require "install.php";
}

if (isset($_GET["post"])) {

  // Load the template file for viewing posts.
  $Template->getFileContents("post", true);

  // Get the post's title.
  $Post->getTitle();

  // Get the post's body.
  $Post->getBody();

  // Get the post's author.
  $Post->getAuthor();

  // Get the post's keywords.
  $Post->getKeywords();

  // Get the post's description.
  $Post->getDescription();

  // Get the post's absolute epoch.
  $Post->getAbsoluteEpoch();

  // Get the post's relative epoch.
  $Post->getRelativeEpoch();

  // Get the post's date time epoch.
  $Post->getDateTimeEpoch();

  // Get the post's uniform resource locator.
  $Post->getUniformResourceLocator();

  // Get the source for comments.
  $Comment->getSource();
}
else if (isset($_GET["range"])) {

  // Load the template file for viewing posts by ranges.
  $Template->getFileContents("range", true);

  // Get the range posts' blocks.
  $Post->getBlocks();

  // Get the posts' title.
  $Post->getTitle();

  // Get the posts' body.
  $Post->getBody();

  // Get the posts' author.
  $Post->getAuthor();

  // Get the posts' keywords.
  $Post->getKeywords();

  // Get the posts' description.
  $Post->getDescription();

  // Get the posts' absolute epoch.
  $Post->getAbsoluteEpoch();

  // Get the posts' relative epoch.
  $Post->getRelativeEpoch();

  // Get the posts' date time epoch.
  $Post->getDateTimeEpoch();

  // Get the posts' uniform resource locator.
  $Post->getUniformResourceLocator();
}
else if (isset($_GET["page"])) {

  // Load the template file for viewing pages.
  $Template->getFileContents("page", true);

  // Get the page's title.
  $Page->getTitle();

  // Get the page's body.
  $Page->getBody();

  // Get the page's description.
  $Page->getDescription();
}
else if (in_array("feed", $_GET)) {

  // View the feed.
  require "feed.php";
}
else {

  // Load the template file for viewing the latest posts.
  $Template->getFileContents("latest", true);

  // Get the latest posts' blocks.
  $Post->getBlocks();

  // Get the posts' title.
  $Post->getTitle();

  // Get the posts' body.
  $Post->getBody();

  // Get the posts' author.
  $Post->getAuthor();

  // Get the posts' keywords.
  $Post->getKeywords();

  // Get the posts' description.
  $Post->getDescription();

  // Get the posts' absolute epoch.
  $Post->getAbsoluteEpoch();

  // Get the posts' relative epoch.
  $Post->getRelativeEpoch();

  // Get the posts' date time epoch.
  $Post->getDateTimeEpoch();

  // Get the posts' uniform resource locator.
  $Post->getUniformResourceLocator();
}

// Clear the head_content and body_content tags if they go unused.
$Hook->addAction("head_content", "");
$Hook->addAction("body_content", "");

$Output->replaceTags();

?>
