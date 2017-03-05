News Manager Addons
===================

Additional functions for use with the News Manager plugin (for GetSimple CMS)

Minimum requirements:
 - PHP 5.2.x
 - GetSimple CMS 3.0+
 - News Manager 2.2.4+

Installation, usage, etc:    
<http://www.cyberiada.org/cnb/news-manager-addons-plugin/>

Support forum:    
<http://get-simple.info/forums/showthread.php?tid=4339>

Sidebar functions/template tags:

 - nm_custom_list_recent($template, $tag='') 
 - nm_custom_display_recent($template, $tag='') 
 - nm_custom_list_future($template, $tag='') 
 - nm_custom_display_future($template, $tag='') 
 - nm_custom_list_random($template, $tag='') 
 - nm_custom_display_random($template, $tag='') 
 - nm_set_custom_date($dateformat) 
 - nm_set_custom_excerpt($excerptlength) 
 - nm_set_custom_title_excerpt($excerptlength, $ellipsis) 
 - nm_set_custom_maxposts($maxposts)
 - nm_set_custom_offset($offset)
 - nm_list_recent_with_date($dateformat, $before=false)
 - nm_list_recent_by_tag($tag, $maxposts)
 - nm_search_with_placeholder($placeholder)

$template is a string (HTML code) where these tokens can be used:
 - {{ post_link }} - absolute URL of post
 - {{ post_title }} - post title
 - {{ post_title_excerpt }} - post title excerpt (News Manager 3.0+)
 - {{ post_date }} - post date/time
 - {{ post_excerpt }} - post content excerpt
 - {{ post_content }} - post content
 - {{ post_slug }} - post id
 - {{ post_number }} - number of post as displayed (0, 1, 2, ...)
 - {{ post_image }} - `<img ...>` tag for the post image (News Manager 3.0+)
 - {{ post_image_url }} - post image URL (News Manager 3.0+)
