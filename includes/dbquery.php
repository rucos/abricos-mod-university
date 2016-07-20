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
					insertrow,
					locate
			FROM ".$db->prefix."un_attribute
			WHERE ".$where."
		";
		return $db->query_read($sql);
	}
	
	public static function AppendAttribute(Ab_Database $db, $d){
		$sql = "
			INSERT INTO ".$db->prefix."un_attribute(sectionid, complexid, typeattribute, nameattribute, applyattribute, tablename, locate, insertrow)
			VALUES (
					".bkint($d->sectionid).",
					".bkint($d->complexid).",
					'".bkstr($d->type)."',
					'".bkstr($d->nameattribute)."',	
					'".bkstr($d->applyattribute)."',
					'".bkstr($d->tablename)."',
					".bkint($d->locate).",
							'manually'
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
	
	public static function SimpleValueAttributeList(Ab_Database $db, $attridd, $build){
		$where = "attributeid=".bkint($attridd);
		if($build){
			$where .= " AND remove=0";
		}
		$sql = "
			SELECT
					valueid as id,
					relationid,
					view,
					value,
					nameurl,
					remove
			FROM ".$db->prefix."un_value
			WHERE ".$where."
		";
		
		return $db->query_read($sql);
	}
	
	public static function ValueAttributeItem(Ab_Database $db, $valueid){
		$valueid = bkint($valueid);
		$act = UniversityQuery::VerificationValue($db, $valueid);
		
		if($act){
			$sql = "
				SELECT
						valueid as id,
						relationid,
						view,
						value,
						nameurl,
						remove
				FROM ".$db->prefix."un_value
				WHERE valueid=".$valueid."
				LIMIT 1
			";
			return $db->query_first($sql);
		}
	}
	
	public static function AppendValueAttribute(Ab_Database $db, $d){
		$atrid = bkint($d->atrid);
		
		$insert = UniversityQuery::AttributeItem($db, $atrid, 'insert');
		if($insert['ins'] == "auto"){
			return;
		}
		
		$numrow = bkint($d->numrow);
		
		$atribute = UniversityQuery::AttributeItem($db, $atrid, 'type');
		
		if($atribute['type'] != 'simple'){
			if($numrow == 0){
				$maxnumrow = UniversityQuery::MaxNumRowValue($db, $atrid);
				$numrow = $maxnumrow['max'] + 1;
			}
		}
		
		$sql = "
			INSERT INTO ".$db->prefix."un_value(attributeid, value, nameurl, view, numrow, mainid)
			VALUES (
					".$atrid.",
					'".bkstr($d->value)."',
					'".bkstr($d->nameurl)."',
					'".bkstr($d->view)."',
					".$numrow.",
					".bkint($d->mainid)."
			)
		";
	
		return $db->query_write($sql);
	}
	
	public static function EditValueAttribute(Ab_Database $db, $d){
		$valueid = bkint($d->id);
		
		$act = UniversityQuery::VerificationValue($db, $valueid);
		
		if($act){
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
	}
	/*
	 * Обновление значения атрибута
	 * 
	 * $act - проверка атрибута
	 * 
	 * $update - флаг обновления
	 * 
	 * $item['complexid'] - id сложного атрибута
	 * 
	 * */
	
	public static function RemoveValueAttribute(Ab_Database $db, $d){
		$valueid = bkint($d->valueid);
		
		$act = UniversityQuery::VerificationValue($db, $valueid);
		
		if($act){
			$sql = "
				SELECT
						a.complexid,
						v.view,
						v.value
				FROM ".$db->prefix."un_attribute a
				INNER JOIN ".$db->prefix."un_value v ON v.attributeid=a.attributeid
				WHERE v.valueid=".$valueid."
				LIMIT 1
			";
			$item = $db->query_first($sql);
			
			$set = "remove=".bkint($d->remove);
			$update = true;
			
			if($item['complexid'] > 0){
				$insertrow = UniversityQuery::AttributeItem($db, $item['complexid'], 'insert');
				
				switch($insertrow['ins']){
					case "auto":
						$set = "value=0";
							break;
					case "semiauto":
						$update = false;
							break;
				}
			} 
			
			if($update){
				$sql = "
					UPDATE ".$db->prefix."un_value
					SET
						".$set."
					WHERE valueid=".$valueid."
					LIMIT 1
				";
				$db->query_write($sql);
			} else {
				return $item;
			}
		}
	}
	
	
	/**
	 * 
	 * 1) Определение для сложного атрибута способа добавления строк в таблицу ($option='insert')
	 * 
	 * 2) Определение типа атрибута ($option='type')
	 * 
	 *  
	 * */
	public static function AttributeItem(Ab_Database $db, $attributeid, $option){
		
		switch($option){
			case 'insert':
				$select = "insertrow as ins";
					break;
			case 'type':
				$select = "typeattribute as type";
					break;
		}
		
		$sql = "
				SELECT
						".$select."
				FROM ".$db->prefix."un_attribute
				WHERE attributeid=".bkint($attributeid)."
				LIMIT 1
		";
		$result = $db->query_first($sql);
		return $result;
	}
	/*
	 * Проверка значения
	 * 
	 * если у атрибута есть связь с другой таблицей, то значение доступно только для чтения
	 * 
	 * 
	 * */
	private static function VerificationValue(Ab_Database $db, $valueid){
		
		$sql = "
				SELECT
						a.attributeid
				FROM ".$db->prefix."un_attribute a
				INNER JOIN ".$db->prefix."un_value v ON v.attributeid=a.attributeid
				WHERE v.valueid=".$valueid." AND a.fieldname='' AND v.relationid=0
				LIMIT 1
			";
		return $db->query_first($sql);
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
		    	
		UniversityQuery::InsertComplexValue($db, "tablename='program' AND fieldname<>''", $numrow['m'], $programid, 0, "programid");
		UniversityQuery::InsertComplexValue($db, "tablename='vakant'", $numrow['m'], 0, $programid, 0);
		
		
		$formEdu = array(
				'очная',
				'очно-заочная',
				'заочная'
		);
		
		foreach ($formEdu as $form){
			UniversityQuery::InsertComplexValue($db, "tablename='program' AND fieldname=''", $numrow['m'], $programid, $programid, $form);
			UniversityQuery::InsertComplexValue($db, "tablename='eduform' AND fieldname=''", $numrow['m'], 0, $programid, 0);
		}
		
   		UniversityQuery::AppendEduForm($db, $d->eduLevel, $programid, $numrow['m']);
	}
	
	/*
	 * Добавление авто значений в таблицу un_value 
	 * 
	 */
	public static function InsertComplexValue(Ab_Database $db, $where, $numrow, $relationid, $mainid, $value, $remove = 0){
		
		$sql = "
				SELECT
	    			attributeid as id
	    		FROM ".$db->prefix."un_attribute
	    		WHERE ".$where."
		";
		$rows = $db->query_read($sql);
		$insert = "";
		
		while ($dd = $db->fetch_array($rows)){
			$insert .= "(".$dd['id'].",".$numrow.",".$relationid.",".$mainid.",'".$value."', ".$remove."),";
		}
		$insert = substr($insert, 0, -1);
		 
		$sql = "
			INSERT INTO ".$db->prefix."un_value(attributeid, numrow, relationid, mainid, value, remove)
			VALUES ".$insert."
		";
		$db->query_write($sql);
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
	
	/*
	 * Удаление учебной программы
	 * 
	 * Обновление значений в таблице un_value по текущему programid 
	 * 
	 * */
	
	public static function RemoveProgram(Ab_Database $db, $d){
		$remove = bkint($d->remove);
		$programid = bkint($d->programid);
		
		$sql = "
			UPDATE ".$db->prefix."un_program p, ".$db->prefix."un_value v
			INNER JOIN ".$db->prefix."un_attribute a ON a.attributeid=v.attributeid
			SET
				p.remove=".$remove.",
				v.remove=".$remove."
			WHERE p.programid=".$programid."
					AND ((a.tablename='program' AND a.fieldname IN ('code,name','') AND v.relationid=".$programid.")
							OR (a.tablename='vakant' AND v.mainid=".$programid.")
									OR (a.tablename='eduform' AND a.fieldname='' AND v.mainid=".$programid.")
											OR (a.tablename='educode' AND v.mainid=".$programid."))
		";
		$db->query_write($sql);
		

		$sql = "
			UPDATE ".$db->prefix."un_value v
			INNER JOIN ".$db->prefix."un_attribute a ON a.attributeid=v.attributeid
			INNER JOIN ".$db->prefix."un_edulevel l
			SET
				v.remove=".$remove."
			WHERE l.programid=".$programid." AND l.remove=0
					AND ((a.tablename='edulevel' AND a.fieldname<>'' AND v.relationid=l.edulevelid)
							OR (a.tablename='edulevel' AND a.fieldname='' AND v.mainid=l.edulevelid)
								OR (a.tablename='eduform' AND a.fieldname<>'' AND v.mainid=l.edulevelid))
		";
		$db->query_write($sql);
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
	
	/*
	 * Редактирование учебной программы
	 *
	 * Обновление значений в таблице un_value по текущему programid, edulevelid
	 *
	 * */
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
		
		foreach ($d->eduLevel as $key => $value){
			$sql = "
				SELECT
						edulevelid as id
				FROM ".$db->prefix."un_edulevel
				WHERE programid=".bkint($d->programid)." AND level=".bkint($key + 1)."
			";
				$lvl = $db->query_first($sql);
				
			$remove = intval($value) ? 0 : 1;
				$sql = "
						UPDATE ".$db->prefix."un_edulevel l, ".$db->prefix."un_value v
						INNER JOIN ".$db->prefix."un_attribute a ON a.attributeid=v.attributeid
						SET
							l.remove=".bkint($remove).",
							v.remove=".bkint($remove)."
						WHERE l.edulevelid = ".$lvl['id']." 
								AND ((a.tablename='edulevel' AND a.fieldname<>'' AND v.relationid=".$lvl['id'].")
										OR (a.tablename='edulevel' AND a.fieldname='' AND v.mainid=".$lvl['id'].")
											OR (a.tablename='eduform' AND a.fieldname<>'' AND v.mainid=".$lvl['id']."))
					";
				$db->query_write($sql);
				
				$sql = "
						UPDATE ".$db->prefix."un_eduform
							SET
								och=".bkint($value[0]).",
								ochzaoch=".bkint($value[1]).",
								zaoch=".bkint($value[2])."
						WHERE edulevelid = ".bkint($lvl['id'])."
					";
				$db->query_write($sql);
		}
	}
	
	/*
	 * Добавление форм обучения
	 *
	 * Обновление значений в таблице un_value по текущему programid, edulevelid
	 *
	 * */
	private function AppendEduForm($db, $eduLevel, $programid, $numrow){
		foreach ($eduLevel as $key => $value){
			$remove = intval($value) ? 0 : 1;
			
				$sql = "
					INSERT INTO ".$db->prefix."un_edulevel(programid, level, remove)
					VALUES (
							".$programid.",
							".bkint($key + 1).",
							 ".$remove."
					)
				";
				$db->query_write($sql);
				$edulevelid = mysql_insert_id();
				
				UniversityQuery::InsertComplexValue($db, "tablename='edulevel' AND fieldname<>''", $numrow, $edulevelid, $programid, "edulevelid", $remove);
				
				UniversityQuery::InsertComplexValue($db, "tablename='edulevel' AND fieldname=''", $numrow, 0, $edulevelid, 0, $remove);
				
		    	$sql = "
					INSERT INTO ".$db->prefix."un_eduform(edulevelid, och, ochzaoch, zaoch)
					VALUES (".$edulevelid.",".bkint($value[0]).",".bkint($value[1]).",".bkint($value[2]).")
				";
		    	$db->query_write($sql);
		    	$formid = mysql_insert_id();
		    	
		    	UniversityQuery::InsertComplexValue($db, "tablename='eduform'  AND fieldname<>''", $numrow, $formid, $edulevelid, "eduformid", $remove);
		}
	}
	
	public static function EmployeesList(Ab_Database $db){
		$sql = "
			SELECT
					employeesid as id,
					fio,
					post,
					telephone,
					email,
					remove
			FROM ".$db->prefix."un_employees
		";
		return $db->query_read($sql);
	}
	
	public static function AppendEmployees(Ab_Database $db, $d){
		$sql = "
			INSERT INTO ".$db->prefix."un_employees(fio, post, telephone, email)
			VALUES (
					'".bkstr($d->fio)."',
					'".bkstr($d->post)."',
					'".bkstr($d->telephone)."',
					'".bkstr($d->email)."'
			)
		";
		$db->query_write($sql);
		$emploeesid = mysql_insert_id();
		 
		$sql = "
			SELECT
    			MAX(employeesid) as m
    		FROM ".$db->prefix."un_employees
    	";
		$numrow = $db->query_first($sql);
		
		UniversityQuery::InsertComplexValue($db, "tablename='employees' AND fieldname<>'' AND insertrow=1", $numrow['m'], $emploeesid, 0, "employeesid");
	}
	
	public static function EditEmployees(Ab_Database $db, $d){
		$sql = "
			UPDATE ".$db->prefix."un_employees
			SET
				fio='".bkstr($d->fio)."',
				post='".bkstr($d->post)."',
				telephone='".bkstr($d->telephone)."',
				email='".bkstr($d->email)."'
			WHERE employeesid=".bkint($d->employeesid)."
			LIMIT 1
		";
		return $db->query_write($sql);
	}
	
	public static function RemoveEmployees(Ab_Database $db, $d){
		$employeesid = bkint($d->employeesid);
		$remove = bkint($d->remove);
		
		$sql = "
			UPDATE ".$db->prefix."un_employees e, ".$db->prefix."un_value v
			INNER JOIN ".$db->prefix."un_attribute a ON a.attributeid=v.attributeid
			SET
				e.remove=".$remove.",
				v.remove=".$remove."
			WHERE e.employeesid=".$employeesid." AND (a.tablename='employees' AND a.fieldname<>'' AND a.insertrow=1 AND v.relationid=".$employeesid.")
		";
		return $db->query_write($sql);
	}
	
	public static function ComplexAttrListAll(Ab_Database $db, $attrid){
		$sql = "
				SELECT
					attributeid as id
				FROM ".$db->prefix."un_attribute
				WHERE complexid=".bkint($attrid)." AND (compositeid=0 OR typeattribute=4)
		";
		return $db->query_read($sql);
	}
	
	public static function ComplexAttrItem(Ab_Database $db, $attrid, $tableName, $fieldname){
		
		$where = "complexid=".bkint($attrid)." AND tablename='".bkstr($tableName)."' AND fieldname='".bkstr($fieldname)."'";
		
		$sql = "
				SELECT
					attributeid as id
				FROM ".$db->prefix."un_attribute
				WHERE ".$where."
				LIMIT 1
		";
		$res = $db->query_first($sql);
		return $res['id'];
	}
	
	public static function CompositAttrItem(Ab_Database $db, $attrid, $idlst){
	
		$sql = "
				SELECT
					attributeid as id
				FROM ".$db->prefix."un_attribute
				WHERE complexid=".bkint($attrid)." AND attributeid NOT IN (".$idlst.") AND tablename <> ''
		";
		$result = $db->query_read($sql);
		
		$respond = "";
	    while ($d = $this->db->fetch_array($result)){
	    	$respond .= $d['id'].",";
    	}
    	$respond = substr($respond, 0, -1);
    	
		return $respond;
	}
	
	public static function MaxNumRowValue($db, $strid, $isComplexAll = false){
		$where = "attributeid IN (".$strid.")";
		
		if($isComplexAll){
			$where .= " AND remove=0";
		}
		
		$sql = "
				SELECT
						MAX(numrow) as max
				FROM ".$db->prefix."un_value
				WHERE ".$where."
		";
		$result = $db->query_first($sql); 
		
		return $result;
	}
	
	public static function ComplexValueAttributeList(Ab_Database $db, $attrList){
		$where = "v.attributeid IN (".$attrList.") AND v.remove=0";
		
		$sql = "
			SELECT
					v.valueid as id,
					a.attributeid,
					a.tablename,
					a.fieldname,
					a.display,
					a.insertRow,
					a.applyattribute,
					v.relationid,
					v.mainid,
					v.view,
					v.value,
					v.nameurl,
					v.remove,
					v.numrow
			FROM ".$db->prefix."un_attribute a
			INNER JOIN ".$db->prefix."un_value v ON a.attributeid=v.attributeid
			WHERE ".$where."
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
			WHERE ".$idFieldName."=".bkint($relationId)."
			LIMIT 1
		";
		$result = $db->query_first($sql);
		
		$respValue = "";
			if($result){
				foreach($result as $value){
					$respValue .= $value." ";
				}
			}
		return $respValue;
	}
	
	public static function AppendSelectValue(Ab_Database $db, $d){
		$attrid = bkint($d->attrid);
		$numrow = bkint($d->numrow);
		
		if($numrow == 0){
			$maxnumrow = UniversityQuery::MaxNumRowValue($db, $attrid);
			$numrow = $maxnumrow['max'] + 1;
		}
		
		$sql = "
			INSERT INTO ".$db->prefix."un_value(attributeid, numrow, relationid, view, value)
			VALUES (
					".$attrid.",
					".$numrow.",
					".bkint($d->relationid).",
						1,
					'employeesid'
			)
		";
		return $db->query_write($sql);
	}
	
	public static function EditSelectValue(Ab_Database $db, $d){
		$sql = "
			UPDATE ".$db->prefix."un_value
			SET
				relationid=".bkint($d->relationid)."
			WHERE valueid=".bkint($d->valueid)."
			LIMIT 1
		";
		return $db->query_write($sql);
	}
	
	public static function RemoveSelectValue(Ab_Database $db, $valueid){
		$sql = "
			DELETE
			FROM ".$db->prefix."un_value
			WHERE valueid=".bkint($valueid)."
			LIMIT 1
		";
		return $db->query_write($sql);
	}
	
	public static function SectionListAttribute(Ab_Database $db, $name){
		$sql = "
			SELECT
					a.attributeid as id,
					a.nameattribute as name,
					a.complexid,
					a.compositeid,  
					a.applyattribute as apply,
					a.typeattribute as type
			FROM ".$db->prefix."un_attribute a
			INNER JOIN ".$db->prefix."un_section s
			INNER JOIN ".$db->prefix."sys_menu m ON m.menuid=s.menuid AND m.name='".$name."'
			WHERE a.sectionid=s.sectionid
		";
		return $db->query_read($sql);
	}
}

?>