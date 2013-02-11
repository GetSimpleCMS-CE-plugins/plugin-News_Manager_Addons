<?php

// register plugin
$thisfile = basename(__FILE__, ".php");
register_plugin(
	$thisfile,
	'News Manager Addons',
	'0.2 beta',
	'Carlos Navarro',
	'http://www.cyberiada.org/cnb/',
	'Additional functions/template tags for News Manager'
);

/*******************************************************
 * @function nm_list_recent_with_date
 * @action print a list with the latest posts (titles and dates)
 * @param $fmt date format (strftime)
 * @param $before show date before (true) or after (false, default) the post title
 */
function nm_list_recent_with_date($fmt='', $before=false) {
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
      if ($before) {
        echo '<li>',$date,' <a href="',$url,'">',$title,'</a></li>';
      } else {
        echo '<li><a href="',$url,'">',$title,'</a> ',$date,'</li>';
      }
    }
    echo '</ul>';
  }
}

/*******************************************************
 * @function nm_custom_list_recent
 */
function nm_custom_list_recent($templ = '') {
  if ($templ == '') $templ = '<a href="{{ post_link }}">{{ post_title }}</a>';
  echo '<ul>',PHP_EOL;
  nm_custom_display_recent('<li>'.$templ.'</li>'.PHP_EOL);
  echo '</ul>',PHP_EOL;
}

/*******************************************************
 * @function nm_custom_display_recent
 */
function nm_custom_display_recent($templ = '') {
  global $NMRECENTPOSTS;
  if ($templ == '') $templ = '<p><a href="{{ post_link }}">{{ post_title }}</a> {{ post_date }}</p>'.PHP_EOL;
  foreach(array('post_link','post_slug','post_title','post_date','post_excerpt','post_number') as $token) {
    str_replace('{{'.$token.'}}', '{{ '.$token.' }}', $templ);
  }
  $posts = nm_get_posts();
  if (!empty($posts)) {
    if (strpos($templ, '{{ post_date }}') !== false) {
	  global $NMCUSTOMDATE;
	  $fmt = $NMCUSTOMDATE ? $NMCUSTOMDATE : i18n_r('news_manager/DATE_FORMAT');
	} else {
	  $fmt = false;
	} 
    $count = 0;
    $posts = array_slice($posts, 0, $NMRECENTPOSTS, true);
    foreach ($posts as $post) {
      $str = $templ;
      $str = str_replace('{{ post_number }}', strval($count), $str);
      $str = str_replace('{{ post_slug }}', $post->slug, $str);
      $str = str_replace('{{ post_link }}', nm_get_url('post').$post->slug, $str);
      $str = str_replace('{{ post_title }}', stripslashes($post->title), $str);
      if ($fmt) {
		$date = nm_get_date($fmt, strtotime($post->date));
		$str = str_replace('{{ post_date }}', $date, $str);
	  }
	  if (strpos($str, '{{ post_excerpt }}') !== false) {
	    $postxml = getXML(NMPOSTPATH.$post->slug.'.xml');
		$excerpt = nm_create_excerpt(strip_decode($postxml->content));
		$str = str_replace('{{ post_excerpt }}', $excerpt, $str);
	  }
      echo $str;
      $count++;
    }
  }
}

/*******************************************************
 * @function nm_set_custom_date
 */
function nm_set_custom_date($fmt = '%Y-%m-%d') {
  global $NMCUSTOMDATE;
  $NMCUSTOMDATE = $fmt;
}
