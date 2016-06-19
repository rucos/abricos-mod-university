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
	
	public static function SectionItem(Ab_Database $db, $sectionid){
		
		$sql = "
			SELECT
					s.sectionid as id,
					m.name,
					m.title
			FROM ".$db->prefix."sys_menu m
			INNER JOIN ".$db->prefix."un_section s ON m.menuid=s.menuid
			WHERE s.sectionid=".bkint($sectionid)."
			LIMIT 1
		";
		return $db->query_first($sql);
	}
	
	public static function SectionItemUpload(Ab_Database $db, $attrid){
		$sql = "
			SELECT
					sectionid as sid
			FROM ".$db->prefix."un_attribute
			WHERE attributeid=".bkint($attrid)."
			LIMIT 1
		";
		$sectionid = $db->query_first($sql);
		
		$sql = "
			SELECT
					m.name
			FROM ".$db->prefix."sys_menu m
			INNER JOIN ".$db->prefix."un_section s ON m.menuid=s.menuid
			WHERE s.sectionid=".$sectionid['sid']."
			LIMIT 1
		";
		return $db->query_first($sql);
	}
	
	public static function AttributeList(Ab_Database $db, $d){
		
		$where = "sectionid=".bkint($d->sectionid)." AND remove=0";

		if($d->isValue){
			if($d->complexid !== 0){
				$where .= " AND typeattribute IN (3, 4) AND complexid=".bkint($d->complexid)."";
			} else {
				$where .= " AND typeattribute IN (1, 2)";
			}
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
					view,
					value,
					nameurl,
					remove
			FROM ".$db->prefix."un_value
			WHERE attributeid=".bkint($attridd)."
		";
		
		return $db->query_read($sql);
	}
	
	public static function ValueAttributeItem(Ab_Database $db, $valueid){
	
		$sql = "
			SELECT
					valueid as id,
					relationid,
					view,
					value,
					nameurl,
					remove
			FROM ".$db->prefix."un_value
			WHERE valueid=".bkint($valueid)."
			LIMIT 1
		";
	
		return $db->query_first($sql);
	}
	
	public static function AppendValueAttribute(Ab_Database $db, $d){
		$sql = "
			INSERT INTO ".$db->prefix."un_value(attributeid, value, nameurl, view, numrow)
			VALUES (
					".bkint($d->atrid).",
					'".bkstr($d->value)."',
					'".bkstr($d->nameurl)."',
					'".bkstr($d->view)."',
					".bkint($d->numrow)."							
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
				nameurl='".bkstr($d->nameurl)."'
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
    	
		    	$sql = "
					SELECT 
		    			MAX(programid) as m
		    		FROM ".$db->prefix."un_program
		    	";
		    	$numrow = $db->query_first($sql);
		    	
		    	$sql = "
					SELECT 
		    			attributeid as id
		    		FROM ".$db->prefix."un_attribute
		    		WHERE tablename='program'
				";
		    	$rows = $db->query_read($sql);
		    	$insert = "";
		    	
		    	while ($dd = $db->fetch_array($rows)){
		    		$insert .= "(".$dd['id'].",".$numrow['m'].",".$programid.",".'"programid"'."),"; 
		    	}
		    	
		    	$insert = substr($insert, 0, -1);
		    	
		    	$sql = "
					INSERT INTO ".$db->prefix."un_value(attributeid, numrow, relationid, value)
					VALUES ".$insert."
				";
		    	$db->query_write($sql);
    	
   		UniversityQuery::AppendEduForm($db, $d->eduLevel, $programid, $numrow['m']);
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
					f.eduformid as id,
					l.level,
					f.och,
					f.ochzaoch,
					f.zaoch
			FROM ".$db->prefix."un_edulevel l
			INNER JOIN ".$db->prefix."un_eduform f ON f.edulevelid=l.edulevelid
			WHERE l.programid=".bkint($programid)."
		";
		return $db->query_read($sql);
	}
	
	public static function EditProgram(Ab_Database $db, $d){

		
	}
	
	private function AppendEduForm($db, $eduLevel, $programid, $numrow){
		
		foreach ($eduLevel as $key => $value){
				$sql = "
					INSERT INTO ".$db->prefix."un_edulevel(programid, level)
					VALUES (
							".$programid.",
							".($key + 1)."
					)
				";
				$db->query_write($sql);
				$edulevelid = mysql_insert_id();
				
				$sql = "
					SELECT 
		    			attributeid as id
		    		FROM ".$db->prefix."un_attribute
		    		WHERE tablename='edulevel'
				";
		    	$rows = $db->query_read($sql);
		    	$insert = "";
		    	
		    	while ($dd = $db->fetch_array($rows)){
		    		$insert .= "(".$dd['id'].",".$numrow.",".$edulevelid.",".'"edulevelid"'."),"; 
		    	}
		    	
		    	$insert = substr($insert, 0, -1);
		    	
		    	$sql = "
					INSERT INTO ".$db->prefix."un_value(attributeid, numrow, relationid, value)
					VALUES ".$insert."
				";
		    	$db->query_write($sql);
				
		    	/*
		    	 * och zaoch проверять fieldname у атрибута
		    	 * 
		    	 * заносить значние если не 0
		    	 * 
		    	 * */
		    	$sql = "
					INSERT INTO ".$db->prefix."un_eduform(edulevelid, och, ochzaoch, zaoch)
					VALUES (".$edulevelid.", ".bkint($value[0]).", ".bkint($value[1]).", ".bkint($value[2]).")
				";
		    	$db->query_write($sql);
		    	$formid = mysql_insert_id();
		    	
		    	$sql = "
					SELECT
		    			attributeid as id
		    		FROM ".$db->prefix."un_attribute
		    		WHERE tablename='eduform'  AND fieldname<>''
				";
		    	$rows = $db->query_read($sql);
		    	$insert = "";
		    	 
		    	while ($dd = $db->fetch_array($rows)){
		    		$insert .= "(".$dd['id'].",".$numrow.",".$formid.",".'"eduformid"'."),";
		    	}
		    	 
		    	$insert = substr($insert, 0, -1);
		    	 
		    	$sql = "
					INSERT INTO ".$db->prefix."un_value(attributeid, numrow, relationid, value)
					VALUES ".$insert."
				";
		    	$db->query_write($sql);
		}
		
				$sql = "
					SELECT
		    			attributeid as id
		    		FROM ".$db->prefix."un_attribute
		    		WHERE tablename='eduform' AND fieldname=''
				";
				$rows = $db->query_read($sql);
				$insert = "";
				
				$form = array(
						'очная',
						'очно-заочная',
						'заочная'
				);
				
				while ($dd = $db->fetch_array($rows)){
					foreach ($form as $value){
						$insert .= "(".$dd['id'].",".$numrow.", 0,'".$value."'),";
					}
				}
				$insert = substr($insert, 0, -1);
				
				$sql = "
					INSERT INTO ".$db->prefix."un_value(attributeid, numrow, relationid, value)
					VALUES ".$insert."
				";
				$db->query_write($sql);
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
	
	public static function AppendEmployees(Ab_Database $db, $fio){
		$sql = "
			INSERT INTO ".$db->prefix."un_employees(FIO)
			VALUES (
					'".bkstr($fio)."'
			)
		";
		return $db->query_write($sql);
	}
	
	public static function EditEmployees(Ab_Database $db, $d){
		$sql = "
			UPDATE ".$db->prefix."un_employees
			SET
				FIO='".bkstr($d->fio)."'
			WHERE employeesid=".bkint($d->employeesid)."
			LIMIT 1
		";
		return $db->query_write($sql);
	}
	
	public static function RemoveEmployees(Ab_Database $db, $d){
		$sql = "
			UPDATE ".$db->prefix."un_employees
			SET
				remove=".bkint($d->remove)."
			WHERE employeesid=".bkint($d->employeesid)."
			LIMIT 1
		";
		return $db->query_write($sql);
	}
	
	public static function ComplexAttrList(Ab_Database $db, $attrid){
		$sql = "
				SELECT
					attributeid as id
				FROM ".$db->prefix."un_attribute
				WHERE complexid=".bkint($attrid)." AND compositeid=0
				UNION ALL
					SELECT
						attributeid as id
					FROM ".$db->prefix."un_attribute
					WHERE complexid=".bkint($attrid)." AND typeattribute=4
		";
		$rows = $db->query_read($sql);
		
		$arrAttrid = array();
		$strid = "";
		
		while ($d = $db->fetch_array($rows)){
			$arrAttrid[$d['id']] = array();
			
			$strid .= $d['id']. ",";
		}
		$maxNumRow = UniversityQuery::MaxNumRowValue($db, $strid);
		
		if(isset($maxNumRow['max'])){
			$dataValue = array();
				for($i = 1; $i <= $maxNumRow['max']; $i++){
					$dataValue[$i] = $arrAttrid;
				}
			return $dataValue;
		}
	}
	
	public static function MaxNumRowValue($db, $strid){
		$strid = substr($strid, 0, -1);
		
		$sql = "
				SELECT
						MAX(numrow) as max
				FROM ".$db->prefix."un_value
				WHERE attributeid IN (".$strid.") 
						AND remove=0
		";
		$result = $db->query_first($sql); 
		
		return $result;
	}
	
	public static function ComplexValueAttributeList(Ab_Database $db, $attrid){
		$sql = "
			SELECT
					v.valueid as id,
					a.attributeid,
					a.tablename,
					a.fieldname,
					v.relationid,
					v.view,
					v.value,
					v.nameurl,
					v.remove,
					v.numrow
			FROM ".$db->prefix."un_attribute a
			INNER JOIN ".$db->prefix."un_value v ON a.attributeid=v.attributeid
			WHERE a.complexid=".bkint($attrid)." AND v.remove=0
					
		";
		return $db->query_read($sql);
	}
	
	public static function ValueOfLinkTable(Ab_Database $db, $tableName, $fieldName, $relationId, $idFieldName){
		$arrFieldName = explode(",", $fieldName);
		$fields = "";
		
		foreach ($arrFieldName as $field){
			$fields .= $field.",";
		}
		
		$fields = substr($fields, 0, -1);
		$tableName = "un_".$tableName;
		
		$sql = "
			SELECT $fields				
			FROM ".$db->prefix."$tableName
			WHERE ".$idFieldName."=".$relationId."
			LIMIT 1
		";
		$result = $db->query_first($sql);
		$respValue = "";
		foreach($result as $value){
			$respValue .= $value." ";
		}
		return $respValue;
	}
}

?>