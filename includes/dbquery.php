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
					m.name,
					m.title
			FROM ".$db->prefix."un_section s
			INNER JOIN ".$db->prefix."sys_menu m ON s.menuid = m.menuid
		";
		return $db->query_read($sql);
	}
	
	public static function AttributeList(Ab_Database $db, $d){
		
		$where = "sectionid=".bkint($d->sectionid)." AND remove=0";

		if($d->isValue){
			$where .= " AND typeattribute IN (1, 2)";
		}
		
		$sql = "
			SELECT
					attributeid as id,
					complexid,
					compositeid,
					typeattribute,
					nameattribute,
					applyattribute,
					tablename,
					locate
			FROM ".$db->prefix."un_attribute
			WHERE ".$where."
		";
		return $db->query_read($sql);
	}
	
	public static function AppendAttribute(Ab_Database $db, $d){
		$sql = "
			INSERT INTO ".$db->prefix."un_attribute(sectionid, complexid, typeattribute, nameattribute, applyattribute, tablename, locate)
			VALUES (
					".bkint($d->sectionid).",
					".bkint($d->complexid).",
					'".bkstr($d->type)."',
					'".bkstr($d->nameattribute)."',	
					'".bkstr($d->applyattribute)."',
					'".bkstr($d->tablename)."',
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
					tablename = '".bkstr($d->tablename)."',
					locate = ".bkint($d->locate)."
			WHERE attributeid=".bkint($d->compositid)."
			LIMIT 1
		";
	
		$db->query_write($sql);
	}
	
	public static function RemoveAttribute(Ab_Database $db, $d){
		$where = "attributeid=".bkint($d->compositid);
		
		if($d->isComplex){
			$where .=  " OR complexid=".bkint($d->compositid);
		}
		
		$sql = "
			UPDATE ".$db->prefix."un_attribute
			SET
				remove=1
			WHERE ".$where."
		";
		
		return $db->query_write($sql);
	}
	
	public static function ValueAttributeList(Ab_Database $db, $sectionid){
		$sql = "
			SELECT
					a.attributeid,
					a.nameattribute,
					s.value,
					s.nameurl,
					s.namedoc,
					s.datedoc
			FROM ".$db->prefix."un_attribute a
			LEFT JOIN ".$db->prefix."un_value s ON a.attributeid=s.attributeid
			WHERE a.sectionid=".bkint($sectionid)." AND a.remove=0
		";
		$rows = $db->query_read($sql);
		while (($d = $db->fetch_array($rows))){
			print_r($d);
		}
	
	}
}

?>