<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

class University extends AbricosApplication {
	const NAME_DIR = "data-edu/";
	
	protected function GetClasses(){
		return array(
				'SectionItem' => 'SectionItem',
				'SectionList' => 'SectionList',
				'AttributeList' => 'AttributeList',
				'AttributeItem' => 'AttributeItem',
				'ValueAttributeList' => 'ValueAttributeList',
				'ValueAttributeItem' => 'ValueAttributeItem',
				'ProgramItem' => 'ProgramItem',
				'ProgramList' => 'ProgramList',
				'ProgramLevelList' => 'ProgramLevelList',
				'ProgramLevelItem' => 'ProgramLevelItem',
				'EmployeesList' => 'EmployeesList',
				'EmployeesItem' => 'EmployeesItem'
		);
	}
	
	
	protected function GetStructures(){
		return 'SectionItem, AttributeItem, ValueItem, ProgramItem, ProgramLevelItem, EmployeesItem';
	}

	public function ResponseToJSON($d){
		if (!$this->manager->IsAdminRole()){
			return 403;
		}
		switch ($d->do){
			case 'sectionList': 
				return $this->SectionListToJSON();
			case 'attributeList':
				return $this->AttributeListToJSON($d->data);
			case 'attributeItemInsertRow':
				return $this->AttributeItemInsertRowToJSON($d->attrid);
			case 'actAttribute':
				return $this->ActAttributeToJSON($d->data);
			case 'removeAttribute':
				return $this->RemoveAttributeToJSON($d);
			case 'valueAttributeItem':
				return $this->ValueAttributeItemToJSON($d->valueid);
			case 'actValueAttribute':
				return $this->ActValueAttributeToJSON($d->data);
			case 'removeValueAttribute':
				return $this->RemoveValueAttributeToJSON($d->data);
			case 'actProgram':
				return $this->ActProgramToJSON($d->data);
			case 'programList':
				return $this->ProgramListToJSON();
			case 'removeProgram':
				return $this->RemoveProgramToJSON($d->data);
			case 'programItem':
				return $this->ProgramItemToJSON($d->programid);
			case 'employeesList':
				return $this->EmployeesListToJSON();
			case 'actEmployees':
				return $this->ActEmployeesToJSON($d->data);
			case 'valueComplexList':
				return $this->ValueComplexListToJSON($d->attrid);
			case 'valueSimpleList':
				return $this->ValueSimpleListToJSON($d->attrid);
			case 'selectValueAct':
				return $this->SelectValueActToJSON($d->data);
        }
        return null;
    }
    
    public function SectionListToJSON(){
    	$res = $this->SectionList();
    		return $this->ResultToJSON('sectionList', $res);
    }
    
    public function SectionList($isBrick = false){
    	$rows = UniversityQuery::SectionList($this->db);
    	
    	if($isBrick){
    		return $rows;
    	} else {
    		$list = $this->models->InstanceClass('SectionList');
	    		while (($d = $this->db->fetch_array($rows))){
	    			$list->Add($this->models->InstanceClass('SectionItem', $d));
	    		}
	    		return $list;
    	}
    }
    
    public function SectionItem($sectionid){
    	$d = UniversityQuery::SectionItem($this->db, $sectionid);
    	$res = $this->models->InstanceClass('SectionItem', $d);
    	 
    	return $res;
    }
    
    public function AttributeListToJSON($d){
    	$d->sectionid = intval($d->sectionid);
    	
    	$res = $this->AttributeList($d);
    	$section = $this->SectionItem($d->sectionid);
    	
    	return $this->ImplodeJSON(
    			$this->ResultToJSON('attributeList', $res),
    			$this->ResultToJSON('sectionItem', $section)
    	);
    }
    
    public function AttributeList($d){
    	$d->isValue = intval($d->isValue);
    	$d->complexid = intval($d->complexid);

    	$list = $this->models->InstanceClass('AttributeList');
    	
    	$rows = UniversityQuery::AttributeList($this->db, $d);
	    	while (($d = $this->db->fetch_array($rows))){
	    		$list->Add($this->models->InstanceClass('AttributeItem', $d));
	    	}
	    	return $list;
    }
    
    public function AttributeItemInsertRowToJSON($attrid){
    	$res = $this->AttributeItemInsertRow($attrid);
    	 
    	return 	$this->ResultToJSON('attributeItemInsertRow', $res);
    }
    
    public function AttributeItemInsertRow($attrid){
    	$attrid = intval($attrid);
    	
    	$result = UniversityQuery::AttributeItem($this->db, $attrid, 'insert');
    	return $result['ins'];
    }
    
    public function ActAttributeToJSON($d){
    	$res = $this->ActAttribute($d);
    	return $this->ResultToJSON('actAttribute', $res);
    }
    
    public function ActAttribute($d){
    	$utmf = Abricos::TextParser(true);
    	
    	$d->sectionid = intval($d->sectionid);
    	$d->compositid = intval($d->compositid);
    	$d->complexid = intval($d->complexid);
    	$d->type = $utmf->Parser($d->type);
    	$d->nameattribute = $utmf->Parser($d->nameattribute);
    	$d->applyattribute = $utmf->Parser($d->applyattribute);
    	$d->tablename = $utmf->Parser($d->tablename);
    	$d->locate = intval($d->locate);
    	
    	if($d->compositid > 0){
    		$rows = UniversityQuery::EditAttribute($this->db, $d);
    	} else {
    		$rows = UniversityQuery::AppendAttribute($this->db, $d);
    	}
    }
    
    public function RemoveAttributeToJSON($d){
    	$res = $this->RemoveAttribute($d);
    	return $this->ResultToJSON('removeAttribute', $res);
    }
    
    public function RemoveAttribute($d){
    	$d->compositid = intval($d->compositid);
    	$d->isComplex = intval($d->isComplex);
    	
    	return UniversityQuery::RemoveAttribute($this->db, $d);
    }
    
    public function ValueSimpleListToJSON($attrid){
    	$res = $this->ValueSimpleList($attrid);
    	return $this->ResultToJSON('valueSimpleList', $res);
    }
    
    public function ValueSimpleList($attrid, $build = false){
    	$attrid = intval($attrid);
    	
		$rows = UniversityQuery::SimpleValueAttributeList($this->db, $attrid, $build);
		
		if($build){
			return $rows;
		} else {
			$list = $this->models->InstanceClass('ValueAttributeList');
			
				while (($d = $this->db->fetch_array($rows))){
					$d['value'] = University::ViewIsFile($d['view'], $d['value']);
				
					$list->Add($this->models->InstanceClass('ValueAttributeItem', $d));
				}
			return $list;
		}
    }
    
    public function ValueComplexListToJSON($attrid){
    	$res = $this->ValueComplexList($attrid);
    	return $this->ResultToJSON('valueComplexList', $res);
    }
    
    public function ValueComplexList($attrid){
    	$attrid = intval($attrid);
    	
    	
    	$attrListid = UniversityQuery::ComplexAttrListAll($this->db, $attrid);
    		 
    	$arrAttrid = array();
    	$strid = "";
    		
    	while ($d = $this->db->fetch_array($attrListid)){
    			$arrAttrid[$d['id']] = array();
    		
    			$strid .= $d['id']. ",";
    	}
    	$strid = substr($strid, 0, -1);
    			
    	$allValue = UniversityQuery::ComplexValueAttributeList($this->db, $strid);
    			
    	if(!$allValue){
    		return false;
    	}
    			
    	$arrayValue = array();
    			 
    	while ($value = $this->db->fetch_array($allValue)){
    			$arrayValue[] = $value;
    	}
    			
    	$maxNumRow = UniversityQuery::MaxNumRowValue($this->db, $strid, true);
    		
    	if(isset($maxNumRow['max'])){
    		$dataValue = array();
    			for($i = 1; $i <= $maxNumRow['max']; $i++){
    					$dataValue[$i] = $arrAttrid;
    			}
    	} else {
    		return false;
    	}
    			
    	foreach ($arrayValue as $val){
    		if($val['remove'] == 1){
    					continue;
    		}
    			
    		$num = $val['numrow'];
    		$atrid = $val['attributeid'];
    		$fieldname = $val['fieldname'];
    		
    		$val['value'] = University::ViewIsFile($val['view'], $val['value']);
    		
    		if($fieldname !== ''){
    				$val['value'] = UniversityQuery::ValueOfLinkTable($this->db, $val['tablename'], $fieldname, $val['relationid'], $val['value']);
    		}
			
			array_push($dataValue[$num][$atrid], $val);
    	}
    			return $dataValue;
    }
    
    public function ValueAttributeItemToJSON($valueid){
    	$res = $this->ValueAttributeItem($valueid);
    	return $this->ResultToJSON('valueAttributeItem', $res);
    }
    
    public function ValueAttributeItem($valueid, $upload = false){
    	$valueid = intval($valueid);
    	
    	$item = UniversityQuery::ValueAttributeItem($this->db, $valueid);
    	
    	$item['value'] = University::ViewIsFile($item['view'], $item['value']);
    	
    	if($upload){
    		return $item;
    	} else {
    		return $this->models->InstanceClass('ValueAttributeItem', $item);
    	}
    }
    
    public function ViewIsFile($view, $value){
    	return $view == "file" ? University::NAME_DIR.$value : $value;
    }
    
    public function ActValueAttributeToJSON($d){
    	$res = $this->CheckValueAttribute($d->value, $d->nameurl, $d->view);
		
    	if($res){
    		$utmf = Abricos::TextParser(true);
    		$d->id = intval($d->id);
    		$d->atrid = intval($d->atrid);
    		$d->value = $utmf->Parser($d->value);
    		$d->nameurl = $utmf->Parser($d->nameurl);
    		$d->view = $utmf->Parser($d->view);
    		$d->namedoc = "";
    		$d->datedoc = "";
    		$d->file = "";
    		$d->numrow = intval($d->numrow);
    		$d->mainid = intval($d->mainid);
    		 
    		$res = $this->ActValueAttribute($d);
    	}
    	
    	return $this->ResultToJSON('actValueAttribute', $res);
    }
    
    private function CheckValueAttribute($value, $nameurl, $view){
    	$pattern = '/[a-zА-Я0-9]/i';
    	
    	$check = preg_match($pattern, $value);
    	
    	if($view == 'url' && $check === 1){
    		$check = preg_match($pattern, $nameurl);
    	} 
    	return $check === 1 ? true : false;
    }
    
    public function ActValueAttribute($d){
		if($d->id > 0){
			return UniversityQuery::EditValueAttribute($this->db, $d);
		} else {
			return UniversityQuery::AppendValueAttribute($this->db, $d); 
		} 	
    }
    
    public function RemoveValueAttributeToJSON($d){
    	$res = $this->RemoveValueAttribute($d);
    	return $this->ResultToJSON('removeValueAttribute', $res);
    }
    
    public function RemoveValueAttribute($d){
    	$d->valueid = intval($d->valueid);
    	$d->remove = intval($d->remove);
    	
    	$respond = UniversityQuery::RemoveValueAttribute($this->db, $d);
    	if(isset($respond)){//если удаляем из 'auto','semiauto'
    		if($respond['view'] == "file"){
    			unlink(realpath(University::NAME_DIR.$respond['value']));//удаляем файл из директории
    		}
    		UniversityQuery::RemoveSelectValue($this->db, $d->valueid);
    	}
    }
    
    public function ActProgramToJSON($d){
    	$res = $this->ActProgram($d);
    	return $this->ResultToJSON('actProgram', $res);
    }
    
    public function ActProgram($d){
    	$utmf = Abricos::TextParser(true);
    	
    	$d->programid = intval($d->programid);
    	$d->code = $utmf->Parser($d->code);
    	$d->name = $utmf->Parser($d->name);
    	
    	$isNotEmpty = false;
    	foreach ($d->eduLevel as $key => $value){
    		$level = intval($value);
    		if($level){
    			$isNotEmpty = true;
    				break;
    		}
    	}
    	
    	if($isNotEmpty){
    			if($d->programid > 0){
    		    	return UniversityQuery::EditProgram($this->db, $d);
    		    } else {
    		    	return UniversityQuery::AppendProgram($this->db, $d);
    		    }
    	} else {
    		return false;
    	}
    }
    
    public function ProgramListToJSON(){
    	$res = $this->ProgramList();
    	return $this->ResultToJSON('programList', $res);
    }
    
    public function ProgramList(){
    	 
    	$list = $this->models->InstanceClass('ProgramList');
    	$rows = UniversityQuery::ProgramList($this->db);
    
    	while (($d = $this->db->fetch_array($rows))){
    		$list->Add($this->models->InstanceClass('ProgramItem', $d));
    	}
    	return $list;
    }
    
    public function RemoveProgramToJSON($d){
    	$res = $this->RemoveProgram($d);
    	return $this->ResultToJSON('removeProgram', $res);
    }
    
    public function RemoveProgram($d){
    	$d->programid = intval($d->programid);
    	$d->remove = intval($d->remove);
    
		return UniversityQuery::RemoveProgram($this->db, $d);
    }
    
    public function ProgramItemToJSON($programid){
    	$programid = intval($programid);
    	
    	$item = $this->ProgramItem($programid);
    	$level = $this->ProgramLevelList($programid);
    	
    	return $this->ImplodeJSON(
    			$this->ResultToJSON('programItem', $item),
    			$this->ResultToJSON('programLevelList', $level)
    	);
    }
    
    public function ProgramItem($programid){
    	
    	$rows = UniversityQuery::ProgramItem($this->db, $programid);
    	
    	return $this->models->InstanceClass('ProgramItem', $rows);
    }
    
    public function ProgramLevelList($programid){
    	$list = $this->models->InstanceClass('ProgramLevelList');
    	
    	$rows = UniversityQuery::ProgramLevelList($this->db, $programid);
    	
    	$resp = array();
    	while ($d = $this->db->fetch_array($rows)){
    		$resp['id'] = $d['id'];
    		$resp['level'] = $d['level'];
    		$resp['eduform'] = $d['och'].$d['ochzaoch'].$d['zaoch'];
    		
    		$list->Add($this->models->InstanceClass('ProgramLevelItem', $resp));
    	}
    	return $list;
    }
    
    public function EmployeesListToJSON(){
    	$res = $this->EmployeesList();
    	return $this->ResultToJSON('employeesList', $res);
    }
    
    public function EmployeesList(){
    	 
    	$list = $this->models->InstanceClass('EmployeesList');
    	$rows = UniversityQuery::EmployeesList($this->db);
    
    	while (($d = $this->db->fetch_array($rows))){
    		$list->Add($this->models->InstanceClass('EmployeesItem', $d));
    	}
    	return $list;
    }
    
    public function ActEmployeesToJSON($d){
    	$res = $this->ActEmployees($d);
    	return $this->ResultToJSON('actEmployees', $res);
    }
    
    public function ActEmployees($d){
    	$utmf = Abricos::TextParser(true);
    	 
    	$d->employeesid = intval($d->employeesid);
    	 
    	if(!isset($d->remove)){
    		$d->fio = $utmf->Parser($d->fio);
    		$d->post = $utmf->Parser($d->post);
    		$d->telephone = $utmf->Parser($d->telephone);
    		$d->email = $utmf->Parser($d->email);
    		
    		if($d->employeesid > 0){
    			$rows = UniversityQuery::EditEmployees($this->db, $d);
    		} else {
    			$rows = UniversityQuery::AppendEmployees($this->db, $d);
    		}
    	} else {
    		$d->remove = intval($d->remove);
    			$rows = UniversityQuery::RemoveEmployees($this->db, $d);
    	}
    	return $rows;
    }

    public function SelectValueActToJSON($d){
    	$res = $this->SelectValueAct($d);
    	return $this->ResultToJSON('selectValueAct', $res);
    }
    
    public function SelectValueAct($d){
    	$d->valueid = intval($d->valueid);
    	
    	if(!isset($d->remove)){
    		$d->attrid = intval($d->attrid);
    		$d->relationid = intval($d->relationid);
    		$d->numrow = intval($d->numrow);
    		
    		if($d->valueid > 0){
    			return UniversityQuery::EditSelectValue($this->db, $d);
    		} else {
    			return UniversityQuery::AppendSelectValue($this->db, $d);
    		}
    	} else {
    		return UniversityQuery::RemoveSelectValue($this->db, $d->valueid);
    	}
    	
    }
    
    public function SectionItemUpload($attrid){
    	$menu = UniversityQuery::SectionItemUpload($this->db, $attrid); 
    	return $menu['name'];
    }
    
    /*
     * Показать все атрибуты текщего раздела
     * 
     * $name - название раздела
     * 
     * $listAttribute - список всех атрибутов для текщуего раздела
     * 
     * $list[0] - если массив -> сложный, объект -> простой
     * 
     * */
    public function BrickSectionListAttribute($name){
    	$listAttribute = UniversityQuery::SectionListAttribute($this->db, $name);
    	$list = array();
    	
    	while ($d = $this->db->fetch_array($listAttribute)){
    		$id = $d['id'];
    		$name = $d['name'];
    		$apply = $d['apply'];
    		$complexid = $d['complexid'];
    		$compositeid = $d['compositeid'];
    	
    		switch($d['type']){
    			case 'simple':
    				$simple = new stdClass();
	    				$simple->name = $name;
	    				$simple->apply = $apply;
	    				
	    			$list[$id] = $simple;
    					break;
    			case 'complex':
    				$list[$id] = array(
	    				$name,
	    				array(),
	    				"",
	    				$apply
    				);
    					break;
    			case 'composite':
    				$list[$complexid][1][$id] = array(
	    				$name,
	    				array()
    				);
    					break;
    			case 'subcomposite':
    				$list[$complexid][1][$compositeid][1][] = $name;
    				$list[$complexid][2] = "rowspan=2";
    					break;
    		}
    	}
    	return $list;
    }
    
}

?>