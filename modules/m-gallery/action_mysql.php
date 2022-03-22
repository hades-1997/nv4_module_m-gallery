<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Đắc Lợi (saka.dacloi@gmail.com)
 * @Copyright (C) 2021 . All rights reserved
 * @Createdate July 15, 2021, 02:32:08 PM
 */

if( ! defined( 'NV_IS_FILE_MODULES' ) ) die( 'Stop!!!' );

$sql_drop_module = array();
$array_table = array(
	'admins',
	'cat',
	'config_post',
	'rows'
);
$table = $db_config['prefix'] . '_' . $lang . '_' . $module_data;
$result = $db->query( 'SHOW TABLE STATUS LIKE ' . $db->quote( $table . '_%' ) );
while( $item = $result->fetch( ) )
{
	$name = substr( $item['name'], strlen( $table ) + 1 );
	if( preg_match( '/^' . $db_config['prefix'] . '\_' . $lang . '\_' . $module_data . '\_/', $item['name'] ) and ( preg_match( '/^([0-9]+)$/', $name ) or in_array( $name, $array_table ) or preg_match( '/^bodyhtml\_([0-9]+)$/', $name ) ) )
	{
		$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $item['name'];
	}
}


$sql_create_module = $sql_drop_module;


$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_cat (
	 catid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	 parentid smallint(5) unsigned NOT NULL DEFAULT '0',
	 title varchar(250) NOT NULL,
	 alias varchar(250) NOT NULL DEFAULT '',
	 weight smallint(5) unsigned NOT NULL DEFAULT '0',
	 sort smallint(5) NOT NULL DEFAULT '0',
	 lev smallint(5) NOT NULL DEFAULT '0',
	 viewcat varchar(50) NOT NULL DEFAULT 'viewcat_page_new',
	 numsubcat smallint(5) NOT NULL DEFAULT '0',
	 subcatid varchar(250) DEFAULT '',
	 inhome tinyint(1) unsigned NOT NULL DEFAULT '0',
	 admins text,
	 add_time int(11) unsigned NOT NULL DEFAULT '0',
	 edit_time int(11) unsigned NOT NULL DEFAULT '0',
	 groups_view varchar(250) DEFAULT '',
	 PRIMARY KEY (catid),
	 UNIQUE KEY alias (alias),
	 KEY parentid (parentid)
	) ENGINE=MyISAM";


	$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rows (
		id int(11) unsigned NOT NULL auto_increment,
		catid smallint(5) unsigned NOT NULL default '0',
		listcatid varchar(250) NOT NULL default '',
		admin_id mediumint(8) unsigned NOT NULL default '0',
		addtime int(11) unsigned NOT NULL default '0',
		edittime int(11) unsigned NOT NULL default '0',
		status tinyint(4) NOT NULL default '1',
		publtime int(11) unsigned NOT NULL default '0',
		exptime int(11) unsigned NOT NULL default '0',
		archive tinyint(1) unsigned NOT NULL default '0',
		title varchar(250) NOT NULL default '', 
		link varchar(250) NOT NULL default '',
		hometext text NOT NULL,
		homeimgfile varchar(250) default '',
		otherimage text,
		homeimgthumb tinyint(4) NOT NULL default '0',
		inhome tinyint(1) unsigned NOT NULL default '0',
		PRIMARY KEY (id),
		KEY catid (catid),
		KEY admin_id (admin_id),
		KEY title (title),
		KEY addtime (addtime),
		KEY publtime (publtime),
		KEY exptime (exptime),
		KEY status (status)
	   ) ENGINE=MyISAM";
   

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config_post (
	 group_id smallint(5) NOT NULL,
	 addcontent tinyint(4) NOT NULL,
	 postcontent tinyint(4) NOT NULL,
	 editcontent tinyint(4) NOT NULL,
	 delcontent tinyint(4) NOT NULL,
	 PRIMARY KEY (group_id)
	) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_admins (
	 userid mediumint(8) unsigned NOT NULL default '0',
	 catid smallint(5) NOT NULL default '0',
	 admin tinyint(4) NOT NULL default '0',
	 add_content tinyint(4) NOT NULL default '0',
	 pub_content tinyint(4) NOT NULL default '0',
	 edit_content tinyint(4) NOT NULL default '0',
	 del_content tinyint(4) NOT NULL default '0',
	 app_content tinyint(4) NOT NULL default '0',
	 UNIQUE KEY userid (userid,catid)
	) ENGINE=MyISAM";


$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'indexfile', 'viewcat_page_new')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'homewidth', '100')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'homeheight', '150')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'blockwidth', '52')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'blockheight', '75')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'speeditem', '400')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'items', '4')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'show_no_image', '')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'structure_upload', 'Ym')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'timecheckstatus', '0')";

