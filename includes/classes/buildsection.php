<?php
/**
 * @package Abricos
 * @subpackage University
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */
class BuildSection {
	private $_nameSection;
	private $_brick;
	private $_manager;
	
	public function __construct(Ab_CoreBrick $brick, $nameSection){
		$this->_nameSection = $nameSection;
		$this->_brick = $brick;
		$this->_manager = UniversityManager::$instance->GetUniversity();
	}
	
	private function SimpleAttributeListParse($id, $nameattribute, $applyattribute){
		$v = &$this->_brick->param->var;
		$values = $this->SimpleValueParse($id);
		
		return Brick::ReplaceVarByData($this->_brick->param->var['simple'], array(
				"nameattribute" => $nameattribute,
				"applyattribute" => $applyattribute,
				"values" => $values
		));
	}
	
	private function SimpleValueParse($id){
		$v = &$this->_brick->param->var;
		$valueList = $this->_manager->ValueSimpleList($id, true);
		$values = "";
	
		while($d = $this->_manager->db->fetch_array($valueList)){
				$values .= $this->ParseValue($d);
		}
	
		return $values;
	}
	
	private function ParseValue($d){
		$value = $d['value'];
		
		switch($d['view']){
			case 'file':
				$value = University::NAME_DIR.$d['value'];
			case 'url':
				$value = $this->ParseUrl($value, $d['nameurl']);
					break;
		}
		return Brick::ReplaceVarByData($this->_brick->param->var['simpleValue'], array(
				"value" => $value
		));
	}
	
	private function ParseUrl($url, $nameUrl){
		return Brick::ReplaceVarByData($this->_brick->param->var['urlValue'], array(
				"url" => $url,
				"nameUrl" => $nameUrl
		));
	}
	
	public function Build(){
		$listAttribute = $this->_manager->BrickSectionListAttribute($this->_nameSection);
		$result = "";
		
	    while ($d = $this->_manager->db->fetch_array($listAttribute)){
	    	if($d['type'] == "simple"){
	    		$result .= $this->SimpleAttributeListParse($d['id'], $d['name'], $d['apply']);
	    	}
    	}
		
		return Brick::ReplaceVarByData($this->_brick->param->var['wrap'], array(
				"result" => $result
		));
	}
	
}
?>