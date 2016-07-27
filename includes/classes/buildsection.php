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
	
	/**
	 * Флаг добавления к странице modal
	 * 
	 * @var bool
	 */
	private $_modal = false;
	
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
			$d['value'] = $this->_manager->ViewIsFile($d['view'], $d['value']);
				$curVal = $this->ParseValue($d);
				$values .= $this->SimpleReplaceValue($curVal);
		}
	
		return $values;
	}
	
	private function ComplexAttributeListParse($id, $nameComplex, $compositList, $rowspan, $applyattribute){
		$tr = "";
		$td = "";
		$tdSub = "";
		
		foreach ($compositList as $comp){
			$curLen = count($comp[1]);
			
			if($curLen > 0){
				$td .= $this->TdReplace("colspan=".$curLen, $comp[0], true);
				
					foreach($comp[1] as $subComp){
						$tdSub .= $this->TdReplace("", $subComp, true);
					}
			} else {
				$td .= $this->TdReplace($rowspan, $comp[0], true);
			}
		}
		
		$tr = $this->TrReplace($td);
		$tr .= $this->TrReplace($tdSub);
		
		$rows = $this->ComplexValueList($id);
		
		if($applyattribute){
			$itemScope = strripos($applyattribute, "http");
			if($itemScope !== false){
				$applyattribute = 'itemscope itemtype='.$applyattribute;
			} else {
				$applyattribute = 'itemprop='.$applyattribute;
			}
		}
		
		return $this->ReplaceVar('complex', array(
				"nameattribute" => $nameComplex,
				"th" => $tr,
				"applyattribute" => $applyattribute,
				"rows" => $rows
		));
	}
	
	private function ComplexValueList($id){
		$insRow = $this->_manager->AttributeItemInsertRow($id);
	
		$valueList = $this->_manager->ValueComplexList($id, true);
		$tr = "";
		
		if($valueList){
			if($insRow == "auto"){
				$tr = $this->AutoComplexValueParse($valueList);
			} else {
				$tr = $this->ManuallyComplexValueParse($valueList, $id);
			}
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
							$curVal = $this->ParseValue($values[$i]);
							
							$p = $this->ComplexReplaceValue($curVal, $values[$i]['applyattribute']);
							
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
			$append = false;
				foreach ($valueItem as $values){
					$p = "";
					$hidelist = false;
						foreach ($values as $val){
							$append = true;
							$apply = $val['applyattribute'];
							$curVal = $this->ParseValue($val, $hidelist);
							
							if($val['display'] == 'hideList'){
								$hidelist = true;
								$p .= $this->SimpleReplaceValue($curVal);
							} else {
								$p .= $this->ComplexReplaceValue($curVal, $apply);
							}
						}
						if($hidelist){
							$p = $this->FilelistReplace($p, $apply);
						} 

						$td .= $this->TdReplace("", $p);
				}
				if($append){
					$tr .= $this->TrReplace($td);
				}
		}
		return $tr;
	}
	
	private function TrReplace($td){
		$replaceArray = array(
				"td" => $td
		);
		return $this->ReplaceVar('tr', $replaceArray);
	}
	
	private function TdReplace($span, $value, $isHead = false){
		$replaceArray = array(
			"span" => $span,
			"value" => $value,
			"tag" => $isHead ? 'th' : 'td'
		);
		return $this->ReplaceVar('td', $replaceArray);
	}
	
	private function FilelistReplace($list, $apply){
		$this->_modal = true;
		
		$replaceArray = array(
				"list" => $list,
				"applyattribute" => $apply
		);
		return $this->ReplaceVar('filelist', $replaceArray);
	}
	
	/**
	 * Парсинг значений
	 * 
	 * $hidelist=true - тип значения display='hideList'
	 *
	 */
	private function ParseValue($d, $hidelist = false){
		$value = $d['value'];
		switch($d['view']){
			case 'file':
				$value = "/".$value;
			case 'url':
				$value = $this->ParseUrl($value, $d['nameurl']);
					break;
		}
		
		if($hidelist){
			return $this->ReplaceVar('fileitem', array(
				"value" => $value
			));
		} else {
			return $value;
		}
	}
	
	private function SimpleReplaceValue($value){
		return $this->ReplaceVar('simpleValue', array(
			"value" => $value
		)); 
	}
	
	private function ComplexReplaceValue($value, $applyattribute = false){
		return $this->ReplaceVar('complexValue', array(
				"applyattribute" => $applyattribute ? "itemprop=".$applyattribute : "",
				"value" => $value != "0" ? $value : ""
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
				$result .= $this->ComplexAttributeListParse($id, $value[0], $value[1], $value[2], $value[3]);
			}
		}
		$modal = "";
		
		if($this->_modal){
			$modal = $this->ReplaceVar('modal', array());
		}
			
		return Brick::ReplaceVarByData($this->_brick->param->var['wrap'], array(
				"result" => $result,
				"modal" => $modal
		));
	}
	
}
?>