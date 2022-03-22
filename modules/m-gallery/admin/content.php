<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Đắc Lợi (saka.dacloi@gmail.com)
 * @Copyright (C) 2021 . All rights reserved
 * @Createdate July 15, 2021, 02:32:08 PM
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

if( defined( 'NV_EDITOR' ) )
{
	require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

$username_alias = change_alias( $admin_info['username'] );
$array_structure_image = array();
$array_structure_image[''] = $module_name;
$array_structure_image['Y'] = $module_name . '/' . date( 'Y' );
$array_structure_image['Ym'] = $module_name . '/' . date( 'Y_m' );
$array_structure_image['Y_m'] = $module_name . '/' . date( 'Y/m' );
$array_structure_image['Ym_d'] = $module_name . '/' . date( 'Y_m/d' );
$array_structure_image['Y_m_d'] = $module_name . '/' . date( 'Y/m/d' );
$array_structure_image['username'] = $module_name . '/' . $username_alias;
$array_structure_image['username_Y'] = $module_name . '/' . $username_alias . '/' . date( 'Y' );
$array_structure_image['username_Ym'] = $module_name . '/' . $username_alias . '/' . date( 'Y_m' );
$array_structure_image['username_Y_m'] = $module_name . '/' . $username_alias . '/' . date( 'Y/m' );
$array_structure_image['username_Ym_d'] = $module_name . '/' . $username_alias . '/' . date( 'Y_m/d' );
$array_structure_image['username_Y_m_d'] = $module_name . '/' . $username_alias . '/' . date( 'Y/m/d' );

$structure_upload = isset( $module_config[$module_name]['structure_upload'] ) ? $module_config[$module_name]['structure_upload'] : 'Ym';
$currentpath = isset( $array_structure_image[$structure_upload] ) ? $array_structure_image[$structure_upload] : '';

if( file_exists( NV_UPLOADS_REAL_DIR . '/' . $currentpath ) )
{
	$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpath;
}
else
{
	$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_name;
	$e = explode( '/', $currentpath );
	if( ! empty( $e ) )
	{
		$cp = '';
		foreach( $e as $p )
		{
			if( ! empty( $p ) and ! is_dir( NV_UPLOADS_REAL_DIR . '/' . $cp . $p ) )
			{
				$mk = nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $cp, $p );
				if( $mk[0] > 0 )
				{
					$upload_real_dir_page = $mk[2];
					$db->query( "INSERT INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $cp . $p . "', 0)" );
				}
			}
			elseif( ! empty( $p ) )
			{
				$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
			}
			$cp .= $p . '/';
		}
	}
	$upload_real_dir_page = str_replace( '\\', '/', $upload_real_dir_page );
}

$currentpath = str_replace( NV_ROOTDIR . '/', '', $upload_real_dir_page );
$uploads_dir_user = NV_UPLOADS_DIR . '/' . $module_name;
if( ! defined( 'NV_IS_SPADMIN' ) and strpos( $structure_upload, 'username' ) !== false )
{
	$array_currentpath = explode( '/', $currentpath );
	if( $array_currentpath[2] == $username_alias )
	{
		$uploads_dir_user = NV_UPLOADS_DIR . '/' . $module_name . '/' . $username_alias;
	}
}

$catid = $nv_Request->get_int( 'catid', 'get', 0 );
$parentid = $nv_Request->get_int( 'parentid', 'get', 0 );
$rowcontent = array(
	'id' => '',
	'catid' => $catid,
	'listcatid' => $catid . ',' . $parentid,
	'admin_id' => $admin_id,
	'addtime' => NV_CURRENTTIME,
	'edittime' => NV_CURRENTTIME,
	'status' => 0,
	'publtime' => NV_CURRENTTIME,
	'exptime' => 0,
	'archive' => 1,
	'title' => '',
	'link' => '',
	'hometext' => '',
	'homeimgfile' => '',
	'otherimage' => '',
	'homeimgthumb' => '',
	'gid' => 0,
	'inhome' => 1,
	'mode' => 'add'
);

$page_title = $lang_module['content_add'];
$error = array();


$rowcontent['id'] = $nv_Request->get_int( 'id', 'get,post', 0 );
if( $rowcontent['id'] > 0 )
{
	$check_permission = false;
	$rowcontent = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows where id=' . $rowcontent['id'] )->fetch();
	if( ! empty( $rowcontent['id'] ) )
	{
		$rowcontent['mode'] = 'edit';
		$arr_catid = explode( ',', $rowcontent['listcatid'] );
		if( defined( 'NV_IS_ADMIN_MODULE' ) )
		{
			$check_permission = true;
		}
		else
		{
			$check_edit = 0;
			$status = $rowcontent['status'];
			foreach( $arr_catid as $catid_i )
			{
				if( isset( $array_cat_admin[$admin_id][$catid_i] ) )
				{
					if( $array_cat_admin[$admin_id][$catid_i]['admin'] == 1 )
					{
						++$check_edit;
					}
					else
					{
						if( $array_cat_admin[$admin_id][$catid_i]['edit_content'] == 1 )
						{
							++$check_edit;
						}
						elseif( $array_cat_admin[$admin_id][$catid_i]['pub_content'] == 1 and ( $status == 0 or $status = 2 ) )
						{
							++$check_edit;
						}
						elseif( ( $status == 0 or $status == 4 ) and $rowcontent['admin_id'] == $admin_id )
						{
							++$check_edit;
						}
					}
				}
			}
			if( $check_edit == sizeof( $arr_catid ) )
			{
				$check_permission = true;
			}
		}
	}

	if( ! $check_permission )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name );
		die();
	}

	$page_title = $lang_module['content_edit'];
}

$array_cat_add_content = $array_cat_pub_content = $array_cat_edit_content = $array_censor_content = array();
foreach( $global_array_cat as $catid_i => $array_value )
{
	$check_add_content = $check_pub_content = $check_edit_content = $check_censor_content = false;
	if( defined( 'NV_IS_ADMIN_MODULE' ) )
	{
		$check_add_content = $check_pub_content = $check_edit_content = $check_censor_content = true;
	}
	elseif( isset( $array_cat_admin[$admin_id][$catid_i] ) )
	{
		if( $array_cat_admin[$admin_id][$catid_i]['admin'] == 1 )
		{
			$check_add_content = $check_pub_content = $check_edit_content = $check_censor_content = true;
		}
		else
		{
			if( $array_cat_admin[$admin_id][$catid_i]['add_content'] == 1 )
			{
				$check_add_content = true;
			}

			if( $array_cat_admin[$admin_id][$catid_i]['pub_content'] == 1 )
			{
				$check_pub_content = true;
			}

			if( $array_cat_admin[$admin_id][$catid_i]['app_content'] == 1 )
			{
				$check_censor_content = true;
			}

			if( $array_cat_admin[$admin_id][$catid_i]['edit_content'] == 1 )
			{
				$check_edit_content = true;
			}
		}
	}
	if( $check_add_content )
	{
		$array_cat_add_content[] = $catid_i;
	}

	if( $check_pub_content )
	{
		$array_cat_pub_content[] = $catid_i;
	}
	if( $check_censor_content ) //Nguoi kiem duyet
	{
		$array_censor_content[] = $catid_i;
	}

	if( $check_edit_content )
	{
		$array_cat_edit_content[] = $catid_i;
	}
}

if( $nv_Request->get_int( 'save', 'post' ) == 1 )
{
	$catids = array_unique( $nv_Request->get_typed_array( 'catids', 'post', 'int', array() ) );
	
	$rowcontent['catid'] = $nv_Request->get_int( 'catid', 'post', 0 );
	$rowcontent['listcatid'] = implode( ',', $catids );

	if( $nv_Request->isset_request( 'status1', 'post' ) ) $rowcontent['status'] = 1; //dang tin
	elseif( $nv_Request->isset_request( 'status0', 'post' ) ) $rowcontent['status'] = 0; //cho tong bien tap duyet
	elseif( $nv_Request->isset_request( 'status4', 'post' ) ) $rowcontent['status'] = 4; //luu tam
	else  $rowcontent['status'] = 6; //gui, cho bien tap

	$message_error_show = $lang_module['permissions_pub_error'];
	if( $rowcontent['status'] == 1 )
	{
		$array_cat_check_content = $array_cat_pub_content;
	}
	elseif( $rowcontent['status'] == 1 and $rowcontent['publtime'] <= NV_CURRENTTIME )
	{
		$array_cat_check_content = $array_cat_edit_content;
	}
	elseif( $rowcontent['status'] == 0 )
	{
		$array_cat_check_content = $array_censor_content;
		$message_error_show = $lang_module['permissions_sendspadmin_error'];
	}
	else
	{
		$array_cat_check_content = $array_cat_add_content;
	}

	foreach( $catids as $catid_i )
	{
		if( ! in_array( $catid_i, $array_cat_check_content ) )
		{
			$error[] = sprintf( $message_error_show, $global_array_cat[$catid_i]['title'] );
		}
	}

	$publ_date = $nv_Request->get_title( 'publ_date', 'post', '' );

	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $publ_date, $m ) )
	{
		$phour = $nv_Request->get_int( 'phour', 'post', 0 );
		$pmin = $nv_Request->get_int( 'pmin', 'post', 0 );
		$rowcontent['publtime'] = mktime( $phour, $pmin, 0, $m[2], $m[1], $m[3] );
	}
	else
	{
		$rowcontent['publtime'] = NV_CURRENTTIME;
	}

	$exp_date = $nv_Request->get_title( 'exp_date', 'post', '' );
	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $exp_date, $m ) )
	{
		$ehour = $nv_Request->get_int( 'ehour', 'post', 0 );
		$emin = $nv_Request->get_int( 'emin', 'post', 0 );
		$rowcontent['exptime'] = mktime( $ehour, $emin, 0, $m[2], $m[1], $m[3] );
	}
	else
	{
		$rowcontent['exptime'] = 0;
	}

	$rowcontent['archive'] = $nv_Request->get_int( 'archive', 'post', 0 );
	if( $rowcontent['archive'] > 0 )
	{
		$rowcontent['archive'] = ( $rowcontent['exptime'] > NV_CURRENTTIME ) ? 1 : 2;
	}
	$rowcontent['title'] = $nv_Request->get_title( 'title', 'post', '', 1 );
	$rowcontent['link'] = $nv_Request->get_title( 'link', 'post', '', 1 );
	$rowcontent['hometext'] = $nv_Request->get_textarea( 'hometext', 'post', '', 'br', 1 );
	$rowcontent['homeimgfile'] = $nv_Request->get_title( 'homeimg', 'post', '' );
	$rowcontent['inhome'] = ( int )$nv_Request->get_bool( 'inhome', 'post' );
	$rowcontent['gid'] = $nv_Request->get_int( 'gid', 'post', 0 );

	if( empty( $rowcontent['title'] ) )
	{
		$error[] = $lang_module['error_title'];
	}
	elseif( empty( $rowcontent['listcatid'] ) )
	{
		$error[] = $lang_module['error_cat'];
	}
	

	if( empty( $error ) )
	{
		$rowcontent['catid'] = in_array( $rowcontent['catid'], $catids ) ? $rowcontent['catid'] : $catids[0];

		// Xu ly anh minh hoa
		$rowcontent['homeimgthumb'] = 0;
		if( ! nv_is_url( $rowcontent['homeimgfile'] ) and is_file( NV_DOCUMENT_ROOT . $rowcontent['homeimgfile'] ) )
		{
			$lu = strlen( NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' );
			$rowcontent['homeimgfile'] = substr( $rowcontent['homeimgfile'], $lu );
			if( file_exists( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module_upload . '/' . $rowcontent['homeimgfile'] ) )
			{
				$rowcontent['homeimgthumb'] = 1;
			}
			else
			{
				$rowcontent['homeimgthumb'] = 2;
			}
		}
		elseif( nv_is_url( $rowcontent['homeimgfile'] ) )
		{
			$rowcontent['homeimgthumb'] = 3;
		}
		else
		{
			$rowcontent['homeimgfile'] = '';
		}
		// Xu ly anh minh hoa khac
		$otherimage = $nv_Request->get_typed_array( 'otherimage', 'post', 'string' );
		$array_otherimage = array();


		foreach( $otherimage as $otherimage_i )
		{
			if( !nv_is_url( $otherimage_i ) and file_exists( NV_DOCUMENT_ROOT . $otherimage_i ) )
			{
				$lu = strlen( NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' );
				$otherimage_i = substr( $otherimage_i, $lu );
			}
			elseif( nv_is_url( $otherimage_i ) )
			{
				$otherimage_i = '';
			}
			if( !empty( $otherimage_i ) )
			{
				$array_otherimage[] = $otherimage_i;
			}
		}
		$rowcontent['otherimage'] = implode( '|', $array_otherimage );
		if( $rowcontent['id'] == 0 )
		{
			$rowcontent['publtime'] = ( $rowcontent['publtime'] > NV_CURRENTTIME ) ? $rowcontent['publtime'] : NV_CURRENTTIME;
			if( $rowcontent['status'] == 1 and $rowcontent['publtime'] > NV_CURRENTTIME )
			{
				$rowcontent['status'] = 2;
			}
			$sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_rows
				(catid, listcatid,  admin_id, addtime, edittime, status, publtime, exptime, archive, title, link,hometext, homeimgfile,otherimage, homeimgthumb, inhome) VALUES
				 (' . intval( $rowcontent['catid'] ) . ',
				 :listcatid,
				 ' . intval( $rowcontent['admin_id'] ) . ',
				 ' . intval( $rowcontent['addtime'] ) . ',
				 ' . intval( $rowcontent['edittime'] ) . ',
				 ' . intval( $rowcontent['status'] ) . ',
				 ' . intval( $rowcontent['publtime'] ) . ',
				 ' . intval( $rowcontent['exptime'] ) . ',
				 ' . intval( $rowcontent['archive'] ) . ',
				 :title, 
				 :link,
				 :hometext,
				 :homeimgfile,
				 :otherimage,
				 :homeimgthumb,
				 ' . intval( $rowcontent['inhome'] ) . ')';

			$data_insert = array();
			$data_insert['listcatid'] = $rowcontent['listcatid'];
			
			$data_insert['title'] = $rowcontent['title'];	
			$data_insert['link'] = $rowcontent['link'];
	
			$data_insert['hometext'] = $rowcontent['hometext'];
			$data_insert['homeimgfile'] = $rowcontent['homeimgfile'];
			$data_insert['otherimage'] = $rowcontent['otherimage'];
			$data_insert['homeimgthumb'] = $rowcontent['homeimgthumb'];
			

		$rowcontent['id'] = $db->insert_id( $sql, 'id', $data_insert );
			if( $rowcontent['id'] > 0 )
			{
			
				foreach( $catids as $catid )
				{
					
					$ct_query[] = ( int )$db->exec( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid . ' SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $rowcontent['id'] );
				}
			}
			else
			{
				$error[] = $lang_module['errorsave'];
			}
		}
		else
		{
			$rowcontent_old = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows where id=' . $rowcontent['id'] )->fetch();
			if( $rowcontent_old['status'] == 1 )
			{
				$rowcontent['status'] = 1;
			}
			if( intval( $rowcontent['publtime'] ) < intval( $rowcontent_old['addtime'] ) )
			{
				$rowcontent['publtime'] = $rowcontent_old['addtime'];
			}
			if( $rowcontent['status'] == 1 and $rowcontent['publtime'] > NV_CURRENTTIME )
			{
				$rowcontent['status'] = 2;
			}
			$sth = $db->prepare( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET
					 catid=' . intval( $rowcontent['catid'] ) . ',
					 listcatid=:listcatid,
					 status=' . intval( $rowcontent['status'] ) . ',
					 publtime=' . intval( $rowcontent['publtime'] ) . ',
					 exptime=' . intval( $rowcontent['exptime'] ) . ',
					 archive=' . intval( $rowcontent['archive'] ) . ',
					 title=:title, 
					 link=:link,
					 hometext=:hometext,
					 homeimgfile=:homeimgfile,
					 otherimage=:otherimage,
					 homeimgthumb=:homeimgthumb,
					 inhome=' . intval( $rowcontent['inhome'] ) . ',
					 edittime=' . NV_CURRENTTIME . '
				WHERE id =' . $rowcontent['id'] );
			$sth->bindParam( ':listcatid', $rowcontent['listcatid'], PDO::PARAM_STR );
			$sth->bindParam( ':title', $rowcontent['title'], PDO::PARAM_STR );	
			$sth->bindParam( ':link', $rowcontent['link'], PDO::PARAM_STR );
		
			$sth->bindParam( ':hometext', $rowcontent['hometext'], PDO::PARAM_STR, strlen( $rowcontent['hometext'] ) );
			$sth->bindParam( ':homeimgfile', $rowcontent['homeimgfile'], PDO::PARAM_STR );
			$sth->bindParam( ':otherimage', $rowcontent['otherimage'], PDO::PARAM_STR );
			$sth->bindParam( ':homeimgthumb', $rowcontent['homeimgthumb'], PDO::PARAM_STR );
			
			if( $sth->execute() )
			{
				nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['content_edit'], $rowcontent['title'], $admin_info['userid'] );

				$array_cat_old = explode( ',', $rowcontent_old['listcatid'] );
				$array_cat_new = explode( ',', $rowcontent['listcatid'] );

				$array_cat_diff = array_diff( $array_cat_old, $array_cat_new );
				foreach( $array_cat_diff as $catid )
				{
					$ct_query[] = $db->exec( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid . ' WHERE id = ' . $rowcontent['id'] );
				}
				foreach( $array_cat_new as $catid )
				{
					$db->exec( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid . ' WHERE id = ' . $rowcontent['id'] );
					$ct_query[] = $db->exec( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_' . $catid . ' SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $rowcontent['id'] );
				}

				if( array_sum( $ct_query ) != sizeof( $ct_query ) )
				{
					$error[] = $lang_module['errorsave'];
				}
			}
			else
			{
				$error[] = $lang_module['errorsave'];
			}
		}

		nv_set_status_module();
		if( empty( $error ) )
		{
			$url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
			$msg1 = $lang_module['content_saveok'];
			$msg2 = $lang_module['content_main'] . ' ' . $module_info['custom_title'];
			redriect( $msg1, $msg2, $url, $module_data . '_bodyhtml' );
		}
	}
	else
	{
		$url = 'javascript: history.go(-1)';
		$msg1 = implode( '<br />', $error );
		$msg2 = $lang_module['content_back'];
		redriect( $msg1, $msg2, $url );
	}
}
if( !empty( $rowcontent['otherimage'] ) )
{
	$otherimage = explode( '|', $rowcontent['otherimage'] );
}
else
{
	$otherimage = array( );
}
$array_catid_in_row = explode( ',', $rowcontent['listcatid'] );
$tdate = date( 'H|i', $rowcontent['publtime'] );
$publ_date = date( 'd/m/Y', $rowcontent['publtime'] );
list( $phour, $pmin ) = explode( '|', $tdate );
if( $rowcontent['exptime'] == 0 )
{
	$emin = $ehour = 0;
	$exp_date = '';
}
else
{
	$exp_date = date( 'd/m/Y', $rowcontent['exptime'] );
	$tdate = date( 'H|i', $rowcontent['exptime'] );
	list( $ehour, $emin ) = explode( '|', $tdate );
}

if( $rowcontent['status'] == 1 and $rowcontent['publtime'] > NV_CURRENTTIME )
{
	$array_cat_check_content = $array_cat_pub_content;
}
elseif( $rowcontent['status'] == 1 )
{
	$array_cat_check_content = $array_cat_edit_content;
}
else
{
	$array_cat_check_content = $array_cat_add_content;
}

if( empty( $array_cat_check_content ) )
{
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat' );
	die();
}
$contents = '';

if( ! empty( $rowcontent['homeimgfile'] ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $rowcontent['homeimgfile'] ) )
{
	$rowcontent['homeimgfile'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $rowcontent['homeimgfile'];
}

$xtpl = new XTemplate( 'content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'rowcontent', $rowcontent );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'module_name', $module_name );

foreach( $global_array_cat as $catid_i => $array_value )
{
	if( defined( 'NV_IS_ADMIN_MODULE' ) )
	{
		$check_show = 1;
	}
	else
	{
		$array_cat = GetCatidInParent( $catid_i );
		$check_show = array_intersect( $array_cat, $array_cat_check_content );
	}
	if( ! empty( $check_show ) )
	{
		$space = intval( $array_value['lev'] ) * 30;
		$catiddisplay = ( sizeof( $array_catid_in_row ) > 1 and ( in_array( $catid_i, $array_catid_in_row ) ) ) ? '' : ' display: none;';
		$temp = array(
			'catid' => $catid_i,
			'space' => $space,
			'title' => $array_value['title'],
			'disabled' => ( ! in_array( $catid_i, $array_cat_check_content ) ) ? ' disabled="disabled"' : '',
			'checked' => ( in_array( $catid_i, $array_catid_in_row ) ) ? ' checked="checked"' : '',
			'catidchecked' => ( $catid_i == $rowcontent['catid'] ) ? ' checked="checked"' : '',
			'catiddisplay' => $catiddisplay );
		$xtpl->assign( 'CATS', $temp );
		$xtpl->parse( 'main.catid' );
	}
}
// Other image
$items = 0;

if( !empty( $otherimage ) )
{
	foreach( $otherimage as $otherimage_i )
	{
		if( !empty( $otherimage_i ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $otherimage_i ) )
		{
			$otherimage_i = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $otherimage_i;
		}
		$data_otherimage_i = array(
			'id' => $items,
			'value' => $otherimage_i
		);
		$xtpl->assign( 'DATAOTHERIMAGE', $data_otherimage_i );
		$xtpl->parse( 'main.otherimage' );
		++$items;
	}
}
$xtpl->assign( 'FILE_ITEMS', $items );
// time update
$xtpl->assign( 'publ_date', $publ_date );
$select = '';
for( $i = 0; $i <= 23; ++$i )
{
	$select .= "<option value=\"" . $i . "\"" . ( ( $i == $phour ) ? ' selected="selected"' : '' ) . ">" . str_pad( $i, 2, "0", STR_PAD_LEFT ) . "</option>\n";
}
$xtpl->assign( 'phour', $select );
$select = '';
for( $i = 0; $i < 60; ++$i )
{
	$select .= "<option value=\"" . $i . "\"" . ( ( $i == $pmin ) ? ' selected="selected"' : '' ) . ">" . str_pad( $i, 2, "0", STR_PAD_LEFT ) . "</option>\n";
}
$xtpl->assign( 'pmin', $select );

// time exp
$xtpl->assign( 'exp_date', $exp_date );
$select = '';
for( $i = 0; $i <= 23; ++$i )
{
	$select .= "<option value=\"" . $i . "\"" . ( ( $i == $ehour ) ? ' selected="selected"' : '' ) . ">" . str_pad( $i, 2, "0", STR_PAD_LEFT ) . "</option>\n";
}
$xtpl->assign( 'ehour', $select );
$select = '';
for( $i = 0; $i < 60; ++$i )
{
	$select .= "<option value=\"" . $i . "\"" . ( ( $i == $emin ) ? ' selected="selected"' : '' ) . ">" . str_pad( $i, 2, "0", STR_PAD_LEFT ) . "</option>\n";
}
$xtpl->assign( 'emin', $select );

$archive_checked = ( $rowcontent['archive'] ) ? ' checked="checked"' : '';
$xtpl->assign( 'archive_checked', $archive_checked );
$inhome_checked = ( $rowcontent['inhome'] ) ? ' checked="checked"' : '';
$xtpl->assign( 'inhome_checked', $inhome_checked );;

if( ! empty( $error ) )
{
	$xtpl->assign( 'error', implode( '<br />', $error ) );
	$xtpl->parse( 'main.error' );
}

if( defined( 'NV_IS_ADMIN_MODULE' ) || ! empty( $array_pub_content ) ) //toan quyen module
{
	if( $rowcontent['status'] == 1 and $rowcontent['id'] > 0 )
	{
		$xtpl->parse( 'main.status' );
	}
	else
	{
		$xtpl->parse( 'main.status0' );
	}
}
else
{
	//gioi hoan quyen
	if( $rowcontent['status'] == 1 and $rowcontent['id'] > 0 )
	{
		$xtpl->parse( 'main.status' );
	}
	elseif( ! empty( $array_cat_pub_content ) ) // neu co quyen dang bai
	{
		$xtpl->parse( 'main.status0' );
	}
	else
	{
		if( ! empty( $array_censor_content ) ) // neu co quyen duyet bai thi
		{
			$xtpl->parse( 'main.status1.status0' );
		}
		$xtpl->parse( 'main.status1' );
	}
}

$xtpl->assign( 'UPLOADS_DIR_USER', $uploads_dir_user );
$xtpl->assign( 'UPLOAD_CURRENT', $currentpath );

$xtpl->parse( 'main' );
$contents .= $xtpl->text( 'main' );

if( $rowcontent['id'] > 0 )
{
	$op = '';
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';