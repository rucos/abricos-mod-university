<?php
/**
 * @package Abricos
 * @subpackage university
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

$modManager = Abricos::GetModule('university')->GetManager();

if(!$modManager->IsAdminRole()){
	return;
}

$utmf = Abricos::TextParser(true);

$resp =  '200';
$data = new stdClass();
	$data->id = intval($_POST['id']);
	$data->atrid = intval($_POST['atrid']);
	$data->value = "";
	$data->nameurl = $utmf->Parser($_POST['nameurl']);
	$data->namedoc = $utmf->Parser($_POST['namedoc']);
	$data->subject = $utmf->Parser($_POST['subject']);
	$data->folder = $utmf->Parser($_POST['folder']);
	$data->datedoc = $_POST['datedoc'];
	
	print_r();
	if(isset($_FILES['file']['tmp_name'])){
		$modManager->GetUniversity()->ActValueAttribute($data);
	} else {
		$resp =  '100';
	}
	
// if(!isset($dir[2])){
// 	return;
// }

// $idValue = isset($dir[3]) ? $dir[3] : 0;

// if($idValue > 0){
// 	$act = 'Изменить';
// } else {
// 	$act = 'Добавить';
// }

// $uploadfile = 'data.pdf';
// // print_r($_POST['folder']);
// if(!isset($_FILES['file']['tmp_name'])){
// 	$resp =  '100';
// } 
// move_uploaded_file($_FILES['file1']['tmp_name'], $uploadfile);

$brick = Brick::$builder->brick;
$v = &$brick->param->var;

	
$brick->content = Brick::ReplaceVarByData($brick->content, array(
			'respon' => $resp
		));
?>