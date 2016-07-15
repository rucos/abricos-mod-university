<?php
/**
 * @package Abricos
 * @subpackage University
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

/**
 * Class BuildSection
 */
class BuildSection {
	/**
	 * Название раздела
	 *
 	 * @var string
	 */
	private $_nameSection;
	
	/**
	 * @var Ab_CoreBrick
	 */
	private $_brick;
	
	/**
	 * @var UniversityManager
	 */
	private $_manager;
	
	public function __construct(Ab_CoreBrick $brick, $nameSection){
		$this->_nameSection = $nameSection;
		$this->_brick = $brick;
		$this->_manager = UniversityManager::$instance->GetUniversity();
	}
	
	private function SimpleAttributeListParse($id, $nameattribute, $applyattribute){
		$values = $this->SimpleValueParse($id);
		
		return $this->ReplaceVar('simple', array(
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
	
	private function ComplexAttributeListParse($id, $nameComplex, $compositList, $rowspan){
		$tr = "";
		$td = "";
		$tdSub = "";
		
		foreach ($compositList as $comp){
			$curLen = count($comp[1]);
			
			if($curLen > 0){
				$td .= $this->TdReplace("colspan=".$curLen, $comp[0]);
				
					foreach($comp[1] as $subComp){
						$tdSub .= $this->TdReplace("", $subComp);
					}
			} else {
				$td .= $this->TdReplace($rowspan, $comp[0]);
			}
		}
		
		$tr = $this->TrReplace($td);
		$tr .= $this->TrReplace($tdSub);
		
		return $this->ReplaceVar('complex', array(
				"nameattribute" => $nameComplex,
				"th" => $tr,
				"rows" => ""
		));
	}
	
	private function TrReplace($td){
		$replaceArray = array(
				"td" => $td,
		);
		return $this->ReplaceVar('tr', $replaceArray);
	}
	
	private function TdReplace($span, $value){
		$replaceArray = array(
			"span" => $span,
			"value" => $value
		);
		return $this->ReplaceVar('td', $replaceArray);
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
		return $this->ReplaceVar('simpleValue', array(
				"value" => $value
		));
	}
	
	private function ParseUrl($url, $nameUrl){
		return $this->ReplaceVar('urlValue', array(
				"url" => $url,
				"nameUrl" => $nameUrl
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