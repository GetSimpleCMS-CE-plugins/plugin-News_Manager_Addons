<?php
/*

News Manager Addons plugin

https://github.com/cnb/News_Manager_Addons-getsimplecms/
http://www.cyberiada.org/cnb/news-manager-addons-plugin/

*/

# register plugin
$thisfile = basename(__FILE__, ".php");
register_plugin(
	$thisfile,
	'News Manager Addons',
	'0.6 beta',
	'Carlos Navarro',
	'http://www.cyberiada.org/cnb/',
	'Additional functions/template tags for News Manager'
);

function nm_search_with_placeholder($placeholder='') {
  global $i18n;
  if (!$placeholder)
    $placeholder = (isset($i18n['news_manager/SEARCH_PLACEHOLDER'])) ? $i18n['news_manager/SEARCH_PLACEHOLDER'] : $i18n['news_manager/SEARCH'].' ...';
  $placeholder = htmlspecialchars($placeholder);
  $url = nm_get_url();
  ?>
  <form id="search" action="<?php echo $url; ?>" method="post">
    <input type="text" class="text" name="keywords" value="<?php echo $placeholder; ?>" onfocus="if (this.value=='<?php echo $placeholder; ?>') {this.value=''}" onblur="if (this.value=='') {this.value='<?php echo $placeholder; ?>'}"/>
    <!--[if IE]><input type="text" style="display: none;" disabled="disabled"
    size="20" value="Ignore field. IE bug fix" /><![endif]-->
    <input type="submit" class="submit" name="search" value="<?php i18n('news_manager/SEARCH'); ?>" />
  </form>
  <?php
}

if(!function_exists('nm_list_recent_by_tag')) {
  function nm_list_recent_by_tag($tag='', $max=0) {
    if (trim($tag) !== '') {
      if ($max <= 0) {
        global $NMRECENTPOSTS;
        $max = $NMRECENTPOSTS;
      }
      $allposts = nm_get_posts();
      $posts = array();
      foreach ($allposts as $post)
        if (in_array($tag, explode(',', $post->tags)))
          $posts[] = $post;
      unset($allposts);
      $posts = array_slice($posts, 0, $max, true);
      if (!empty($posts)) {
        echo '<ul class="nm_recent">',PHP_EOL;
        foreach ($posts as $post) {
          $url = nm_get_url('post').$post->slug;
          $title = stripslashes($post->title);
          echo '<li><a href="'.$url.'">'.$title.'</a></li>',PHP_EOL;
        }
        echo '</ul>',PHP_EOL;
      }
    }
  }
}

function nm_list_recent_with_date($fmt='', $before=false) {
  nm_set_custom_date($fmt);
  if ($before) {
    $templ = '{{ post_date }} <a href="{{ post_link }}">{{ post_title }}</a>';
  } else {
    $templ = '<a href="{{ post_link }}">{{ post_title }}</a> {{ post_date }}';
  }
  nm_custom_list_recent($templ);
}

function nm_custom_list_recent($templ = '') {
  if ($templ == '') $templ = '<a href="{{ post_link }}">{{ post_title }}</a>';
  echo '<ul class="nm_recent">',PHP_EOL;
  nm_custom_display_recent('<li>'.$templ.'</li>'.PHP_EOL);
  echo '</ul>',PHP_EOL;
}

function nm_custom_display_recent($templ = '') {
  global $NMRECENTPOSTS, $NMIMAGES, $NMCUSTOMIMAGES;
  if ($templ == '') $templ = '<p><a href="{{ post_link }}">{{ post_title }}</a> {{ post_date }}</p>'.PHP_EOL;
  foreach(array('post_link','post_slug','post_title','post_date','post_excerpt','post_number','post_image','post_image_url') as $token) {
    if (strpos($templ, '{{'.$token.'}}'))
      $templ = str_replace('{{'.$token.'}}', '{{ '.$token.' }}', $templ);
  }
  $posts = nm_get_posts();
  if (!empty($posts)) {
    if (strpos($templ, '{{ post_date }}') !== false) {
      global $NMCUSTOMDATE;
      $fmt = $NMCUSTOMDATE ? $NMCUSTOMDATE : i18n_r('news_manager/DATE_FORMAT');
    } else {
      $fmt = false;
    }
    if ($NMCUSTOMIMAGES) {
      $NMIMAGES_orig = $NMIMAGES;
      $NMIMAGES = array_merge($NMIMAGES_orig, $NMCUSTOMIMAGES);
    }
    $count = 0;
    $posts = array_slice($posts, 0, $NMRECENTPOSTS, true);
    foreach ($posts as $post) {
      $str = $templ;
      $str = str_replace('{{ post_number }}', strval($count), $str);
      $str = str_replace('{{ post_slug }}', $post->slug, $str);
      $str = str_replace('{{ post_link }}', nm_get_url('post').$post->slug, $str);
      $str = str_replace('{{ post_title }}', stripslashes($post->title), $str);
      if (strpos($str, '{{ post_image') !== false && function_exists('nm_get_image_url')) {
        $img = (string)$post->image;
        $str = str_replace('{{ post_image_url }}', htmlspecialchars(nm_get_image_url($img)), $str);
        $str = str_replace('{{ post_image }}', '<img src="'.htmlspecialchars(nm_get_image_url($img)).'" />', $str);
      }
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
    if ($NMCUSTOMIMAGES) $NMIMAGES = $NMIMAGES_orig;
  }
}

function nm_set_custom_date($fmt = '%Y-%m-%d') {
  global $NMCUSTOMDATE;
  $NMCUSTOMDATE = $fmt;
}

function nm_set_custom_excerpt($len = 150) {
  global $NMEXCERPTLENGTH;
  $NMEXCERPTLENGTH = $len;
}

function nm_set_custom_maxposts($max = 5) {
  global $NMRECENTPOSTS;
  $NMRECENTPOSTS = $max;
}

function nm_set_custom_image($width=null, $height=null, $crop=null, $default=null) {
  global $NMCUSTOMIMAGES;
  $NMCUSTOMIMAGES = array();
  if ($width) $NMCUSTOMIMAGES['width'] = $width;
  if ($height) $NMCUSTOMIMAGES['height'] = $height;
  if ($crop) $NMCUSTOMIMAGES['crop'] = $crop;
  if ($default) $NMCUSTOMIMAGES['default'] = $default;
}
