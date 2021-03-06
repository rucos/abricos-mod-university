<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */


class UniversityModule extends Ab_Module {


    public static $instance;
	
    private $_manager = null;

    public function UniversityModule(){
        UniversityModule::$instance = $this;
        $this->version = "0.1.7";
        $this->name = "university";
        $this->takelink = "university";
        $this->permission = new UniversityPermission($this);
     }

    public function GetManager(){
        if (is_null($this->_manager)){
            require_once 'includes/manager.php';
            $this->_manager = new UniversityManager($this);
        }
        return $this->_manager;
    }
    
    public function GetContentName(){
    	$dir = Abricos::$adress->dir;
    	
    	if(isset($dir[0])){
    		switch ($dir[0]){
    			case 'abitur':
    				return $dir[0];
    		}
    	}
    	
    	if (isset($dir[1])){
    		switch ($dir[1]){
    			case 'upload':
				case 'common':
				case 'struct':
				case 'document':
				case 'education': 
				case 'edustandarts':
				case 'employees':
				case 'objects':
				case 'grants':
				case 'paid_edu':
				case 'budget':
				case 'vacant':
					return $dir[1];
    		}
    	}
    	return '';
    }
    
    public function Bos_IsMenu(){
    	return true;
    }
    
    public function Bos_IsSummary(){
    	return true;
    }
}

class UniversityAction {
    const ADMIN = 50;
}

class UniversityPermission extends Ab_UserPermission {

    public function __construct(UniversityModule $module){
        $defRoles = array(
            new Ab_UserRole(UniversityAction::ADMIN, Ab_UserGroup::ADMIN)
        );
        parent::__construct($module, $defRoles);
    }

    public function GetRoles(){
        return array(
        	UniversityAction::ADMIN => $this->CheckAction(UniversityAction::ADMIN)
        );
    }
}

Abricos::ModuleRegister(new UniversityModule());

?>