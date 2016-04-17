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
				'AttributeItem' => 'AttributeItem'
		);
	}
	
	
	protected function GetStructures(){
		return 'SectionItem, AttributeItem';
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
			case 'appendAttribute':
				return $this->AppendAttributeToJSON($d->data);
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
    
    public function AppendAttributeToJSON($d){
    	$res = $this->AppendAttribute($d);
    	return $this->ResultToJSON('appendAttribute', $res);
    }
    
    public function AppendAttribute($d){
    	$utmf = Abricos::TextParser(true);
    	
    	$d->sectionid = intval($d->sectionid);
    	$d->complexid = intval($d->complexid);
    	$d->type = $utmf->Parser($d->type);
    	$d->nameattribute = $utmf->Parser($d->nameattribute);
    	$d->applyattribute = $utmf->Parser($d->applyattribute);
    	$d->locate = intval($d->locate);
    	 
    	$rows = UniversityQuery::AppendAttribute($this->db, $d);
    }
    
}

?>