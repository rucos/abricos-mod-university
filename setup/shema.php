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


if ($updateManager->isInstall('0.1.6')){
	
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
		* 2. Атрибуты
		*
		* */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_attribute(
				attributeid int(10) unsigned NOT NULL auto_increment,
				sectionid int(10) unsigned NOT NULL default 0 COMMENT 'Раздел',
				complexid int(10) unsigned NOT NULL default 0 COMMENT 'id сложного атрибута',
				compositeid int(10) unsigned NOT NULL default 0 COMMENT 'id составного атрибута',
				typeattribute enum('simple','complex','composite','subcomposite') NOT NULL COMMENT 'Тип атрибута',
				nameattribute TEXT NOT NULL default '' COMMENT 'Название атрибута',
				applyattribute varchar(255) NOT NULL default '' COMMENT 'Применяемый атрибут',
				tablename varchar(50) NOT NULL default '' COMMENT 'Связующая таблица',
				fieldname varchar(255) NOT NULL default '' COMMENT 'Поля связующей таблицы',
				insertrow tinyint(1) unsigned NOT NULL default 0 COMMENT 'Для сложного атрибута добавление строк в таблицу: 1-авто, 2-вручную',
				locate tinyint(1) unsigned NOT NULL default 1 COMMENT 'Показывать?',
				remove tinyint(1) unsigned NOT NULL default 0 COMMENT 'Удален?',
				PRIMARY KEY (attributeid)
			)".$charset
		);
		
		/*
		 *
		* 3. Значение атрибута
		*
		* */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_value(
				valueid int(10) unsigned NOT NULL auto_increment,
				attributeid int(10) unsigned NOT NULL default 0 COMMENT 'id атрибута',
				numrow tinyint(2) unsigned NOT NULL default 0 COMMENT 'позиция значения в таблице',
				relationid int(10) unsigned NOT NULL default 0 COMMENT 'id поля связующей таблицы',
				mainid int(10) unsigned NOT NULL default 0 COMMENT 'id ключевго поля главной таблицы',
				view enum('value','url','file') NOT NULL COMMENT 'Вид значения',
				value TEXT NOT NULL default '' COMMENT 'Значение атрибута/url',
				nameurl TEXT NOT NULL default '' COMMENT 'Название ссылки',
				remove tinyint(1) unsigned NOT NULL default 0 COMMENT 'Удален?',
				PRIMARY KEY (valueid)
			)".$charset
		);
		
		/*
		 *
		* 4. Направления, специальности
		*
		* */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_program(
				programid int(10) unsigned NOT NULL auto_increment,
				code varchar(20) default NULL COMMENT 'Код направления',
				name varchar(255) default NULL COMMENT 'Направление',
				remove tinyint(1) unsigned NOT NULL default 0 COMMENT 'Удален?',
				PRIMARY KEY (programid)
			)".$charset
		);
		
		/*
		 *
		* 5. Уровень образования
		*
		* */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_edulevel(
				edulevelid int(10) unsigned NOT NULL auto_increment,
				programid int(10) unsigned NOT NULL default 0 COMMENT 'id направления',
				level enum('бакалавриат академический','бакалавриат прикладной','специалитет') NOT NULL COMMENT 'уровень образования',
				remove tinyint(1) unsigned NOT NULL default 0 COMMENT 'Удален?',
				PRIMARY KEY (edulevelid),
				UNIQUE KEY level (programid,level)
			)".$charset
		);
		
		/*
		 *
		 * 6. Формы обучения
		 *
		 * */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_eduform(
				eduformid int(10) unsigned NOT NULL auto_increment,
				edulevelid int(10) unsigned NOT NULL default 0 COMMENT 'id уровня образования',
				och tinyint(1) unsigned NOT NULL default 0 COMMENT 'Срок обучения очная форма',
				ochzaoch tinyint(1) unsigned NOT NULL default 0 COMMENT 'Срок обучения очно-заочная форма',
				zaoch tinyint(1) unsigned NOT NULL default 0 COMMENT 'Срок обучения заочная форма',
				PRIMARY KEY (eduformid)
			)".$charset
		);
		
		/*
		 *
		* 7. Сотрудники
		*
		* */
		$db->query_write("
			CREATE TABLE IF NOT EXISTS ".$pfx."un_employees(
				employeesid int(10) unsigned NOT NULL auto_increment,
				fio varchar(255) default NULL COMMENT 'ФИО сотрудника',
				post varchar(255) default NULL COMMENT 'Должность',
				telephone varchar(50) default NULL COMMENT 'Контактный телефон',
				email varchar(254) default NULL COMMENT 'Электронный адрес',
				remove tinyint(1) unsigned NOT NULL default 0 COMMENT 'Удален?',
				PRIMARY KEY (employeesid)
			)".$charset
		);
		
		/*
		 *
		* Добавление основного раздела в структуру сайта
		*
		* */
		require_once('modules/university/includes/classes/section.php');
		
		$sect = new Section();
		
}
?>