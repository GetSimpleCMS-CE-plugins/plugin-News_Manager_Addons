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
	'0.9.2',
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
    <input type="text" class="text" name="keywords" value="<?php echo $placeholder; ?>" onfocus="if (this.value=='<?php echo $placeholder; ?>') {this.value=''}" onblur="if (this.value=='') {this.value='<?php echo $placeholder; ?>'}" /><!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="20"
    value="Ignore field. IE bug fix" /><![endif]--><input type="submit" class="submit" name="search" value="<?php i18n('news_manager/SEARCH'); ?>" />
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

function nm_custom_list_recent($templ='', $tag='') {
  if ($templ == '') $templ = '<a href="{{ post_link }}">{{ post_title }}</a>';
  echo '<ul class="nm_recent">',PHP_EOL;
  nm_custom_display_posts('<li>'.$templ.'</li>'.PHP_EOL, $tag);
  echo '</ul>',PHP_EOL;
}

function nm_custom_list_future($templ='', $tag='') {
  if ($templ == '') $templ = '<a href="{{ post_link }}">{{ post_title }}</a>';
  echo '<ul class="nm_future">',PHP_EOL;
  nm_custom_display_posts('<li>'.$templ.'</li>'.PHP_EOL, $tag, 'future');
  echo '</ul>',PHP_EOL;
}

function nm_custom_display_recent($templ='', $tag='') {
  nm_custom_display_posts($templ, $tag);
}

function nm_custom_display_future($templ='', $tag='') {
  nm_custom_display_posts($templ, $tag, 'future');
}

function nm_custom_display_posts($templ='', $tag='', $type='') {
  global $NMRECENTPOSTS, $NMCUSTOMIMAGES, $NMCUSTOMOFFSET;
  if ($templ == '') $templ = '<p><a href="{{ post_link }}">{{ post_title }}</a> {{ post_date }}</p>'.PHP_EOL;
  foreach(array('post_link','post_slug','post_title','post_date','post_excerpt','post_content','post_number','post_image','post_image_url','post_author') as $token) {
    if (strpos($templ, '{{'.$token.'}}'))
      $templ = str_replace('{{'.$token.'}}', '{{ '.$token.' }}', $templ);
  }
  if ($type == 'future') {
    $posts = array_reverse(nm_get_posts(true));
    $allposts = array();
    $now = time();
    foreach ($posts as $post) {
      if ($post->private != 'Y' && strtotime($post->date) > $now)
        $allposts[] = $post;
    }
  } else {
    $allposts = nm_get_posts();
  }
  if (trim($tag) !== '') {
    $posts = array();
    foreach ($allposts as $post)
      if (in_array($tag, explode(',', $post->tags)))
        $posts[] = $post;
  } else {
    $posts = $allposts;
  }
  unset($allposts);
  
  if (!empty($posts)) {
    ob_start(); // content filter
    if (strpos($templ, '{{ post_date }}') !== false) {
      global $NMCUSTOMDATE;
      $fmt = $NMCUSTOMDATE ? $NMCUSTOMDATE : i18n_r('news_manager/DATE_FORMAT');
    } else {
      $fmt = false;
    }
    $w = isset($NMCUSTOMIMAGES['width']) ? $NMCUSTOMIMAGES['width'] : 0;
    $h = isset($NMCUSTOMIMAGES['height']) ? $NMCUSTOMIMAGES['height'] : 0;
    $c = isset($NMCUSTOMIMAGES['crop']) ? $NMCUSTOMIMAGES['crop'] : 0;
    $d = isset($NMCUSTOMIMAGES['default']) ? $NMCUSTOMIMAGES['default'] : '';
    $count = 0;
    $offset = $NMCUSTOMOFFSET ? intval($NMCUSTOMOFFSET) : 0;
    $posts = array_slice($posts, $offset, $NMRECENTPOSTS, true);
    foreach ($posts as $post) {
      $str = $templ;
      $str = str_replace('{{ post_number }}', strval($count), $str);
      $str = str_replace('{{ post_slug }}', $post->slug, $str);
      $str = str_replace('{{ post_link }}', nm_get_url('post').$post->slug, $str);
      $str = str_replace('{{ post_title }}', stripslashes($post->title), $str);
      if (strpos($str, '{{ post_image') !== false && function_exists('nm_get_image_url')) {
        $img = htmlspecialchars(nm_get_image_url((string)$post->image,$w,$h,$c,$d));
        $str = str_replace('{{ post_image_url }}', $img , $str);
        if (!empty($img))
          $str = str_replace('{{ post_image }}', '<img src="'.$img.'" alt="" />', $str);
        else
          $str = str_replace('{{ post_image }}', '', $str);
      }
      if ($fmt) {
        $date = nm_get_date($fmt, strtotime($post->date));
        $str = str_replace('{{ post_date }}', $date, $str);
      }
      if (strpos($str, '{{ post_author }}') !== false) {
        if (isset($post->author)) {
          $author = strval($post->author);
          global $NMAUTHOR; // NM Custom Authors (array)
          if ($NMAUTHOR && isset($NMAUTHOR[$author]))
            $author = $NMAUTHOR[$author];
        } else {
          if (function_exists('nm_get_option')) {
            $author = nm_get_option('defaultauthor'); // NM 3.0+ custom setting
            if (!$author) $author = '';
          } else {
            $author = '';
          }
        }
        $str = str_replace('{{ post_author }}', $author, $str);
      }
      if (strpos($str, '{{ post_excerpt }}') !== false || strpos($str, '{{ post_content }}') !== false) {
        $postxml = getXML(NMPOSTPATH.$post->slug.'.xml');
        if (strpos($str, '{{ post_excerpt }}') !== false) {
          if (function_exists('nm_make_excerpt1'))
            $excerpt = nm_make_excerpt(strip_decode($postxml->content));
          else // NM < 3.0 - remove <p>, </p>
            $excerpt = substr(nm_create_excerpt(strip_decode($postxml->content)), 3, -4);
          $str = str_replace('{{ post_excerpt }}', $excerpt, $str);
        } else {
          $str = str_replace('{{ post_content }}', strip_decode($postxml->content), $str);
        }
      }
      echo $str;
      $count++;
    }
    $output = ob_get_contents();
    ob_end_clean();
    echo exec_filter('content', $output); // content filter
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

function nm_set_custom_offset($offset = 0) {
  global $NMCUSTOMOFFSET;
  $NMCUSTOMOFFSET = $offset;
}

function nm_set_custom_image($width=null, $height=null, $crop=null, $default=null) {
  global $NMCUSTOMIMAGES;
  $NMCUSTOMIMAGES = array();
  if ($width) $NMCUSTOMIMAGES['width'] = $width;
  if ($height) $NMCUSTOMIMAGES['height'] = $height;
  if ($crop) $NMCUSTOMIMAGES['crop'] = $crop;
  if ($default) $NMCUSTOMIMAGES['default'] = $default;
}
