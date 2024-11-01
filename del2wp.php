<?php
/**
Title: WordPre.cio.us
Author: Chris Heisel
E-mail: del2wp@heisel.org
Version: 1.0
**/

/*** Don't edit down here 'less you know what your doing ***/
require('del2wp.config.php');
require($wpconfigpath);
require_once($magpiepath);
require_once('del2wp.lib.php');

$defaults['author'] = $authorid;
$defaults['categories'] = $category_names;
$defaults['ping_status'] = $ping_status;
$defaults['comment_status'] = $comment_status;
define("MAGPIE_INPUT_ENCODING", "UTF-8"); //added to avoid funny character issues
define("MAGPIE_OUTPUT_ENCODING", "UTF-8");
$rss = fetch_rss($rss_url);

$factory = new BlogmarkFactory;
$items = $factory->fromRSS($rss, $offset_hours);

$mywp = new WordPressImporter($wpdb);

foreach($items as $entry) {
    $mywp->import_post($entry->wpMarshall($defaults));
}
?>