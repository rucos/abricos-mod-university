<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

class UniversityQuery {
	
	public static function SectionList(Ab_Database $db){
		$sql = "
			SELECT
					s.sectionid as id,
					m.title
			FROM ".$db->prefix."un_section s
			INNER JOIN ".$db->prefix."sys_menu m ON s.menuid = m.menuid
		";
		return $db->query_read($sql);
	}
	
	public static function AttributeList(Ab_Database $db, $sectionid){
		$sql = "
			SELECT
					attributeid as id,
					complexid,
					typeattribute,
					nameattribute,
					applyattribute,
					locate,
					remove
			FROM ".$db->prefix."un_attribute
			WHERE sectionid=".bkint($sectionid)."
			
		";
		return $db->query_read($sql);
	}
	
	public static function AppendAttribute(Ab_Database $db, $d){
		$sql = "
			INSERT INTO ".$db->prefix."un_attribute(sectionid, complexid, typeattribute, nameattribute, applyattribute, locate)
			VALUES (
					".bkint($d->sectionid).",
					".bkint($d->complexid).",
					'".bkstr($d->type)."',
					'".bkstr($d->nameattribute)."',	
					'".bkstr($d->applyattribute)."',	
					".bkint($d->locate)."
			)
		";
		
		$db->query_write($sql);
	}
	
	public static function EditAttribute(Ab_Database $db, $d){
		$sql = "
			UPDATE ".$db->prefix."un_attribute
			SET
					sectionid = ".bkint($d->sectionid).",
					complexid = ".bkint($d->complexid).",
					typeattribute = '".bkstr($d->type)."',
					nameattribute = '".bkstr($d->nameattribute)."',
					applyattribute = '".bkstr($d->applyattribute)."',
					locate = ".bkint($d->locate)."
			WHERE attributeid=".bkint($d->compositid)."
			LIMIT 1
		";
	
		$db->query_write($sql);
	}
	
}

?>