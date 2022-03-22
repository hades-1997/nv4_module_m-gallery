<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Đắc Lợi (saka.dacloi@gmail.com)
 * @Copyright (C) 2021 . All rights reserved
 * @Createdate July 15, 2021, 02:32:08 PM
 */
if( ! defined( 'NV_IS_MOD_NEWS' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

if( ! $slider )
{
	header( "Location:" . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA, true ) );
}

include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_site_theme( "" );
include ( NV_ROOTDIR . "/includes/footer.php" );

?>