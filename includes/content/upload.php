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
		
			$id = intval($_POST['id']);
			$fill = $this->FillDate($id);
			
			if($fill){
				if($id > 0){
					if(isset($_POST['file'])){
						switch($_POST['file']){
							case 'undefined':
								$this->resp = "10";
								break;
							case '':
								$this->RenameFile();
								break;
						}
					} else {
						$this->CheckFile(true);
					}
				} else {
					$this->CheckFile();
				}
			}
	}
	
	private function FillDate($id){
		$utmf = Abricos::TextParser(true);
		
		$this->data = new stdClass();
		$this->data->id = $id;
		$this->data->atrid = intval($_POST['atrid']);
		$this->data->numrow = intval($_POST['numrow']);
			
		$nameurl = $utmf->Parser($_POST['nameurl']);
		if($nameurl === ''){
			$this->resp = '11';
				return false;
		}
		$this->data->nameurl = $nameurl;
		$this->data->view = $utmf->Parser($_POST['view']);
			
		$namedoc = $utmf->Parser($_POST['namedoc']);
		if($namedoc === ''){
			$this->resp = '12';
				return false;
		}
		$this->namedoc = $namedoc;
			
		$datedoc = $utmf->Parser($_POST['datedoc']);
		
		if($datedoc == -1){
			$this->datedoc = "";
		} else {
			if(!preg_match("/[1-2]\d{3}-[01]\d-[0-3]\d/", $datedoc)){
				$this->resp = '13';
				return false;
			}
			$arrDateDoc = explode('-', $datedoc);
			$this->datedoc = "_".$arrDateDoc[2].".".$arrDateDoc[1].".".$arrDateDoc[0];
		}
		
		return true;
	}
	
	private function RenameFile(){
		$value = $this->ValueItem();
		
		preg_match("/.\w{3,4}\$/i", $value, $typeDoc);
		
		$uploadFile = $this->ParsePathFile($typeDoc[0]);
		
		rename($value, $uploadFile);
		
		$this->data->value = $uploadFile;
		
		$this->ActValue();
	}
	
	private function CheckFile($remove = false){
			if(isset($_FILES['file'])){
				$this->file = $_FILES['file'];
				$error = $this->file['error'];
				
				if($error > 0){
					$this->resp = $error;
				} else {
					$this->AppendFile($remove);
				}
			} else {
				$this->resp = "10";
			}
	}
	
	private function RemoveFile(){
		$value = $this->ValueItem();
		
		unlink(realpath($value));
	}
	
	private function AppendFile($remove){
		$name = $this->file['name'];
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
	
	private function ParsePathFile($typeDoc){
		$menu = $this->modManager->GetUniversity()->SectionItemUpload($this->data->atrid);
		
		return "data-edu/".$menu."/".$this->namedoc.$this->datedoc.$typeDoc;
	}
	
	private function ValueItem(){
		$value = $this->modManager->GetUniversity()->ValueAttributeItem($this->data->id, true);
		return $value['value'];
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