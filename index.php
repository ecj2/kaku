<?php

require "includes/configuration.php";

require "includes/classes/utility.php";
require "includes/classes/database.php";

require "includes/classes/hook.php";
require "includes/classes/page.php";
require "includes/classes/post.php";
require "includes/classes/theme.php";
require "includes/classes/output.php";
require "includes/classes/comment.php";

global $Hook;

$Hook = new Hook;
$Page = new Page;
$Post = new Post;
$Theme = new Theme;
$Output = new Output;
$Comment = new Comment;
$Database = new Database;

$Database->connect();

$Page->setDatabaseHandle($Database->getHandle());
$Post->setDatabaseHandle($Database->getHandle());
$Theme->setDatabaseHandle($Database->getHandle());
$Output->setDatabaseHandle($Database->getHandle());
$Comment->setDatabaseHandle($Database->getHandle());

$Output->startBuffer();

if (file_exists("install.php")) {

  require "install.php";
}

$Output->loadExtensions();

$_GET[""] = "";

switch (array_keys($_GET)[0]) {

  case "post_url":

    // Viewing a post.

    $Hook->addAction(

      "post_file_contents",

      $Theme,

      "getFileContents",

      "post.html"
    );

    echo $Hook->doAction("post_file_contents");

    $Hook->addAction("post_body", $Post, "getBody");

    $Output->addTagReplacement(

      "post_body",

      $Hook->doAction("post_body")
    );

    $Hook->addAction("post_keywords", $Post, "getKeywords");

    $Output->addTagReplacement(

      "post_keywords",

      $Hook->doAction("post_keywords")
    );

    $Hook->addAction("post_title", $Post, "getTitle");

    $Output->addTagReplacement(

      "post_title",

      $Hook->doAction("post_title")
    );

    $Hook->addAction("post_author", $Post, "getAuthor");

    $Output->addTagReplacement(

      "post_author",

      $Hook->doAction("post_author")
    );

    $Hook->addAction("post_description", $Post, "getDescription");

    $Output->addTagReplacement(

      "post_description",

      $Hook->doAction("post_description")
    );

    $Hook->addAction("post_absolute_epoch", $Post, "getAbsoluteEpoch");

    $Output->addTagReplacement(

      "post_absolute_epoch",

      $Hook->doAction("post_absolute_epoch")
    );

    $Hook->addAction("post_relative_epoch", $Post, "getRelativeEpoch");

    $Output->addTagReplacement(

      "post_relative_epoch",

      $Hook->doAction("post_relative_epoch")
    );

    $Hook->addAction("post_date_time_epoch", $Post, "getDateTimeEpoch");

    $Output->addTagReplacement(

      "post_date_time_epoch",

      $Hook->doAction("post_date_time_epoch")
    );

    $Hook->addAction(

      "comment_block_file_contents",

      $Theme,

      "getFileContents",

      "comment_block.html"
    );

    $Hook->addAction(

      "comments",

      $Comment,

      "getSource",

      $Hook->doAction("comment_block_file_contents")
    );

    $Output->addTagReplacement(

      "comments",

      $Hook->doAction("comments")
    );
  break;

  case "page_url":

    // Viewing a page.

    $Hook->addAction(

      "page_file_contents",

      $Theme,

      "getFileContents",

      "page.html"
    );

    echo $Hook->doAction("page_file_contents");

    $Hook->addAction("page_body", $Page, "getBody");

    $Output->addTagReplacement(

      "page_body",

      $Hook->doAction("page_body")
    );

    $Hook->addAction("page_title", $Page, "getTitle");

    $Output->addTagReplacement(

      "page_title",

      $Hook->doAction("page_title")
    );

    $Hook->addAction("page_description", $Page, "getDescription");

    $Output->addTagReplacement(

      "page_description",

      $Hook->doAction("page_description")
    );
  break;

  case "page_number":

    // Viewing posts by range.

    $Hook->addAction(

      "range_file_contents",

      $Theme,

      "getFileContents",

      "range.html"
    );

    echo $Hook->doAction("range_file_contents");

    $Hook->addAction(

      "post_block_file_contents",

      $Theme,

      "getFileContents",

      "post_block.html"
    );

    $Hook->addAction(

      "posts_range",

      $Post,

      "getRange",

      $Hook->doAction("post_block_file_contents")
    );

    $Output->addTagReplacement(

      "posts_range",

      $Hook->doAction("posts_range")
    );
  break;

  default:

    // Viewing latest posts.

    $Hook->addAction(

      "latest_file_contents",

      $Theme,

      "getFileContents",

      "latest.html"
    );

    echo $Hook->doAction("latest_file_contents");

    $Hook->addAction(

      "post_block_file_contents",

      $Theme,

      "getFileContents",

      "post_block.html"
    );

    $Hook->addAction(

      "latest_posts",

      $Post,

      "getLatest",

      $Hook->doAction("post_block_file_contents")
    );

    $Output->addTagReplacement(

      "latest_posts",

      $Hook->doAction("latest_posts")
    );

    $Hook->addAction(

      "post_titles",

      $Post,

      "getTitles"
    );

    $count = 1;

    foreach ($Hook->doAction("post_titles") as $title) {

      $Output->addTagReplacement(

        "post_title_{$count}",

        $title
      );

      ++$count;
    }
  break;
}

$Output->addTagReplacement(

  "navigation_items",

  $Theme->getNavigationItems()
);

$Output->loadExtensions();

// Remove head_content tag if no extension uses it.
$Output->addTagReplacement(

  "head_content",

  ""
);

// Remove body_content tag if no extension uses it.
$Output->addTagReplacement(

  "body_content",

  ""
);

$Output->replaceTags();

$Output->flushBuffer();

$Database->disconnect();

?>
