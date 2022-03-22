<?php
/**
 * @Project NUKEVIET 4.x
 * @Author Đắc Lợi (saka.dacloi@gmail.com)
 * @Copyright (C) 2021 . All rights reserved
 * @Createdate July 15, 2021, 02:32:08 PM
 */


if( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$module_version = array(
	'name' => 'Slider Gallery', // Tieu de module
	'modfuncs' => 'main,rss', // Cac function co block
	'is_sysmod' => 0, // 1:0 => Co phai la module he thong hay khong
	'virtual' => 1, // 1:0 => Co cho phep ao hao module hay khong
	'version' => '4.4.00', // Phien ban cua modle
	'date' => 'Tu, 218 July 2021 00:00:00 GMT', // Ngay phat hanh phien ban
	'author' => 'DacLoi (Saka.dacloi@gmail.com)', // Tac gia
	'note' => '', // Ghi chu
	'uploads_dir' => array( $module_name)
);