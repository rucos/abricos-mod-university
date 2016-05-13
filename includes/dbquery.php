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
    	
   		UniversityQuery::AppendEduForm($db, $d->eduLevel, $programid);

	}
	
	public static function ProgramList(Ab_Database $db){
		$sql = "
			SELECT
				programid as id,
				code,
				name,
				remove
			FROM ".$db->prefix."un_program
		";
		return $db->query_read($sql);
	}
	
	public static function RemoveProgram(Ab_Database $db, $d){
		$sql = "
			UPDATE ".$db->prefix."un_program
			SET
				remove=".bkint($d->remove)."
			WHERE programid=".bkint($d->programid)."
			LIMIT 1
		";
		return $db->query_write($sql);
	}
	
	public static function ProgramItem(Ab_Database $db, $programid){
		$sql = "
			SELECT
					programid as id,
					code,
					name
			FROM ".$db->prefix."un_program
			WHERE programid=".$programid."
			LIMIT 1
		";
		return $db->query_first($sql);
	}
	
	public static function ProgramLevelList(Ab_Database $db, $programid){
		
		$sql = "
			SELECT
					edulevelid as id,
					level,
					eduform,
					educount
			FROM ".$db->prefix."un_edulevel
			WHERE programid=".bkint($programid)."
		";
		return $db->query_read($sql);
	}
	
	public static function EditProgram(Ab_Database $db, $d){
		$sql = "
			UPDATE ".$db->prefix."un_program
			SET
				code='".bkstr($d->code)."',
				name='".bkstr($d->name)."'
			WHERE programid=".bkint($d->programid)."
			LIMIT 1
		";
		$db->query_write($sql);
		
		$sql = "
			DELETE 
			FROM ".$db->prefix."un_edulevel
			WHERE programid=".bkint($d->programid)."
		";
		$db->query_write($sql);
		
		UniversityQuery::AppendEduForm($db, $d->eduLevel, $d->programid);
		
	}
	
	private function AppendEduForm($db, $eduLevel, $programid){
		
		$insert = "";
			
		foreach($eduLevel as $level => $eduForm){
			$pos = $eduForm / 1;
		
			if($pos !== 0){
				$level++;
				for($i = 1; $i <= 3; $i++){
					$educount = $eduForm[$i - 1];
		
					if($educount > 0){
						$insert .= "(".$programid.",".$level.",".$i.",".$educount."),";
					}
				}
			}
		}
		
		if($insert !== ""){
			$insert = substr($insert, 0, -1);
		
			$sql = "
		    		INSERT INTO ".$db->prefix."un_edulevel(programid, level, eduform, educount)
		    		VALUES ".$insert."
		    	";
			$db->query_write($sql);
		} else {
			return false;
		}
		
	}
	
	public static function EmployeesList(Ab_Database $db){
		$sql = "
			SELECT
					employeesid as id,
					FIO,
					remove
			FROM ".$db->prefix."un_employees
		";
		return $db->query_read($sql);
	}
}

?>