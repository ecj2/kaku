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
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<rss version=\"2.0\">";

// Describe the channel.
echo "<channel>";
echo "<title>{%blog_title%}</title>";
echo "<link>{%blog_url%}</link>";
echo "<description>{%blog_description%}</description>";
echo "<language>{%blog_language%}</language>";

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

    echo "<item>";
    echo "<title>{$post->title}</title>";
    echo "<link>{%blog_url%}/post/{$post->url}</link>";
    echo "<description>{$post->description}</description>";
    echo "<pubDate>" . date("D, d M Y H:i:s O", $post->epoch) ."</pubDate>";
    echo "</item>";
  }
}

// End the XML RSS document.
echo "</channel>";
echo "</rss>";

$Output->replaceTags();

$Output->flushBuffer();

$Database->disconnect();

?>
