<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

/*
 * 
 * Добавление основного раздела в структуру сайта
 * 
 * */
class Section {

	private $menu = null;
	private $db = null;
	private $pfx = null;


	public function __construct(){
		$this->menu = new stdClass();
		$this->menu->common = 'Основные сведения';
		$this->menu->struct = 'Структура и органы управления';
		$this->menu->document = 'Документы';
		$this->menu->education = 'Образование';
		$this->menu->edustandarts = 'Образовательные стандарты';
		$this->menu->employees = 'Руководство. Педагогический (научно-педагогический) состав';
		$this->menu->objects = 'Материально-техническое обеспечение и оснащенность образовательного процесса';
		$this->menu->grants = 'Стипендии и иные виды материальной поддержки';
		$this->menu->paid_edu = 'Платные образовательне услуги';
		$this->menu->budget = 'Финансово-хозяйственная деятельность';
		$this->menu->vacant = 'Вакантные места для приема(перевода)';

		$this->db = Abricos::$db;
		$this->pfx = Abricos::$db->prefix;
	}

	public function AppendSysMenu($menu, $name, $parentmenuid){

		$this->db->query_write("
			INSERT INTO ".$this->pfx."sys_menu
			(parentmenuid, menutype, name, title, descript, link, language, menuorder, level, off, dateline, deldate) VALUES
			(".$parentmenuid.", 0, '".$menu."', '".$name."', '', '', '".Abricos::$LNG."', 0, 0, 0, 0, 0)
		");
		return $this->db->insert_id();
	}

	public function AppendContent($head){
		return Ab_CoreQuery::ContentAppend($this->db, "<h2>".$head."</h2>", 'sitemap');
	}

	public function AppendSysPage($sectionid, $contentId){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."sys_page (menuid, contentid, pagename, title, language, metakeys, metadesc, usecomment, dateline, deldate, mods) VALUES
			(".$sectionid.", ".$contentId.", 'index', '', '".Abricos::$LNG."', '', '', 0, ".TIMENOW.", 0, '')
		");
	}

	public function AppendSectionMenu($parentmenuid){
		foreach($this->menu as $menu => $name){
			$idcontent = Section::AppendContent($name);

			$menuid = Section::AppendSysMenu($menu, $name, $parentmenuid);

			Section::AppendSysPage($menuid, $idcontent);
				
			Section::AppendUniverSection($menuid);
		}
	}

	public function AppendUniverSection($menuid){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."un_section (menuid)
			VALUES (".$menuid.")
		");
	}
}
?>