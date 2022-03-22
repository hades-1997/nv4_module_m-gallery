<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Đắc Lợi (saka.dacloi@gmail.com)
 * @Copyright (C) 2021 . All rights reserved
 * @Createdate July 15, 2021, 02:32:08 PM
 */


if( ! defined( 'NV_IS_FILE_SITEINFO' ) ) die( 'Stop!!!' );

$lang_siteinfo = nv_get_lang_module( $mod );

// Tong so bai viet
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE status= 1 AND publtime < ' . NV_CURRENTTIME . ' AND (exptime=0 OR exptime>' . NV_CURRENTTIME . ')' )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_publtime'], 'value' => $number );
}



// So bai viet da het han
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE exptime > 0 AND exptime<' . NV_CURRENTTIME )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_expired'], 'value' => $number );
}

// So bai viet sap het han
$number = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE status = 1 AND exptime>' . NV_CURRENTTIME )->fetchColumn();
if( $number > 0 )
{
	$siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_exptime'], 'value' => $number );
}


