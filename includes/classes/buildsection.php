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
		
		$rows = $this->ComplexValueList($id);
		
		return $this->ReplaceVar('complex', array(
				"nameattribute" => $nameComplex,
				"th" => $tr,
				"rows" => $rows
		));
	}
	
	private function ComplexValueList($id){
		$insRow = $this->_manager->AttributeItemInsertRow($id);
	
		$valueList = $this->_manager->ValueComplexList($id);
		
		if($insRow == "auto"){
			$tr = $this->AutoComplexValueParse($valueList);
		} else {
			$tr = $this->ManuallyComplexValueParse($valueList);
		}
		return $tr;
	}
	
	private function AutoComplexValueParse($valueList){
		$tr = "";
		foreach ($valueList as $valueItem){
			$cntRowSpan = $this->DetermineRowSpan($valueItem);
			$rowSpan = "rowspan=".$cntRowSpan;
			$span = true;
			
				for ($i = 0; $i < $cntRowSpan; $i++){
					$td = "";
					foreach ($valueItem as $values){
						if(isset($values[$i])){
							$p = $this->ParseValue($values[$i]);
							
							$td .= $this->TdReplace($rowSpan, $p);
							
							if($span){
								$rowSpan = "";
								$span = false;
							}
						}
					}
					$tr .= $this->TrReplace($td);
				}
		}
		return $tr;
	}
	
	private function DetermineRowSpan($valueItem){
		$cnt = 0;
		foreach ($valueItem as $values){
			if($cnt === 1){
				return count($values);
			} else {
				$cnt++;
			}
		}
	}
	
	private function ManuallyComplexValueParse($valueList){
		$tr = "";
		foreach ($valueList as $valueItem){
			$td = "";
			foreach ($valueItem as $values){
				$p = "";
					foreach ($values as $val){
						$p .= $this->ParseValue($val);
					}
				$td .= $this->TdReplace("", $p);
			}
				$tr .= $this->TrReplace($td);
		}
		return $tr;
	}
	
	private function TrReplace($td){
		$replaceArray = array(
				"td" => $td,
		);
		return $this->ReplaceVar('tr', $replaceArray);
	}
	
	private function TdReplace($span, $value){
		$replaceArray = array(
			"span" => "",
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