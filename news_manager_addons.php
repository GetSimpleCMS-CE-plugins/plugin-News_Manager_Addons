<?php

// register plugin
$thisfile = basename(__FILE__, ".php");
register_plugin(
	$thisfile,
	'News Manager Addons',
	'0.1 beta',
	'Carlos Navarro',
	'http://www.cyberiada.org/cnb/',
	'Additional functions/template tags for News Manager'
);

/*******************************************************
 * @function nm_list_recent_with_date
 * @action print a list with the latest posts (titles and dates)
 */
function nm_list_recent_with_date($fmt='') {
  global $NMRECENTPOSTS;
  if ($fmt == '') $fmt = i18n_r('news_manager/DATE_FORMAT');
  $posts = nm_get_posts();
  if (!empty($posts)) {
    echo '<ul>';
    $posts = array_slice($posts, 0, $NMRECENTPOSTS, true);
    foreach ($posts as $post) {
      $url = nm_get_url('post').$post->slug;
      $title = stripslashes($post->title);
      $date = nm_get_date($fmt, strtotime($post->date));
      echo "<li><a href=\"$url\">$title</a> $date</li>";
    }
    echo '</ul>';
  }
}
