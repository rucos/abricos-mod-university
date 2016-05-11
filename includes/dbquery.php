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
	
	public static function SimpleValueAttributeList(Ab_Database $db, $attridd){
		
		$sql = "
			SELECT
					valueid as id,
					relationid,
					value,
					nameurl,
					namedoc,
					subject,
					datedoc,
					folder,
					remove
			FROM ".$db->prefix."un_value
			WHERE attributeid=".bkint($attridd)."
		";
		
		return $db->query_read($sql);
	}
	
	public static function AppendValueAttribute(Ab_Database $db, $d){
		$sql = "
			INSERT INTO ".$db->prefix."un_value(attributeid, value, nameurl, namedoc, subject, datedoc, folder)
			VALUES (
					".bkint($d->atrid).",
					'".bkstr($d->value)."',
					'".bkstr($d->nameurl)."',
					'".bkstr($d->namedoc)."',
					'".bkstr($d->subject)."',
					".bkint($d->datedoc).",
					'".bkstr($d->folder)."'
			)
		";
	
		return $db->query_write($sql);
	}
	
	public static function EditValueAttribute(Ab_Database $db, $d){
		$sql = "
			UPDATE ".$db->prefix."un_value
			SET
				attributeid=".bkint($d->atrid).",
				value='".bkstr($d->value)."',
				nameurl='".bkstr($d->nameurl)."',
				namedoc='".bkstr($d->namedoc)."',
				subject='".bkstr($d->subject)."',
				datedoc=".bkint($d->datedoc).",
				folder='".bkstr($d->folder)."'
			WHERE valueid=".bkint($d->id)."
			LIMIT 1
		";
	
		return $db->query_write($sql);
	}
	
	public static function RemoveValueAttribute(Ab_Database $db, $d){
	
		$sql = "
			UPDATE ".$db->prefix."un_value
			SET
				remove=".bkint($d->remove)."
			WHERE valueid=".bkint($d->valueid)."
			LIMIT 1
		";
	
		return $db->query_write($sql);
	}
	
	public static function AppendProgram(Ab_Database $db, $d){
		$sql = "
			INSERT INTO ".$db->prefix."un_program(code, name)
			VALUES (
					'".bkstr($d->code)."',
					'".bkstr($d->name)."'
			)
		";
		$db->query_write($sql);
    	$programid = mysql_insert_id();
    	
	    foreach($d->eduLevel as $keyLvl => $eduLevel){
	    	if($eduLevel !== ''){
	    		$sql = "
	    				INSERT INTO ".$db->prefix."un_edulevel(programid, level)
	    				VALUES (
	    						".$programid.",
	    						".++$keyLvl."
	    				)
	    			";
	    		$db->query_write($sql);
	    		$eduLevelid = mysql_insert_id();
	    		$insert = "";
	    		
	    		foreach($eduLevel as $keyForm => $eduFrom){
	    			 if($eduFrom !== ''){
	    			 	$insert .= "(".$eduLevelid.",".++$keyForm.",".bkint($eduFrom)."),";
	    			 }
	    		}
	    		$insert = substr($insert, 0, -1);
	    		
	    		$sql = "
	    				INSERT INTO ".$db->prefix."un_eduform(edulevelid, eduform, educount)
	    				VALUES ".$insert."
	    			";
	    		$db->query_write($sql);
	    	}
    	}
	}
}

?>