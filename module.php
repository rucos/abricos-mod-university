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
        $this->version = "0.1.0";
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
    
    public function Bos_IsMenu(){
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