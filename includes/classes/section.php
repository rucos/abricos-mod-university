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

	/**
	 * Список разделов
	 *
 	 * @var object
	 */
	private $menu = null;
	
	/**
	 * Директория сайта
	 *
 	 * @var string
	 */
	public static $docroot;
	
	/**
	 * База данных 
	 * 
	 * Abricos::$db
	 *
	 * @var string
	 */
	private $db = null;
	
	/**
	 * Префикс базы
	 *
	 * Abricos::$db->prefix
	 *
	 * @var string
	 */
	private $pfx = null;
	
	/**
	 * id основного раздела
	 *
	 * @var int
	 */
	private $sectionid = null; 
	
	public function __construct(){
		$this->docroot = $_SERVER['DOCUMENT_ROOT'];
		
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
		
		$sveden = 'Сведения об образовательной организации';
		$this->sectionid = $this->AppendSysMenu('sveden', $sveden);
		$contentId = $this->AppendContent($sveden, true);
		
		$this->AppendSysPage($this->sectionid, $contentId, true);
		$this->FillSection();
	}
	
	/*
	 * 
	 * Добавление директории data-edu
	 * 
	 * */
	private function FillSection(){
		mkdir($this->docroot."/data-edu", 0700);
		
		$i = 0;
		foreach($this->menu as $menu => $name){
			mkdir($this->docroot."/data-edu/".$menu, 0700);
			$this->AppendSectionMenu($this->sectionid, $menu, $name, ++$i);
		}
	}
	
	private function AppendSysMenu($menu, $name, $parentmenuid = 0, $i = 0){

		$this->db->query_write("
			INSERT INTO ".$this->pfx."sys_menu
			(parentmenuid, menutype, name, title, descript, link, language, menuorder, level, off, dateline, deldate) VALUES
			(".$parentmenuid.", 0, '".$menu."', '".$name."', '', '', '".Abricos::$LNG."', ".$i.", 0, 0, 0, 0)
		");
		return $this->db->insert_id();
	}

	private function AppendContent($head, $isSveden = false){
		if($isSveden){
			$mods = '[mod]university:svedensection[/mod]';
		} else {
			$mods = '[mod]university:edusection[/mod]';
		}
		
		return Ab_CoreQuery::ContentAppend($this->db, "<h2>".$head."</h2>".$mods, 'sitemap');
	}

	private function AppendSysPage($sectionid, $contentId, $isSveden = false){
		if($isSveden){
			$mods = '{"university":{"svedensection":""}}';
		} else {
			$mods = '{"university":{"edusection":""}}';
		}
		
		$this->db->query_write("
			INSERT INTO ".$this->pfx."sys_page (menuid, contentid, pagename, title, language, metakeys, metadesc, usecomment, dateline, deldate, mods, template) VALUES
			(".$sectionid.", ".$contentId.", 'index', '', '".Abricos::$LNG."', '', '', 0, ".TIMENOW.", 0, '".$mods."', 'edu:sveden')
		");
	}

	private function AppendSectionMenu($parentmenuid, $menu, $name, $i){
			$idcontent = Section::AppendContent($name);
			$menuid = Section::AppendSysMenu($menu, $name, $parentmenuid, $i);
			Section::AppendSysPage($menuid, $idcontent);
			$idSection = Section::AppendUniverSection($menuid);
			
			
			/*
			 * 
			 * Добавление атрибутов для каждого раздела
			 * 
			 * */
				switch($menu){
					case 'common':
						 $this->FillCommonSection($idSection);
							break;
					case 'struct':
						$this->FillStructSection($idSection);
							break;
					case 'document':
						$this->FillDocumentSection($idSection);
							break;
					case 'education': 
						$this->FillEducationSection($idSection);
							break;
					case 'edustandarts':
						$this->FillEduStandartsSection($idSection);
							break;
					case 'employees':
						$this->FillEmployeesSection($idSection);
							break;
					case 'objects':
						$this->FillObjectsSection($idSection);
							break;
					case 'grants':
						$this->FillGrantsSection($idSection);
							break;
					case 'paid_edu':
						$this->FillPaidEduSection($idSection);
							break;
					case 'budget':
						$this->FillBudgetSection($idSection);
							break;
					case 'vacant':
						$this->FillVacantSection($idSection);
							break;
				}
			
	}

	private function AppendUniverSection($menuid){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."un_section (menuid)
			VALUES (".$menuid.")
		");
		return $this->db->insert_id();
	}
	
	private function FillCommonSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Дата создания образовательной организации', 'RegDate', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о месте нахождения образовательной организации', 'Address', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о месте нахождения филиалов образовательной организации', 'AddressFil', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о режиме и графике работы образовательной организации', 'WorkTime', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о контактных телефонах образовательной организации', 'Telephone', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация об адресах электронной почты образовательной организации', 'E-mail', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация об учредителе (учредителях) образовательной организации', 'http://obrnadzor.gov.ru/microformats/UchredLaw', '', '', 3, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование учредителя', 'nameUchred', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Фамилия, имя, отчество руководителя', 'fullnameUchred', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Юридический адрес', 'addressUchred', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Контактный телефон', 'telUchred', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес сайта', 'websiteUchred', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес электронной почты', 'mailUchred', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillStructSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Структура университета', '', '', '', 3, 1)
		";

		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименования структурных подразделений', 'Name', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Руководитель структурного подразделения', 'Fio', 'employees', 'fio,post,telephone,email', 4, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Местонахождение структурного подразделения', 'AddressStr', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес официального сайта', 'Site', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес электронной почты', 'E-mail', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Сведения о наличии положений', 'DivisionClause_DocLink', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillDocumentSection($idSection){

		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Копия устава образовательной организации', 'Ustav_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия лицензии на осуществление образовательной деятельности', 'License_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия свидетельства о государственной аккредитации', 'Accreditation_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего правила приема обучающихся', 'Priem_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего режим занятий обучающихся', 'Mode_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего формы, периодичность и порядок текущего контроля успеваемости и промежуточной аттестации обучающихся', 'Tek_kontrol_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего порядок и основания перевода, отчисления и восстановления обучающихся', 'Perevod_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего порядок оформления возникновения, приостановления и прекращения отношений между образовательной организацией и обучающимися и (или) родителями (законными представителями) несовершеннолетних обучающихся', 'Voz_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия плана финансово-хозяйственной деятельности образовательной организации, утвержденного в установленном законодательством Российской Федерации порядке, или бюджетных смет образовательной организации', 'FinPlan_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия правил внутреннего распорядка обучающихся', 'LocalActStud', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия правил внутреннего трудового распорядка', 'LocalActOrder', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия коллективного договора', 'LocalActCollec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего размер платы за пользование жилым помещением и коммунальные услуги в общежитии', 'LocalActObSt', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Отчет о результатах самообследования', 'ReportEdu_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Документ о порядке оказания платных образовательных услуг, в том числе образец договора об оказании платных образовательных услуг, документ об утверждении стоимости обучения по каждой образовательной программе', 'PaidEdu_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Предписания органов, осуществляющих государственный контроль (надзор) в сфере образования', 'Prescription_DocLink', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Отчеты об исполнении предписаний органов, осуществляющих государственный контроль (надзор) в сфере образования', 'Prescription_Otchet_DocLink', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillEducationSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Перечень направлений', '', '', '', 1, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Образовательная программа', 'EduCode', 'program', 'code,name', 1, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о реализуемых уровнях образования', 'EduLevel', 'edulevel', 'level', 1, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о нормативных сроках обучения', 'LearningTerm', '', '', 1, 1)
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$this->UpdateUnAttr($compisteid);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Очная форма обучения', 'EduForm', 'eduform', 'och', 1, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Очно-заочная форма обучения', 'EduForm', 'eduform', 'ochzaoch', 1, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Заочная форма обучения', 'EduForm', 'eduform', 'zaoch', 1, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о сроке действия государственной аккредитации образовательной программы', 'DateEnd', 'edulevel', '', 1, 1)
		";
		$this->AppendUnAttr($rows);
		
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Образование', '', '', '', 2, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Образовательная программа', 'EduCode', 'program', 'code,name', 1, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Описание образовательной программы', 'OOP_main', 'educode', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Учебный план', 'education_plan', 'educode', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Аннотации к рабочим программам дисциплин', 'education_annotation', 'educode', '', 3, 2),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Календарный учебный график', 'education_shedule', 'educode', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Методические и иные документы', 'methodology', 'educode', '', 3, 2),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Практики', 'EduPr', 'educode', '', 3, 2)
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация о численности обучающихся по реализуемым образовательным программам и результаты приема', '', '', '', 1, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование специальности/направления подготовки', 'EduCode', 'program', 'code,name', 1, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Форма обучения', 'EduForm', 'program', '', 1, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Численность обучающихся, чел.', '', '', '', 1, 1)
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$this->UpdateUnAttr($compisteid);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'за счет бюджетных ассигнований федерального бюджета', 'BudgAmount', 'eduform', '', 1, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'за счет бюджетов субъектов Российской Федерации', 'BudgAmount', 'eduform', '', 1, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'за счет местных бюджетов', 'BudgAmount', 'eduform', '', 1, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'за счет средств физических и (или) юридических лиц', 'PaidAmount', 'eduform', '', 1, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Средняя сумма набранных баллов по всем вступительным испытаниям', '', 'eduform', '', 1, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация о результатах перевода, восстановления и отчисления', 'http://obrnadzor.gov.ru/microformats/Perevod', '', '', 1, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование специальности/направления подготовки', 'CodePerevod,SpecialPerevod', 'program', 'code,name', 1, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Форма обучения', 'FullPerevod,DistancePerevod,ParttimePerevod', 'program', '', 1, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Численность обучающихся, чел.', '', '', '', 1, 1)
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$this->UpdateUnAttr($compisteid);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'переведено в другие образовательные организации', 'NumberOutPerevod', 'eduform', '', 1, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'переведено из других образовательных организаций', 'NumberToPerevod', 'eduform', '', 1, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'восстановлено', 'NumberResPerevod', 'eduform', '', 1, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'отчислено', 'NumbeExpPerevod', 'eduform', '', 1, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Информация о языках, на которых осуществляется образование (обучение)', 'language', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о направлениях и результатах научной (научно-исследовательской) деятельности и научно-исследовательской базе для ее осуществления', 'http://obrnadzor.gov.ru/microformats/NIR', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillEduStandartsSection($idSection){
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Образовательные стандарты', '', '', '', 2, 1)
		";
	
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование специальности/направления подготовки', 'EduCode', 'program', 'code,name', 1, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Копии федеральных государственных образовательных стандартов', 'EduStandartDoc', 'educode', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillEmployeesSection($idSection){
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация об администрации образовательной организации', '', '', '', 1, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Фамилия, Имя, Отчество', 'fio', 'employees', 'fio', 1, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Должность', 'Post', 'employees', 'post', 1, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Контактный телефоный', 'Telephone', 'employees', 'telephone', 1, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес электронной почты', 'e-mail', 'employees', 'email', 1, 1)
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация о составе педагогических работников образовательной организации', '', '', '', 3, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Фамилия, Имя, Отчество педагогического работника', 'fio', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Занимаемая должность (должности)', 'Post', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Преподаваемые дисциплины', 'TeachingDiscipline', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Ученая степень', 'Degree', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Ученое звание', 'AcademStat', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование направления подготовки и (или) специальности', 'EmployeeQualification', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Данные о повышении квалификации и (или) профессиональной переподготовке', 'ProfDevelopment', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Общий стаж работы', 'GenExperience', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Стаж работы по специальности', 'SpecExperience', '', '', 3, 1)
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Представители работодателей', '', '', '', 3, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Фамилия, Имя, Отчество', 'fio', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Должность (должности)', 'Post', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Контактный телефон', 'Telephone', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Контактный e-mail', 'e-mail', '', '', 3, 1)
		";
		
		$this->AppendUnAttr($rows);
	}
	
	private function FillObjectsSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Сведения о наличии оборудованных учебных кабинетов', 'PurposeKab', '', '', 3, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование объекта', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес объекта', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Назначение объекта', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Площадь (в кв.м.)', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Документ - основание возникновения права', '', '', '', 3, 1)
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Сведения о наличии объектов для проведения практических занятий', 'PurposePrac', '', '', 3, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование объекта', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес объекта', '', '', '', 3, 1)
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Оборудованные учебные кабинеты', '', '', '', 1, 1)
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$this->UpdateUnAttr($compisteid);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Кол-во', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Общая площадь, м2', '', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Объекты для проведения практических занятий', '', '', '', 1, 1)
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$this->UpdateUnAttr($compisteid);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Кол-во', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Общая площадь, м2', '', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация о наличии библиотек, объектов питания и охраны здоровья обучающихся', 'PurposeLibr,Meals', '', '', 3, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Параметр', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Библиотека', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Столовая/Буфет', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Медицинский пункт', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Студенческая поликлиника', '', '', '', 3, 1)
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Сведения о наличии объектов спорта', 'PurposeSport', '', '', 3, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Вид объекта спорта (спортивного сооружения)', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес местонахождения объекта', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Площадь', '', '', '', 3, 1)
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация о наличии материально-технических условий, обеспечивающих возможность 
					беспрепятственного доступа поступающих с ограниченными возможностями здоровья и (или) инвалидов в аудитории, туалетные и другие помещения, 
					а также их пребывании', '', '', '', 3, 1)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Вид материально-технических условий', '', '', '', 3, 1),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наличие условий (да/нет)', '', '', '', 3, 1)
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Сведения о доступе к информационным системам и информационно-телекоммуникационным сетям', 'ComNet', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Сведения об электронных образовательных ресурсах', 'ERList', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillGrantsSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Информация о наличии и условиях предоставления стипендий, в том числе локальные нормативные акты', 'http://obrnadzor.gov.ru/microformats/Grant', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о наличии общежития, интерната', 'HostelInfo', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о количестве жилых помещений в общежитии, интернате для иногородних обучающихся', 'HostelNum', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего размер платы за пользование жилым помещением и коммунальные услуги в общежитии', 'LocalActObSt', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация об иных видах материальной поддержки обучающихся', 'Support', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о трудоустройстве выпускников', 'GraduateJob', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о наличии общежития', '', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillPaidEduSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Документ о порядке оказания платных образовательных услуг, в том числе образец договора об оказании платных образовательных услуг, документ об утверждении стоимости обучения', 'PaidEdu_DocLink', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillBudgetSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Информация об объеме образовательной деятельности, финансовое обеспечение которой осуществляется за счет бюджетных ассигнований федерального бюджета, бюджетов субъектов Российской Федерации, местных бюджетов, по договорам об образовании за счет средств физических и (или) юридических лиц', 'http://obrnadzor.gov.ru/microformats/Volume', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Объем образовательной деятельности, финансовое обеспечение которой осуществляется за счёт бюджетных ассигнований федерального бюджета', 'FinBFVolume', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Объём образовательной деятельности, финансовое обеспечение которой осуществляется за счёт бюджетов субъектов Российской Федерации', 'FinBRVolume', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Объём образовательной деятельности, финансовое обеспечение которой осуществляется за счёт местных бюджетов', 'FinBMVolume', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Объём образовательной деятельности, финансовое обеспечение которой осуществляется по договорам об образовании за счёт средств физических и (или) юридических лиц', 'FinPVolume', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Информация о поступлении и расходовании финансовых и материальных средств', 'http://obrnadzor.gov.ru/microformats/FinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Ссылка на информацию, представленную на официальном сайте государственных (муниципальных) образовательных организаций', 'BusgovFinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Показатели финансово-хозяйственной деятельности', 'IndicatorFinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Год (отчетный период, за который предоставляются сведения)', 'YearFinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'План финансово-хозяйственной деятельности', 'PlanFinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Источники поступления средств', 'SourceIncomeFinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Объем поступивших средств', 'VolumeIncomeFinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Структура доходов', 'StructIncomeFinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Источники расходования средств', 'SourceCostsFinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Объем расходованных средств', 'SourceCostsFinRec', '', '', 3, 1),
			(".$idSection.", 0, 0, 'simple', 'Структура расходов', 'StructCostsFinRec', '', '', 3, 1)
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillVacantSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация о количестве вакантных мест для приема (перевода) по каждой образовательной программе, специальности, направлению подготовки', 'http://obrnadzor.gov.ru/microformats/Vacant', '', '', 1, 3)
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование образовательной программы, специальности, направления подготовки', 'NameProgVacant,SpecialVacant', 'program', 'code,name', 1, 1)
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Количество вакантных мест для приема (перевода)', '', '', '', 1, 1)
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$this->UpdateUnAttr($compisteid);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'За счет бюджетных ассигнований федерального бюджета', 'NumberBFVacant', 'vakant', '', 1, 3),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'За счет бюджетных ассигнований бюджетов субъекта Российской Федерации', 'NumberBRVacant', 'vakant', '', 1, 3),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'За счет бюджетных ассигнований местных бюджетов', 'NumberBMVacant', 'vakant', '', 1, 3),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'За счет средств физических и (или) юридических лиц', 'NumberPVacant', 'vakant', '', 1, 3)
		";
		$this->AppendUnAttr($rows);
	}
	
	/*
	 * Добавление в базу атрибута
	 * 
	 * */
	
	private function AppendUnAttr($rows, $ret = false){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."un_attribute(sectionid, complexid, compositeid, typeattribute, nameattribute, applyattribute, tablename, fieldname, insertrow, display)
			VALUES ".$rows."
		");
		
		if($ret){
			return $this->db->insert_id();
		}
	}
	
	private function UpdateUnAttr($idAttr){
		$this->db->query_write("
			UPDATE ".$this->pfx."un_attribute
			SET 
				compositeid=".$idAttr."
			WHERE attributeid=".$idAttr."
			LIMIT 1
		");
	}
}
?>