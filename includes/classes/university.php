<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

class University extends AbricosApplication {
	
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
			case 'actAttribute':
				return $this->ActAttributeToJSON($d->data);
			case 'removeAttribute':
				return $this->RemoveAttributeToJSON($d);
			case 'valueAttributeList':
				return $this->ValueAttributeListToJSON($d->data);
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
        }
        return null;
    }
    
    public function SectionListToJSON(){
    	$res = $this->SectionList();
    		return $this->ResultToJSON('sectionList', $res);
    }
    
    public function SectionList(){
    	
    	$list = $this->models->InstanceClass('SectionList');
    	$rows = UniversityQuery::SectionList($this->db);
    	 
    	while (($d = $this->db->fetch_array($rows))){
    		$list->Add($this->models->InstanceClass('SectionItem', $d));
    	}
    	return $list;
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
    
    public function ValueAttributeListToJSON($d){
    	$res = $this->ValueAttributeList($d);
    	return $this->ResultToJSON('valueAttributeList', $res);
    }
    
    public function ValueAttributeList($d){
    	$d->attrid = intval($d->attrid);
    	
    	switch($d->type){
    		case 'simple': 
    			$list = $this->RenderModelsValue('SimpleValueAttributeList', $d->attrid);
    				break;
    		case 'complex': 
    			$list = 'complex';
    				break;
    		default: return;
    	}
    	
    	return $list;
    }
    
    public function RenderModelsValue($quory, $attridd){
    	
    	$list = $this->models->InstanceClass('ValueAttributeList');
    	
    	$rows = UniversityQuery::$quory($this->db, $attridd);
    	while (($d = $this->db->fetch_array($rows))){
    		$list->Add($this->models->InstanceClass('ValueAttributeItem', $d));
    	}
    	return $list;
    }
    
    public function ValueAttributeItemToJSON($valueid){
    	$res = $this->ValueAttributeItem($valueid);
    	return $this->ResultToJSON('valueAttributeItem', $res);
    }
    
    public function ValueAttributeItem($valueid){
    	$valueid = intval($valueid);
    	
    	$item = UniversityQuery::ValueAttributeItem($this->db, $valueid);
    	
    	return $this->models->InstanceClass('ValueAttributeItem', $item);
    }
    
    public function ActValueAttributeToJSON($d){
    	$utmf = Abricos::TextParser(true);
    	
    	$d->id = intval($d->id);
    	$d->atrid = intval($d->atrid);
    	$d->value = $utmf->Parser($d->value);
    	$d->nameurl = "";
    	
    	$res = $this->ActValueAttribute($d);
    	return $this->ResultToJSON('actValueAttribute', $res);
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
    	 
    	return UniversityQuery::RemoveValueAttribute($this->db, $d);
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
    	
    	if($d->programid > 0){
    		return UniversityQuery::EditProgram($this->db, $d);
    	} else {
    		return UniversityQuery::AppendProgram($this->db, $d);
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
    	
    	while (($d = $this->db->fetch_array($rows))){
    		$list->Add($this->models->InstanceClass('ProgramLevelItem', $d));
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
    		
    		if($d->employeesid > 0){
    			$rows = UniversityQuery::EditEmployees($this->db, $d);
    		} else {
    			$rows = UniversityQuery::AppendEmployees($this->db, $d->fio);
    		}
    	} else {
    		$d->remove = intval($d->remove);
    			$rows = UniversityQuery::RemoveEmployees($this->db, $d);
    	}
    	return $rows;
    }
    
    public function SectionItemUpload($attrid){
    	$menu = UniversityQuery::SectionItemUpload($this->db, $attrid); 
    	return $menu['name'];
    }
    
    
}

?>