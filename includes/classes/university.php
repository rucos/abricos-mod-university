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
				return $this->AttributeListToJSON($d->sectionid);
			case 'actAttribute':
				return $this->ActAttributeToJSON($d->data);
			case 'removeAttribute':
				return $this->RemoveAttributeToJSON($d);
			case 'valueAttributeList':
				return $this->ValueAttributeListToJSON($d->sectionid);
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
    
    public function AttributeListToJSON($sectionid){
    	$res = $this->AttributeList($sectionid);
    	return $this->ResultToJSON('attributeList', $res);
    }
    
    public function AttributeList($sectionid){
    	$sectionid = intval($sectionid);
    	
        $list = $this->models->InstanceClass('AttributeList');
        
    	$rows = UniversityQuery::AttributeList($this->db, $sectionid);
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
    
    public function ValueAttributeListToJSON($sectionid){
    	$res = $this->ValueAttributeList($sectionid);
    	return $this->ResultToJSON('valueAttributeList', $res);
    }
    
    public function ValueAttributeList($sectionid){
    	$sectionid = intval($sectionid);
    
    	UniversityQuery::ValueAttributeList($this->db, $sectionid);
    }
    
}

?>