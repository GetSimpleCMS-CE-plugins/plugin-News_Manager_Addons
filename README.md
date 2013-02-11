News Manager Addons
===================

Additional functions for use with the News Manager plugin (for GetSimple CMS)

Minimum requirements:
 - PHP 5.2.x
 - GetSimple CMS 3.0+
 - News Manager 2.2.4+
 
Sidebar functions/template tags:

 - **nm_custom_list_recent($template)**    
   ...
 
 - **nm_custom_display_recent($template)**    
   ...

 - **nm_set_custom_date($dateformat)**     
   ...    

 - **nm_list_recent_with_date($dateformat, $before=false)**   
    Usage (examples):    
    `<?php nm_list_recent_with_date(); ?>`    
    `<?php nm_list_recent_with_date(' - %d/%m/%Y'); ?>`
    `<?php nm_list_recent_with_date('%d/%m/%Y - ', true); ?> // date before the title`   
 

