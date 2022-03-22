<?php
/**
 * @Project NUKEVIET 4.x
 * @Author Đắc Lợi (saka.dacloi@gmail.com)
 * @Copyright (C) 2021 . All rights reserved
 * @Createdate July 15, 2021, 02:32:08 PM
 */


if( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );

if( ! function_exists('nv_news_array_cat_admin') )
{
	/**
	 * nv_news_array_cat_admin()
	 *
	 * @return
	 */
	function nv_news_array_cat_admin( $module_data )
	{
		global $db;

		$array_cat_admin = array();
		$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_admins ORDER BY userid ASC';
		$result = $db->query( $sql );

		while( $row = $result->fetch() )
		{
			$array_cat_admin[$row['userid']][$row['catid']] = $row;
		}

		return $array_cat_admin;
	}
}

$is_refresh = false;
$array_cat_admin = nv_news_array_cat_admin( $module_data );

if( ! empty( $module_info['admins'] ) )
{
	$module_admin = explode( ',', $module_info['admins'] );
	foreach( $module_admin as $userid_i )
	{
		if( ! isset( $array_cat_admin[$userid_i] ) )
		{
			$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_admins (userid, catid, admin, add_content, pub_content, edit_content, del_content) VALUES (' . $userid_i . ', 0, 1, 1, 1, 1, 1)' );
			$is_refresh = true;
		}
	}
}
if( $is_refresh )
{
	$array_cat_admin = nv_news_array_cat_admin( $module_data );
}

$admin_id = $admin_info['admin_id'];
$NV_IS_ADMIN_MODULE = false;
$NV_IS_ADMIN_FULL_MODULE = false;
if( defined( 'NV_IS_SPADMIN' ) )
{
	$NV_IS_ADMIN_MODULE = true;
	$NV_IS_ADMIN_FULL_MODULE = true;
}
else
{
	if( isset( $array_cat_admin[$admin_id][0] ) )
	{
		$NV_IS_ADMIN_MODULE = true;
		if( intval( $array_cat_admin[$admin_id][0]['admin'] ) == 2 )
		{
			$NV_IS_ADMIN_FULL_MODULE = true;
		}
	}
}

$allow_func = array( 'main', 'view', 'exptime', 'publtime', 'waiting', 'declined', 're-published', 'content', 'rpc', 'del_content', 'alias',  'cat', 'change_cat', 'list_cat', 'del_cat' );

$submenu['cat'] = $lang_module['categories'];
if( ! isset( $site_mods['cms'] ) )
{
	$submenu['content'] = $lang_module['content_add'];
}

if( $NV_IS_ADMIN_MODULE )
{

	$submenu['setting'] = $lang_module['setting'];
	$allow_func[] = 'setting';
	$allow_func[] = 'tools';
}

if( file_exists( NV_ROOTDIR . '/modules/' . $module_file . '/admin/admins.php' ) )
{
	$submenu['admins'] = $lang_module['admin'];
	$allow_func[] = 'admins';
}