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
				'ValueAttributeItem' => 'ValueAttributeItem'
		);
	}
	
	
	protected function GetStructures(){
		return 'SectionItem, AttributeItem, ValueItem';
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
			case 'actValueAttribute':
				return $this->ActValueAttributeToJSON($d->data);
			case 'removeValueAttribute':
				return $this->RemoveValueAttributeToJSON($d->data);
			case 'actProgram':
				return $this->ActProgramToJSON($d->data);
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
    
    public function AttributeListToJSON($d){
    	$res = $this->AttributeList($d);
    	return $this->ResultToJSON('attributeList', $res);
    }
    
    public function AttributeList($d){
    	$d->sectionid = intval($d->sectionid);
    	$d->isValue = intval($d->isValue);

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
    
    public function ActValueAttributeToJSON($d){
    	$res = $this->ActValueAttribute($d);
    	return $this->ResultToJSON('actValueAttribute', $res);
    }
    
    public function ActValueAttribute($d){
    	$utmf = Abricos::TextParser(true);
    	 
    	$d->id = intval($d->id);
    	$d->datedoc = intval($d->datedoc);
    	$d->folder = $utmf->Parser($d->folder);
    	$d->namedoc = $utmf->Parser($d->namedoc);
    	$d->nameurl = $utmf->Parser($d->nameurl);
    	$d->subject = $utmf->Parser($d->subject);
    	$d->value = $utmf->Parser($d->value);
    	$d->atrid = intval($d->atrid);
    	
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
    	
    	foreach($d->eduLevel as $key => $value){
    		if($value !== ''){
    			$d->eduLevel[$key] = explode(',', $value);
    		} 
    	}
    	
    	if($d->programid > 0){
    		
    	} else {
    		return UniversityQuery::AppendProgram($this->db, $d);
    	}
    	
    }
    
}

?>