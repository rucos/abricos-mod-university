<?php
/**
 * @package Abricos
 * @subpackage University
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

$man = Abricos::GetModule('university')->GetManager();
$brick = Brick::$builder->brick;

$university = $man->GetUniversity();
$section = $university->SectionList(true);

$result = "";
while (($d = $university->db->fetch_array($section))){
	$result .= Brick::ReplaceVarByData($brick->param->var['section'], array(
			"name" => $d['name'],
			"title" => $d['title']
	));
}

$brick->content = Brick::ReplaceVarByData($brick->param->var['wrap'], array(
		"result" => $result
));

?>