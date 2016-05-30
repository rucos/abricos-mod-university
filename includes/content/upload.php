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
	$data->nameurl = $utmf->Parser($_POST['nameurl']);
	$data->namedoc = $utmf->Parser($_POST['namedoc']);
	$data->subject = $utmf->Parser($_POST['subject']);
	$data->folder = $utmf->Parser($_POST['folder']);
	$data->datedoc = $utmf->Parser($_POST['datedoc']);
	
	$file = $_FILES['file']['tmp_name'];
	
	if(isset($file)){
		$error = $_FILES['file']['error'];
		$name = $_FILES['file']['name'];
		
		if($error > 0){
			$resp = $error;
		} else {
			$typeDoc = '';
			$whitelist = array(".pdf", ".doc", ".docx", ".xls", ".xlsx");
			
			foreach($whitelist as $item){
				if(preg_match("/$item\$/i", $name)) {
					$typeDoc = $item;
						break;
				} 
			}
			
			if($typeDoc !== ''){
				$menu = $modManager->GetUniversity()->SectionItemUpload($data->atrid);
					
				$datedoc = explode('-', $data->datedoc);
				$dateDocStr = $datedoc[2].".".$datedoc[1].".".$datedoc[0];
					
				$uploadfile = "data-edu/".$menu."/".$data->namedoc."_".$dateDocStr.$typeDoc;
					
				move_uploaded_file($file, $uploadfile);
				
				$data->value = $typeDoc;
				$modManager->GetUniversity()->ActValueAttribute($data);
			} else {
				$resp = '9';
			}
		}
	} else {
		$resp =  '10';
	}
	
$brick = Brick::$builder->brick;
$v = &$brick->param->var;

$brick->content = Brick::ReplaceVarByData($brick->content, array(
			'respon' => $resp
		));
?>