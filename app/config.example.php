<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 09/04/14
 * Time: 11:39
 *
 * Configuration file for external services.
 * To fill in before running the system and rename to config.php
 */

return array(
    'nvl-slim.slideshare' => array(
        'url'       =>  'https://www.slideshare.net/api/2/get_slideshows_by_user',    // URL for the Slideshare API
        'api_key'   =>  '',   // SlideShare Personal API key
        'secret'    =>  ''   // SlideShare Shared Secret
    ),
    'nvl-slim.zotero' => array(
        'url'       =>  'https://api.zotero.org/users/',    // URL of the Zotero user API
        'userID'    =>  '',             // Zotero personal userID
        'api_key'   =>  '',             // Zotero API key for access to lib
        'collectID' =>  ''              // Unique ID of the Zotero collection to access
    ),

);