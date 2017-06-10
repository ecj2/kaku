<?php

require "core/includes/common.php";

$Extension->loadExtensions();

if (!empty($_GET["path"]) && substr($_GET["path"], 0, 4) == "page") {

  $range = substr($_GET["path"], 5);

  $offset = $GLOBALS["Utility"]->getTag("posts_per_page") * ($range - 1);

  // Get posts by range.
  $statement = "

    SELECT *
    FROM " . DB_PREF . "content
    WHERE draft = 0
    AND type = 0
    ORDER BY epoch_created DESC
    LIMIT " . $GLOBALS["Utility"]->getTag("posts_per_page") . "
    OFFSET {$offset}
  ";

  $Query = $GLOBALS["Database"]->getHandle()->query($statement);

  if (!$Query || $Query->rowCount() == 0) {

    showErrorPage();
  }
  else {

    $posts = getPosts($Query);

    // Let extensions to hook into this.
    $GLOBALS["Hook"]->addAction("content_range", $posts);

    // Viewing posts by a pagination range.
    echo $Theme->getFileContents("range");
  }
}
else if (!empty($_GET["path"])) {

  // Get unique content data.
  $Content->getEpoch();
  $Content->getAuthor();
  $Content->getKeywords();
  $Content->getDescription();
  $Content->getCommentSource();

  // Get additional content data.
  $Content->getColumn("url");
  $Content->getColumn("body");
  $Content->getColumn("type");
  $Content->getColumn("draft");
  $Content->getColumn("title");
  $Content->getColumn("keywords");
  $Content->getColumn("author_id");
  $Content->getColumn("description");
  $Content->getColumn("epoch_created");
  $Content->getColumn("allow_comments");
  $Content->getColumn("show_on_search");

  // Viewing a single post or page.
  echo $Theme->getFileContents($Hook->doAction("content_type") == 0 ? "post" : "page");
}
else {

  // Get recent posts.
  $statement = "

    SELECT id
    FROM " . DB_PREF . "content
    WHERE draft = 0
    AND type = 0
    ORDER BY epoch_created DESC
    LIMIT " . $GLOBALS["Utility"]->getTag("posts_per_page") . "
  ";

  $Query = $GLOBALS["Database"]->getHandle()->query($statement);

  if (!$Query) {

    // Selection failed.
    $GLOBALS["Utility"]->displayError("failed to select recent posts");
  }

  $posts = getPosts($Query);

  // Let extensions to hook into this.
  $GLOBALS["Hook"]->addAction("content_recent", $posts);

  // Viewing recent posts.
  echo $Theme->getFileContents("recent");
}

echo $Buffer->flush();

?>
