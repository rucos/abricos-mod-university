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
		$valueList = $this->_manager->ValueSimpleList($id, true);
		$values = "";
	
		while($d = $this->_manager->db->fetch_array($valueList)){
				$values .= $this->ParseValue($d);
		}
	
		return $values;
	}
	
	private function ParseValue($d){
		$v = &$this->_brick->param->var;
		$value = $d['value'];
		
		switch($d['view']){
			case 'file':
				$value = University::NAME_DIR.$d['value'];
			case 'url':
				$value = $this->ParseUrl($value, $d['nameurl']);
					break;
		}
		return Brick::ReplaceVarByData($v['simpleValue'], array(
				"value" => $value
		));
	}
	
	private function ParseUrl($url, $nameUrl){
		return Brick::ReplaceVarByData($this->_brick->param->var['urlValue'], array(
				"url" => $url,
				"nameUrl" => $nameUrl
		));
	}

	private function ComplexAttributeListParse($id, $nameComplex, $compositList, $rowspan){
		$tr = "";
		$td = "";
		$trSub = "";
		$tdSub = "";
		
		foreach ($compositList as $comp){
			$curLen = count($comp[1]);
			
			if($curLen > 0){
				$td .= $this->ReplaceVar('td', array(
						"span" => "colspan=".$curLen,
						"value" => $comp[0]
				));
					foreach($comp[1] as $subComp){
						$tdSub .= $this->ReplaceVar('td', array(
								"span" => "",
								"value" => $subComp
						));
					}
			} else {
				$td .= $this->ReplaceVar('td', array(
						"span" => $rowspan,
						"value" => $comp[0]
				));
			}
		}
		$trSub .= $this->ReplaceVar('tr', array(
				"td" => $tdSub
		));
		
		$tr = $this->ReplaceVar('tr', array(
				"td" => $td
		));
		
		return $this->ReplaceVar('complex', array(
				"nameattribute" => $nameComplex,
				"th" => $tr.$trSub,
				"rows" => ""
		));
	}
	
	private function ReplaceVar($bkvar, $replaceArray){
		$v = &$this->_brick->param->var;
		return Brick::ReplaceVarByData($v[$bkvar], $replaceArray);
	}
	
	public function Build(){
		$listAttribute = $this->_manager->BrickSectionListAttribute($this->_nameSection);
		$result = "";

		foreach ($listAttribute as $id => $value){
			if(is_object($value)){
				$result .= $this->SimpleAttributeListParse($id, $value->name, $value->apply);
			} else {
				$result .= $this->ComplexAttributeListParse($id, $value[0], $value[1], $value[2]);
			}
		}
    	
		return Brick::ReplaceVarByData($this->_brick->param->var['wrap'], array(
				"result" => $result
		));
	}
	
}
?>