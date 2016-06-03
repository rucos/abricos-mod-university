<?php
/**
 * @package Abricos
 * @subpackage university
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

class UploadFile{
	
	private $resp = '200';
	private $modManager = null;
	
	private $data = null;
	
	private $namedoc = null;
	private $datedoc = null;
	
	private $file = null;
	
	public function __construct($modManager){
		$this->modManager = $modManager;
		
		$utmf = Abricos::TextParser(true);
		
		$this->data = new stdClass();
			$this->data->id = intval($_POST['id']);
			$this->data->atrid = intval($_POST['atrid']);
			$this->data->nameurl = $utmf->Parser($_POST['nameurl']);
			$this->data->view = $utmf->Parser($_POST['view']);
			
			$this->namedoc = $utmf->Parser($_POST['namedoc']);
			$this->datedoc = explode('-', $utmf->Parser($_POST['datedoc']));

			
			if($this->data->id > 0){
				if(isset($_POST['file'])){
					if($_POST['file'] === 'undefined'){
						$this->resp = "10";
					} 
					if($_POST['file'] === ''){
						$this->RenameFile();
					}
				} else {
					$this->CheckFile(true);
				}
			} else {
				$this->CheckFile();
			}
	}
	
	
	private function RenameFile(){
		$valueItem = $this->ValueItem();
		$value = $valueItem['value'];
		
		preg_match("/.\w{3,4}\$/i", $value, $path);
		
		$uploadFile = $this->ParsePathFile($path[0]);
		
		rename($value, $uploadFile);
		
		$this->data->value = $uploadFile;
		
		$this->ActValue();
	}
	
	private function CheckFile($remove = false){
			if(isset($_FILES['file'])){
				$this->file = $_FILES['file'];
				
				$this->AppendFile($remove);
			} else {
				$this->resp = "10";
			}
	}
	
	private function RemoveFile(){
		$valueItem = $this->ValueItem();
		
		unlink(realpath($valueItem['value']));
	}
	
	private function AppendFile($remove){
			$error = $this->file['error'];
			$name = $this->file['name'];
	
			if($error > 0){
				$this->resp = $error;
			} else {
				$typeDoc = $this->CheckTypeFile($name);
		
				if($typeDoc !== ''){
					
					if($remove){
						$this->RemoveFile();
					}
					
					$uploadfile = $this->ParsePathFile($typeDoc);
					
					move_uploaded_file($this->file['tmp_name'], $uploadfile);
	
					$this->data->value = $uploadfile;
					
					$this->ActValue();
				} else {
					$this->resp = '9';
				}
			}
	}
	
	private function ParsePathFile($typeDoc){
		$menu = $this->modManager->GetUniversity()->SectionItemUpload($this->data->atrid);
		
		$dateDocStr = $this->datedoc[2].".".$this->datedoc[1].".".$this->datedoc[0];
		
		return "data-edu/".$menu."/".$this->namedoc."_".$dateDocStr.$typeDoc;
	}
	
	private function ValueItem(){
		return $this->modManager->GetUniversity()->ValueAttributeItem($this->data->id, true);
	}
	
	
	private function ActValue(){
		$this->modManager->GetUniversity()->ActValueAttribute($this->data);
	}
	
	private function CheckTypeFile($name){
		$typeDoc = "";
		$whitelist = array(".pdf", ".doc", ".docx", ".xls", ".xlsx");
	
		foreach($whitelist as $item){
			if(preg_match("/$item\$/i", $name)) {
				$typeDoc = $item;
				break;
			}
		}
	
		return $typeDoc;
	}
	
	public function ReplaceVarByData(){
		Brick::$builder->brick->content = Brick::ReplaceVarByData(Brick::$builder->brick->content, array(
				'respon' => $this->resp
		));
	}
}

$modManager = Abricos::GetModule('university')->GetManager();

if(!$modManager->IsAdminRole()){
	return;
}

$file = new UploadFile($modManager);
$file->ReplaceVarByData();
?>