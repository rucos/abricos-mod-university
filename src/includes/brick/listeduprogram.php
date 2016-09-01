<?php
/**
 * @package Abricos
 * @subpackage University
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

/** @var UniversityModule $mod */
$mod = Abricos::GetModule('university');
$univer = $mod->GetManager()->GetUniversity();
$brick = Brick::$builder->brick;

$programList = $univer->ProgramList(true);
$arrlevelList = $univer->BrickLevelList();

$rows = "";

  while ($d = $univer->db->fetch_array($programList)){
  	$tr = "";
  	$trCapital = "";
  	$tdCapital = "";
  	$span = 0;
  	$isCap = true;
  	
	  	foreach ($arrlevelList as $lvlKey => $level){
	  		if($level['programid'] === $d['id']){
	  			$td = "";
	  			$span++;
	  			
	  			foreach($level as $formKey => $form){
	  				if($formKey !== 'programid'){
  						$td .= Brick::ReplaceVarByData($brick->param->var['td'], array(
  								"span" => "",
  								"value" => $form
  						));
	  				}
	  			}
	  			if($isCap){
	  				$isCap = false;
	  				$tdCapital = $td;
	  			} else {
	  				$tr .= Brick::ReplaceVarByData($brick->param->var['tr'], array(
	  						"td" => $td,
	  				));
	  			}
	  			unset($arrlevelList[$lvlKey]);
	  		}
	  	}
	  	
	  	$tdCapital = Brick::ReplaceVarByData($brick->param->var['td'], array(
  			"span" => $span > 1 ? "rowspan=".$span : "",
  			"value" => $d['code']." ".$d['name']
  		)).$tdCapital;
	  	
  		$trCapital = Brick::ReplaceVarByData($brick->param->var['tr'], array(
  			"td" => $tdCapital,
  		));
  		
  		$rows .= $trCapital.$tr;
  }

$brick->content = Brick::ReplaceVarByData($brick->param->var['wrap'], array(
		"rows" => $rows
));

?>