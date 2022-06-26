<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DACLOI
 * @Copyright (C) 2021 . All rights reserved
 * @Createdate JULY 18, 2021, 02:32:08 PM
 */


if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );


/**
 * slider_thumbs()
 * front-end thumbs create
 *
 */
if( ! nv_function_exists( 'slider_thumbs' ) )
{
	function slider_thumbs( $id, $file, $module_upload, $width = 200, $height = 150, $quality = 90 )
	{
		if( $width >= $height ) $rate = $width / $height;
		else  $rate = $height / $width;

		$image = NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $file;
 
		if( $file != '' and file_exists( $image ) )
		{
			$imgsource = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $file;
			$imginfo = nv_is_image( $image );

			$basename = $module_upload . '_' . $width . 'x' . $height . '-' . $id . '-' . md5_file( $image ) . '.' . $imginfo['ext'];

			if( file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload. '/' . $basename ) )
			{
				$imgsource = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload. '/' . $basename;
			}
			else
			{

				$_image = new NukeViet\Files\Image( $image, NV_MAX_WIDTH, NV_MAX_HEIGHT );

				if( $imginfo['width'] <= $imginfo['height'] )
				{
					$_image->resizeXY( $width, 0 );

				}
				elseif( ( $imginfo['width'] / $imginfo['height'] ) < $rate )
				{
					$_image->resizeXY( $width, 0 );
				}
				elseif( ( $imginfo['width'] / $imginfo['height'] ) >= $rate )
				{
					$_image->resizeXY( 0, $height );
				}

				$_image->cropFromCenter( $width, $height );

				$_image->save( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/', $basename, $quality );

				if( file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload. '/' . $basename ) )
				{
					$imgsource = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload. '/' . $basename;
				}
			}
		}
		elseif( nv_is_url( $file ) )
		{
			$imgsource = $file;
		}
		else
		{
			$imgsource = '';
		}
		return $imgsource;
	}
}


if( ! nv_function_exists( 'nv_block_mslider' ) )
{
	function nv_block_config_sgallery( $module, $data_block, $lang_block )
	{
		global $site_mods, $nv_Cache;

		$html = '';
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['catid'] . '</td>';
		$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_cat ORDER BY sort ASC';
		$list = $nv_Cache->db( $sql, '', $module );
		$html .= '<td>';
		foreach( $list as $l )
		{
			$xtitle_i = '';

			if( $l['lev'] > 0 )
			{
				for( $i = 1; $i <= $l['lev']; ++$i )
				{
					$xtitle_i .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}
			if( !isset($data_block['catid']) AND empty($data_block['catid']) ){
				$data_block['catid'] = array();
			}
			$html .= $xtitle_i . '<label><input type="checkbox" name="config_catid[]" value="' . $l['catid'] . '" ' . ( ( in_array( $l['catid'], $data_block['catid'] ) ) ? ' checked="checked"' : '' ) . '</input>' . $l['title'] . '</label><br />';
		}
		$html .= '</td>';
		$html .= '</tr>';
		
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['numrow'] . '</td>';
		$html .= '<td><input type="text" class="form-control w200" name="config_numrow" size="5" value="' . $data_block['numrow'] . '"/></td>';
		$html .= '</tr>';
		
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['block_template'] . '</td>';
        $html .= '<td>';
        $block_template = array( 'block_flex' => $lang_block['block_flex'] );
        $html .= '<select name="config_block_template" class="form-control w100 pull-left">';
		foreach ($block_template as $key => $value) {
            $html .= '<option value="' . $key . '" ' . ($data_block['block_template'] == $key ? 'selected="selected"' : '') . '>' . $value . '</option>';
        }
		$html .= '</tr>';
		return $html;
	}

	function nv_block_config_sgallery_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['catid'] = $nv_Request->get_array( 'config_catid', 'post', array() );
		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		$return['config']['block_template'] = $nv_Request->get_string( 'config_block_template', 'post', 0 );
		return $return;
	}

	function nv_block_mslider( $block_config )
	{
		global $module_array_cat,$array_config,$module_name, $module_info, $site_mods, $module_config, $global_config, $db, $nv_Cache, $blockID, $lang_module,$module_file, $my_head, $module_upload;
		$module = $block_config['module'];        

		$show_no_image = $module_config[$module]['show_no_image'];

		if( empty( $block_config['catid'] ) ) return '';
		$catid = implode( ',', $block_config['catid'] );
		
		$db->sqlreset()
			->select( 'id, catid, title, homeimgfile,otherimage, homeimgthumb, hometext, link, publtime' )
			->from( NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_rows' )
			->where( 'status= 1 AND catid IN(' . $catid . ')' )
			->order( 'publtime DESC' )
			->limit( $block_config['numrow'] );
		$list = $nv_Cache->db( $db->sql(), '', $module );

		if( ! empty( $list ) )
		{
			if( empty($block_config['block_template']) ){
				$block_config['block_template'] = 'block_flex';
			}
			$block_tpl = $block_config['block_template'];
			if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $site_mods[$module]['module_file'] . '/'. $block_tpl . '.tpl' ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = 'default';
			}
			
		//	var_dump( );die;
			$xtpl = new XTemplate( $block_tpl . '.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] );
			$xtpl->assign( 'TEMPLATE', $block_theme );
			//$xtpl->assign( 'BLOCKID', $blockID );
			$xtpl->assign( 'MODULEINFO',$site_mods[$module]['module_file'] );
			$xtpl->assign( 'DATAGALLERY',  $module_config[$module] );
            $count = 1;
			foreach( $list as $l )
			{
				//var_dump($l['otherimage']);die;
				if( $l['homeimgthumb'] == 1 )
				{
					$l['thumb'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module . '/' . $l['homeimgfile'];
				}
				elseif( $l['homeimgthumb'] == 2 )
				{
					$l['thumb'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module . '/' . $l['homeimgfile'];
				}
				elseif( $l['homeimgthumb'] == 3 )
				{
					$l['thumb'] = $l['homeimgfile'];
				}
				elseif( ! empty( $show_no_image ) )
				{
					$l['thumb'] = NV_MY_DOMAIN . NV_BASE_SITEURL . $show_no_image;
				}
				else
				{
					$l['thumb'] = '';
				}
                $l['active'] = ($count == 1 ? 'active' : '');
                $count++;
				$xtpl->assign( 'ROW', $l );
				if( ! empty( $l['thumb'] ) ) 
					$xtpl->parse( 'main.loop.img' );
				if( ! empty( $l['link'] ) ) 
					$xtpl->parse( 'main.loop.link' );
				if( ! empty( $l['hometext'] ) )
					$xtpl->parse( 'main.loop.hometext' );

				if( !empty($l['otherimage']))
				{
					$l['otherimage'] = explode('|',$l['otherimage']);
					if(is_array($l['otherimage']))
					{
						foreach($l['otherimage'] as $pro_img)
						{
							$pro_img_i = array();
							$pro_img_i['img_large'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module . '/' . $pro_img;
							$pro_img_i['img_small'] = NV_BASE_SITEURL . NV_ASSETS_DIR . '/' . $module_upload . '/' . $pro_img;
							$xtpl->assign( 'PRO_IMG', $pro_img_i );
							$xtpl->parse( 'main.loop.pro_img' );
						}
					}
				}	


				$xtpl->parse( 'main.loop' );

			}
			
			if( ! defined( 'FLEX' ) )
			{
				define( 'FLEX', true );
				//$my_head .= '<link href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] . '/plugins/flex/flexslider.css" type="text/css" media="all" rel="stylesheet" />';
				//$my_head .= '<script src="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] . '/plugins/flex/jquery.flexslider.js"></script>';
			}
			
			if( ! defined( 'BX' ) )
			{
				define( 'BX', true );
				$xtpl->parse( 'main.BX' );
			}
			
			if( ! defined( 'SKITTER' ) )
			{
				define( 'SKITTER', true );
				$xtpl->parse( 'main.SKITTER' );
			}
			
			if( ! defined( 'CAMERA' ) )
			{
				define( 'CAMERA', true );
				$xtpl->parse( 'main.CAMERA' );
			}
			if( ! defined( 'MAGIC' ) )
			{
				define( 'MAGIC', true );
				$xtpl->parse( 'main.MAGIC' );
			//	$my_head .= '<link href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] . '/plugins/magicslideshow/magicslideshow.css" type="text/css" media="all" rel="stylesheet" />';
			//	$my_head .= '<script src="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] . '/plugins/magicslideshow/magicslideshow.js"></script>';
			}
			$xtpl->parse( 'main' );
			return $xtpl->text( 'main' );
		}
	}
}
if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name, $global_array_cat, $module_array_cat, $nv_Cache;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		if( $module == $module_name )
		{
			$module_array_cat = $global_array_cat;
			unset( $module_array_cat[0] );
		}
		else
		{
			$module_array_cat = array();
			$sql = 'SELECT catid, parentid, title, alias, viewcat, subcatid,  inhome, groups_view FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_cat ORDER BY sort ASC';
			$list = $nv_Cache->db( $sql, 'catid', $module );
			
			foreach( $list as $l )
			{
				$module_array_cat[$l['catid']] = $l;
				$module_array_cat[$l['catid']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
			}
		}
		$content = nv_block_mslider( $block_config );
	}
}
