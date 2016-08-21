<?php
/**
 * @package Abricos
 * @subpackage university
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

class UploadFile{
	
	/**
	 * Ответ сервера
	 *
	 *
 	 * @var string
 	 * 
 	 * @return
 	 * $1: 'Размер файла превышает допустимое значение UPLOAD_MAX_FILE_SIZE'
	 * $2: 'Размер файла превышает допустимое значение MAX_FILE_SIZE'
	 * $3: 'Не удалось загрузить часть файла'
	 * $4: 'Файл не был загружен'
	 * $6: 'Отсутствует временная папка'
	 * $7: 'Не удалось записать файл на диск'
	 * $8: 'PHP-расширение остановило загрузку файла'
	 * $9: 'Не верный тип файла'
	 * $10: 'Укажите документ для загрузки'
	 * $11: 'Укажите название ссылки на документ'
	 * $12: 'Укажите название документа'
	 * $13: 'Укажите дату утверждения'
	 * $14: 'Не верное название документа! Пример: Pril1_akkred_2014'
	 * $15: 'Файл с таким именем уже существует!'
	 */
	private $_resp = '200';
	
	/**
	 * @var UniversityManager
	 */
	private $_modManager = null;
	
	/**
	 * Объект данных от клиента
	 *
	 * @var object
	 */
	private $_data = null;
	
	/**
	 * Название документа
	 *
	 * @var string
	 */
	private $_namedoc = null;
	
	/**
	 * Дата документа
	 *
	 * @var string
	 */
	private $_datedoc = null;
	
	/**
	 * Принимаемый файл
	 *
	 * @var object
	 */
	private $_file = null;
	
	/**
	 * Название директории
	 *
	 * @var const
	 */
	const NAME_DIR = "data-edu/";
	const UPLOAD_MAX_FILE_SIZE = 15728640;
	
	public function __construct($modManager){
		$this->_modManager = $modManager;
		
			if(isset($_POST['id'])){
				$id = intval($_POST['id']);
				$fill = $this->FillDate($id);
				
				if($fill){
					if($id > 0){
						if(isset($_POST['file'])){
							switch($_POST['file']){
								case 'undefined':
									$this->_resp = "10";
									break;
								case 'rename':
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
			} else {
				$this->_resp = '2';
			}
	}
	
	private function FillDate($id){
		$utmf = Abricos::TextParser(true);
		
		$this->_data = new stdClass();
		$this->_data->id = $id;
		$this->_data->atrid = intval($_POST['atrid']);
		$this->_data->numrow = intval($_POST['numrow']);
		$this->_data->mainid = intval($_POST['mainid']);
			
		$nameurl = $utmf->Parser($_POST['nameurl']);
		if($nameurl === ''){
			$this->_resp = '11';
				return false;
		}
		$this->_data->nameurl = $nameurl;
		$this->_data->view = $utmf->Parser($_POST['view']);
			
		$namedoc = $utmf->Parser($_POST['namedoc']);
		
		if(!preg_match("/[a-z0-9_]+/i", $namedoc)){
			$this->_resp = '12';
			return false;
		} else if(preg_match("/[^a-z0-9_]/i", $namedoc) === 1){
			$this->_resp = '14';
			return false;
		}
		$this->_namedoc = $namedoc;
			
		$datedoc = $utmf->Parser($_POST['datedoc']);
		
		if($datedoc == -1){
			$this->datedoc = "";
		} else {
			if(!preg_match("/[1-2]\d{3}-[01]\d-[0-3]\d/", $datedoc)){
				$this->_resp = '13';
				return false;
			}
			$arrDateDoc = explode('-', $datedoc);
			$this->_datedoc = "_".$arrDateDoc[2].".".$arrDateDoc[1].".".$arrDateDoc[0];
		}

		return true;
	}
	
	private function RenameFile(){
		$value = $this->ValueItem();
		
		preg_match("/.\w{3,4}\$/i", $value, $typeDoc);
		
		$uploadFile = $this->ParsePathFile($typeDoc[0]);
		
		rename($value, UploadFile::NAME_DIR.$uploadFile);
		
		$this->_data->value = $uploadFile;
		
		$this->ActValue();
	}
	
	private function CheckFile($remove = false){
			if(isset($_FILES['file'])){
				$this->_file = $_FILES['file'];
				$error = $this->_file['error'];
				
				if($error > 0){
					$this->_resp = $error;
				} else if($this->_file['size'] > UploadFile::UPLOAD_MAX_FILE_SIZE){
					$this->_resp = "1";
				} else {
					$this->AppendFile($remove);
				}
			} else {
				$this->_resp = "10";
			}
	}
	
	private function RemoveFile(){
		$value = $this->ValueItem();
		
		unlink(realpath($value));
	}
	
	private function AppendFile($remove){
		$name = $this->_file['name'];
		$typeDoc = $this->CheckTypeFile($name);
	
			if($typeDoc !== ''){
				if($remove){
					$this->RemoveFile();
				}
					$uploadfile = $this->ParsePathFile($typeDoc);
					$file = UploadFile::NAME_DIR.$uploadfile;
					
						if(file_exists($file)){
							$this->_resp = '15';
						} else {
							move_uploaded_file($this->_file['tmp_name'], $file);
								
							$this->_data->value = $uploadfile;
								
							$this->ActValue();
						}
			} else {
				$this->_resp = '9';
			}
	}
	
	private function ParsePathFile($typeDoc){
		$menu = $this->_modManager->GetUniversity()->SectionItemUpload($this->_data->atrid);
		
		return $menu."/".$this->_namedoc.$this->_datedoc.$typeDoc;
	}
	
	private function ValueItem(){
		$value = $this->_modManager->GetUniversity()->ValueAttributeItem($this->_data->id, true);
		return $value['value'];
	}
	
	
	private function ActValue(){
		$this->_modManager->GetUniversity()->ActValueAttribute($this->_data);
	}
	
	private function CheckTypeFile($name){
		$typeDoc = "";
		$whitelist = array(".pdf", ".doc", ".docx", ".xls", ".xlsx", ".odt", ".ods");
	
		foreach($whitelist as $item){
			if(preg_match("/$item\$/i", $name)) {
				$typeDoc = $item;
				break;
			}
		}
		return $typeDoc;
	}
	
	/*

	 * */
	public function ReplaceVarByData(){
		Brick::$builder->brick->content = Brick::ReplaceVarByData(Brick::$builder->brick->content, array(
				'respon' => $this->_resp
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