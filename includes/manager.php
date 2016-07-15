<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

class UniversityManager extends Ab_ModuleManager {


    public static $instance;

    public $module = null;

    public function __construct(UniversityModule $module){
        parent::__construct($module);
        UniversityManager::$instance = $this;
    }

    private $_university = null;
    
    public function IsAdminRole(){
    	return $this->IsRoleEnable(UniversityAction::ADMIN);
    }
    
    public function GetBuildSection($brick, $nameSection){
    	require_once 'classes/buildsection.php';
    	return new BuildSection($brick, $nameSection);
    }
    
    /**
     * @return University
     */
    public function GetUniversity() {
        if (empty($this->_university)) {
         	require_once 'dbquery.php';
         	require_once 'classes/models.php';
            require_once 'classes/university.php';
            $this->_university = new University($this);
        }
       
        return $this->_university;
    }

    public function AJAX($d) {
        return $this->GetUniversity()->AJAX($d);
    }
    
    public function Bos_MenuData(){
    	if (!$this->IsAdminRole()){
    		return null;
    	}
    	$i18n = $this->module->I18n();
    	return array(
    			array(
    					"name" => "university",
    					"title" => "Университет",
    					"icon" => "/modules/university/images/cp_icon.png",
    					"url" => "university/wspace/ws"
    			)
    	);
    }

}

?>