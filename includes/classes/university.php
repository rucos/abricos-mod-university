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
				'SectionList' => 'SectionList'
		);
	}
	
	
	protected function GetStructures(){
		return 'SectionItem';
	}

	public function ResponseToJSON($d){
		if (!$this->manager->IsAdminRole()){
			return 403;
		}
		
		switch ($d->do){
			case 'sectionList': 
				return $this->SectionListToJSON();
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
}

?>