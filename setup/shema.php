<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

$charset = "CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'";
$updateManager = Ab_UpdateManager::$current;
$db = Abricos::$db;
$pfx = $db->prefix;

if ($updateManager->isInstall('0.1.0')){
	
	Abricos::GetModule('university')->permission->Install();
		/*
		 * 
		 * 1. Разделы 
		 * 
		 * */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_section(
				sectionid int(10) unsigned NOT NULL auto_increment,
				menuid int(10) unsigned NOT NULL default 0 COMMENT 'Позиция раздела в общей структуре сайта',
				remove tinyint(1) unsigned NOT NULL default 0 COMMENT 'Удален?',
				PRIMARY KEY (sectionid)
			)".$charset
		);
		/*
		 *
		* 2. Простой атрибут
		*
		* */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_simple_attribute(
				simpleid int(10) unsigned NOT NULL auto_increment,
				sectionid int(10) unsigned NOT NULL default 0 COMMENT 'Раздел',
				nameattribute TEXT NOT NULL default '' COMMENT 'Название атрибута',
				applyattribute varchar(255) NOT NULL default '' COMMENT 'Применяемый атрибут',
				locate tinyint(1) unsigned NOT NULL default 0 COMMENT 'Показывать?',
				remove tinyint(1) unsigned NOT NULL default 0 COMMENT 'Удален?',
				PRIMARY KEY (simpleid)
			)".$charset
		);
		
		/*
		 *
		* 3. Сложный атрибут
		*
		* */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_complex_attribute(
				complexid int(10) unsigned NOT NULL auto_increment,
				sectionid int(10) unsigned NOT NULL default 0 COMMENT 'Раздел',
				nameattribute TEXT NOT NULL default '' COMMENT 'Название атрибута',
				locate tinyint(1) unsigned NOT NULL default 0 COMMENT 'Установлен?',
				remove tinyint(1) unsigned NOT NULL default 0 COMMENT 'Удален?',
				PRIMARY KEY (complexid)
			)".$charset
		);
		
		/*
		 *
		* 4. Составной атрибут
		*
		* */
		
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_composite_attribute(
				compositeid int(10) unsigned NOT NULL auto_increment,
				complexid int(10) unsigned NOT NULL default 0 COMMENT 'Сложный атрибут',
				nameattribute TEXT NOT NULL default '' COMMENT 'Название атрибута',
				applyattribute varchar(255) NOT NULL default '' COMMENT 'Применяемый атрибут',
				remove tinyint(1) unsigned NOT NULL default 0 COMMENT 'Удален?',
				PRIMARY KEY (compositeid)
			)".$charset
		);
		
		/*
		 *
		* 5. Значение простого атрибута
		*
		* */
		
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_simple_value(
				splvalueid int(10) unsigned NOT NULL auto_increment,
				simpleid int(10) unsigned NOT NULL default 0 COMMENT 'Простой атрибут',
				value varchar(255) NOT NULL default '' COMMENT 'Значение атрибута',
				nameurl TEXT NOT NULL default '' COMMENT 'Название ссылки',
				namedoc varchar(255) NOT NULL default '' COMMENT 'Название документа',
				datedoc int(10) unsigned NOT NULL default 0 COMMENT 'Дата утверждения',
				PRIMARY KEY (splvalueid)
			)".$charset
		);
		
		/*
		 *
		* 6. Значение составного атрибута
		*
		* */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_composite_value(
				cmptvalueid int(10) unsigned NOT NULL auto_increment,
				compositeid int(10) unsigned NOT NULL default 0 COMMENT 'Составной атрибут',
				nameurl TEXT NOT NULL default '' COMMENT 'Название ссылки',
				namedoc varchar(255) NOT NULL default '' COMMENT 'Название документа',
				field varchar(20) NOT NULL default '' COMMENT 'Направление',
				subject varchar(255) default NULL COMMENT 'Название предмета',
				datedoc int(10) unsigned NOT NULL default 0 COMMENT 'Дата утверждения',
				folder varchar(20) NOT NULL default '' COMMENT 'Название директории',
				PRIMARY KEY (cmptvalueid)
			)".$charset
		);

}
?>