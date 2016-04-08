<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

class University extends AbricosApplication {
	
	protected function GetClasses(){
		return array();
	}
	
	
	protected function GetStructures(){
		return '';
	}

	public function ResponseToJSON($d){
		if (!$this->manager->IsAdminRole()){
			return 403;
		}
		
// 		switch ($d->do){
            
//         }
        return null;
    }
}

?>