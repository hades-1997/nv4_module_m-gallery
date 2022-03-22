<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Đắc Lợi (saka.dacloi@gmail.com)
 * @Copyright (C) 2021 . All rights reserved
 * @Createdate July 15, 2021, 02:32:08 PM
 */


if( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) )
	die( 'Stop!!!' );

if( $NV_IS_ADMIN_MODULE )
{
	define( 'NV_IS_ADMIN_MODULE', true );
}

if( $NV_IS_ADMIN_FULL_MODULE )
{
	define( 'NV_IS_ADMIN_FULL_MODULE', true );
}

$array_viewcat_full = array(
	'viewcat_page_new' => $lang_module['viewcat_page_new']
	
);
$array_viewcat_nosub = array(
	'viewcat_page_new' => $lang_module['viewcat_page_new']

);

$array_allowed_comm = array(
	$lang_global['no'],
	$lang_global['level6'],
	$lang_global['level4']
);

define( 'NV_IS_FILE_ADMIN', true );
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';

global $global_array_cat;
$global_array_cat = array();
$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_cat ORDER BY sort ASC';
$result = $db->query( $sql );
while( $row = $result->fetch() )
{
	$global_array_cat[$row['catid']] = $row;
}

/**
 * nv_fix_cat_order()
 *
 * @param integer $parentid
 * @param integer $order
 * @param integer $lev
 * @return
 */
function nv_fix_cat_order( $parentid = 0, $order = 0, $lev = 0 )
{
	global $db, $module_data;

	$sql = 'SELECT catid, parentid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_cat WHERE parentid=' . $parentid . ' ORDER BY weight ASC';
	$result = $db->query( $sql );
	$array_cat_order = array();
	while( $row = $result->fetch() )
	{
		$array_cat_order[] = $row['catid'];
	}
	$result->closeCursor();
	$weight = 0;
	if( $parentid > 0 )
	{
		++$lev;
	}
	else
	{
		$lev = 0;
	}
	foreach( $array_cat_order as $catid_i )
	{
		++$order;
		++$weight;
		$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_cat SET weight=' . $weight . ', sort=' . $order . ', lev=' . $lev . ' WHERE catid=' . intval( $catid_i );
		$db->query( $sql );
		$order = nv_fix_cat_order( $catid_i, $order, $lev );
	}
	$numsubcat = $weight;
	if( $parentid > 0 )
	{
		$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_cat SET numsubcat=' . $numsubcat;
		if( $numsubcat == 0 )
		{
			$sql .= ",subcatid='', viewcat='viewcat_page_new'";
		}
		else
		{
			$sql .= ",subcatid='" . implode( ',', $array_cat_order ) . "'";
		}
		$sql .= ' WHERE catid=' . intval( $parentid );
		$db->query( $sql );
	}
	return $order;
}


/**
 * nv_show_cat_list()
 *
 * @param integer $parentid
 * @return
 */
function nv_show_cat_list( $parentid = 0 )
{
	global $db, $lang_module, $lang_global, $module_name, $module_data, $array_viewcat_full, $array_viewcat_nosub, $array_cat_admin, $global_array_cat, $admin_id, $global_config, $module_file;

	$xtpl = new XTemplate( 'cat_list.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );

	// Cac chu de co quyen han
	$array_cat_check_content = array();
	foreach( $global_array_cat as $catid_i => $array_value )
	{
		if( defined( 'NV_IS_ADMIN_MODULE' ) )
		{
			$array_cat_check_content[] = $catid_i;
		}
		elseif( isset( $array_cat_admin[$admin_id][$catid_i] ) )
		{
			if( $array_cat_admin[$admin_id][$catid_i]['admin'] == 1 )
			{
				$array_cat_check_content[] = $catid_i;
			}
			elseif( $array_cat_admin[$admin_id][$catid_i]['add_content'] == 1 )
			{
				$array_cat_check_content[] = $catid_i;
			}
			elseif( $array_cat_admin[$admin_id][$catid_i]['pub_content'] == 1 )
			{
				$array_cat_check_content[] = $catid_i;
			}
			elseif( $array_cat_admin[$admin_id][$catid_i]['edit_content'] == 1 )
			{
				$array_cat_check_content[] = $catid_i;
			}
		}
	}

	// Cac chu de co quyen han
	if( $parentid > 0 )
	{
		$parentid_i = $parentid;
		$array_cat_title = array();
		while( $parentid_i > 0 )
		{
			$array_cat_title[] = "<a href=\"" . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=cat&amp;parentid=" . $parentid_i . "\"><strong>" . $global_array_cat[$parentid_i]['title'] . "</strong></a>";
			$parentid_i = $global_array_cat[$parentid_i]['parentid'];
		}
		sort( $array_cat_title, SORT_NUMERIC );

		$xtpl->assign( 'CAT_TITLE', implode( ' &raquo; ', $array_cat_title ) );
		$xtpl->parse( 'main.cat_title' );
	}

	$sql = 'SELECT catid, parentid, title, weight, viewcat, numsubcat, inhome FROM ' . NV_PREFIXLANG . '_' . $module_data . '_cat WHERE parentid = ' . $parentid . ' ORDER BY weight ASC';
	$rowall = $db->query( $sql )->fetchAll( 3 );
	$num = sizeof( $rowall );
	$a = 1;
	$array_inhome = array(
		$lang_global['no'],
		$lang_global['yes']
	);

	foreach ($rowall as $row)
	{
		list( $catid, $parentid, $title, $weight, $viewcat, $numsubcat, $inhome ) = $row;
		if( defined( 'NV_IS_ADMIN_MODULE' ) )
		{
			$check_show = 1;
		}
		else
		{
			$array_cat = GetCatidInParent( $catid );
			$check_show = array_intersect( $array_cat, $array_cat_check_content );
		}

		if( ! empty( $check_show ) )
		{
			$array_viewcat = ($numsubcat > 0) ? $array_viewcat_full : $array_viewcat_nosub;
			if( ! array_key_exists( $viewcat, $array_viewcat ) )
			{
				$viewcat = 'viewcat_page_new';
				$stmt = $db->prepare( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_cat SET viewcat= :viewcat WHERE catid=' . intval( $catid ) );
				$stmt->bindParam( ':viewcat', $viewcat, PDO::PARAM_STR );
				$stmt->execute();
			}

			$admin_funcs = array();
			$weight_disabled = $func_cat_disabled = true;
			if( defined( 'NV_IS_ADMIN_MODULE' ) or (isset( $array_cat_admin[$admin_id][$catid] ) and $array_cat_admin[$admin_id][$catid]['add_content'] == 1) )
			{
				$func_cat_disabled = false;
				$admin_funcs[] = "<em class=\"fa fa-plus fa-lg\">&nbsp;</em> <a href=\"" . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=content&amp;catid=" . $catid . "&amp;parentid=" . $parentid . "\">" . $lang_module['content_add'] . "</a>\n";
			}
			if( defined( 'NV_IS_ADMIN_MODULE' ) or ($parentid > 0 and isset( $array_cat_admin[$admin_id][$parentid] ) and $array_cat_admin[$admin_id][$parentid]['admin'] == 1) )
			{
				$func_cat_disabled = false;
				$admin_funcs[] = "<em class=\"fa fa-edit fa-lg\">&nbsp;</em> <a class=\"\" href=\"" . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=cat&amp;catid=" . $catid . "&amp;parentid=" . $parentid . "#edit\">" . $lang_global['edit'] . "</a>\n";
			}
			if( defined( 'NV_IS_ADMIN_MODULE' ) or ($parentid > 0 and isset( $array_cat_admin[$admin_id][$parentid] ) and $array_cat_admin[$admin_id][$parentid]['admin'] == 1) )
			{
				$weight_disabled = false;
				$admin_funcs[] = "<em class=\"fa fa-trash-o fa-lg\">&nbsp;</em> <a href=\"javascript:void(0);\" onclick=\"nv_del_cat(" . $catid . ")\">" . $lang_global['delete'] . "</a>";
			}

			$xtpl->assign( 'ROW', array(
				'catid' => $catid,
				'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=cat&amp;parentid=' . $catid,
				'title' => $title,
				'adminfuncs' => implode( '&nbsp;-&nbsp;', $admin_funcs )
			) );

			if( $weight_disabled )
			{
				$xtpl->assign( 'STT', $a );
				$xtpl->parse( 'main.data.loop.stt' );
			}
			else
			{
				for( $i = 1; $i <= $num; ++$i )
				{
					$xtpl->assign( 'WEIGHT', array(
						'key' => $i,
						'title' => $i,
						'selected' => $i == $weight ? ' selected="selected"' : ''
					) );
					$xtpl->parse( 'main.data.loop.weight.loop' );
				}
				$xtpl->parse( 'main.data.loop.weight' );
			}

			if( $func_cat_disabled )
			{
				$xtpl->assign( 'INHOME', $array_inhome[$inhome] );
				$xtpl->parse( 'main.data.loop.disabled_inhome' );

				$xtpl->assign( 'VIEWCAT', $array_viewcat[$viewcat] );
				$xtpl->parse( 'main.data.loop.disabled_viewcat' );

				
			}
			else
			{
				foreach( $array_inhome as $key => $val )
				{
					$xtpl->assign( 'INHOME', array(
						'key' => $key,
						'title' => $val,
						'selected' => $key == $inhome ? ' selected="selected"' : ''
					) );
					$xtpl->parse( 'main.data.loop.inhome.loop' );
				}
				$xtpl->parse( 'main.data.loop.inhome' );

				foreach( $array_viewcat as $key => $val )
				{
					$xtpl->assign( 'VIEWCAT', array(
						'key' => $key,
						'title' => $val,
						'selected' => $key == $viewcat ? ' selected="selected"' : ''
					) );
					$xtpl->parse( 'main.data.loop.viewcat.loop' );
				}
				$xtpl->parse( 'main.data.loop.viewcat' );

				

			}

			if( $numsubcat )
			{
				$xtpl->assign( 'NUMSUBCAT', $numsubcat );
				$xtpl->parse( 'main.data.loop.numsubcat' );
			}

			$xtpl->parse( 'main.data.loop' );
			++$a;
		}
	}

	if( $num > 0 )
	{
		$xtpl->parse( 'main.data' );
	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	return $contents;
}

/**
 * GetCatidInParent()
 *
 * @param mixed $catid
 * @return
 */
function GetCatidInParent( $catid )
{
	global $global_array_cat;
	$array_cat = array();
	$array_cat[] = $catid;
	$subcatid = explode( ',', $global_array_cat[$catid]['subcatid'] );
	if( ! empty( $subcatid ) )
	{
		foreach( $subcatid as $id )
		{
			if( $id > 0 )
			{
				if( $global_array_cat[$id]['numsubcat'] == 0 )
				{
					$array_cat[] = $id;
				}
				else
				{
					$array_cat_temp = GetCatidInParent( $id );
					foreach( $array_cat_temp as $catid_i )
					{
						$array_cat[] = $catid_i;
					}
				}
			}
		}
	}
	return array_unique( $array_cat );
}

/**
 * redriect()
 *
 * @param string $msg1
 * @param string $msg2
 * @param mixed $nv_redirect
 * @return
 */
function redriect( $msg1 = '', $msg2 = '', $nv_redirect, $autoSaveKey = '' )
{
	global $global_config, $module_file, $module_name;
	$xtpl = new XTemplate( 'redriect.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );

	if( empty( $nv_redirect ) )
	{
		$nv_redirect = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
	}
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'NV_REDIRECT', $nv_redirect );
	$xtpl->assign( 'MSG1', $msg1 );
	$xtpl->assign( 'MSG2', $msg2 );

	if( ! empty( $autoSaveKey ) )
	{
		$xtpl->assign( 'AUTOSAVEKEY', $autoSaveKey );
		$xtpl->parse( 'main.removelocalstorage' );
	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
	exit();
}