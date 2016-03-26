<?php

require "includes/configuration.php";

require "includes/classes/utility.php";
require "includes/classes/database.php";

require "includes/classes/hook.php";
require "includes/classes/output.php";

global $Hook;

$Hook = new Hook();
$Output = new Output();
$Database = new Database();

// Pretend to be an XML document.
header("Content-Type: application/xml; charset=utf-8");

$Database->connect();

$Output->setDatabaseHandle($Database->getHandle());

$Output->startBuffer();

// Start the XML document.
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<rss version=\"2.0\">\n";

// Describe the channel.
echo "<channel>\n";
echo "<title>{%blog_title%}</title>\n";
echo "<link>{%blog_url%}</link>\n";
echo "<description>{%blog_description%}</description>\n";
echo "<language>{%blog_language%}</language>\n";

// Select published posts.
$statement = "

  SELECT url, title, epoch, description
  FROM " . DB_PREF . "posts
  WHERE draft = '0'
  ORDER BY id DESC
";

$query = $Database->getHandle()->query($statement);

if ($query && $query->rowCount() > 0) {

  while ($post = $query->fetch(PDO::FETCH_OBJ)) {

    // Generate an item for each post.

    echo "<item>\n";
    echo "<title>{$post->title}</title>\n";
    echo "<link>{%blog_url%}/post/{$post->url}</link>\n";

    echo "<description>\n";

    if (strlen(trim($post->description)) == 0) {

      // This post lacks a description.
      echo "No description.";
    }
    else {

      echo trim($post->description);
    }

    echo "</description>\n";
    echo "<pubDate>" . date("D, d M Y H:i:s O", $post->epoch) ."</pubDate>\n";
    echo "</item>\n";
  }
}

// End the XML RSS document.
echo "</channel>\n";
echo "</rss>";

$Output->replaceTags();

$Output->flushBuffer();

$Database->disconnect();

?>
