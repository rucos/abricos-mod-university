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
	
	public static $docroot;
	
	private $db = null;
	private $pfx = null;
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
		$contentId = $this->AppendContent($sveden);
		
		$this->AppendSysPage($this->sectionid, $contentId);
		$this->FillSection();
	}
	
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

	private function AppendContent($head){
		return Ab_CoreQuery::ContentAppend($this->db, "<h2>".$head."</h2>", 'sitemap');
	}

	private function AppendSysPage($sectionid, $contentId){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."sys_page (menuid, contentid, pagename, title, language, metakeys, metadesc, usecomment, dateline, deldate, mods) VALUES
			(".$sectionid.", ".$contentId.", 'index', '', '".Abricos::$LNG."', '', '', 0, ".TIMENOW.", 0, '')
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
			(".$idSection.", 0, 0, 'simple', 'Дата создания образовательной организации', 'RegDate', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация о месте нахождения образовательной организации', 'Address', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация о месте нахождения филиалов образовательной организации', 'AddressFil', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация о режиме и графике работы образовательной организации', 'WorkTime', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация о контактных телефонах образовательной организации', 'Telephone', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация об адресах электронной почты образовательной организации', 'E-mail', '')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация об учредителе (учредителях) образовательной организации', 'http://obrnadzor.gov.ru/microformats/UchredLaw', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование учредителя', 'Name', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Фамилия, имя, отчество руководителя', 'Fio', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Юридический адрес', 'AddressStr', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Контактный телефон', 'tel', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес сайта', 'url', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адрес электронной почты', 'E-mail', '')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillStructSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Структура университета', '', '')
		";

		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименования структурных подразделений (органов управления)', 'Name', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о руководителях структурных подразделений', 'Fio', 'employees'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о местах нахождения структурных подразделений', 'AddressStr', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация об адресах официальных сайтов в сети Интернет структурных подразделений', 'Site', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация об адресах электронной почты структурных подразделений', 'E-mail', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Сведения о наличии положений о структурных подразделениях (об органах управления) с приложением копий указанных положений', 'DivisionClause_DocLink', '')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillDocumentSection($idSection){

		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Копия устава образовательной организации', 'Ustav_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия лицензии на осуществление образовательной деятельности', 'License_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия свидетельства о государственной аккредитации', 'Accreditation_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего правила приема обучающихся', 'Priem_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего режим занятий обучающихся', 'Mode_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего формы, периодичность и порядок текущего контроля успеваемости и промежуточной аттестации обучающихся', 'Tek_kontrol_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего порядок и основания перевода, отчисления и восстановления обучающихся', 'Perevod_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего порядок оформления возникновения, приостановления и прекращения отношений между образовательной организацией и обучающимися и (или) родителями (законными представителями) несовершеннолетних обучающихся', 'Voz_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия плана финансово-хозяйственной деятельности образовательной организации, утвержденного в установленном законодательством Российской Федерации порядке, или бюджетных смет образовательной организации', 'FinPlan_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия правил внутреннего распорядка обучающихся', 'LocalActStud', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия правил внутреннего трудового распорядка', 'LocalActOrder', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия коллективного договора', 'LocalActCollec', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего размер платы за пользование жилым помещением и коммунальные услуги в общежитии', 'LocalActObSt', ''),
			(".$idSection.", 0, 0, 'simple', 'Отчет о результатах самообследования', 'ReportEdu_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Документ о порядке оказания платных образовательных услуг, в том числе образец договора об оказании платных образовательных услуг, документ об утверждении стоимости обучения по каждой образовательной программе', 'PaidEdu_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Предписания органов, осуществляющих государственный контроль (надзор) в сфере образования', 'Prescription_DocLink', ''),
			(".$idSection.", 0, 0, 'simple', 'Отчеты об исполнении предписаний органов, осуществляющих государственный контроль (надзор) в сфере образования', 'Prescription_Otchet_DocLink', '')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillEducationSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Информация о языках, на которых осуществляется образование (обучение)', 'language', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация о направлениях и результатах научной (научно-исследовательской) деятельности и научно-исследовательской базе для ее осуществления', 'http://obrnadzor.gov.ru/microformats/NIR', '')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Перечень направлений', '', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Образовательная программа', '', 'program'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о реализуемых уровнях образования', 'EduLevel', '')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о нормативных сроках обучения', 'LearningTerm', '')
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Очная форма обучения', 'EduForm', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Очно-заочная форма обучения', 'EduForm', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'Заочная форма обучения', 'EduForm', '')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о сроке действия государственной аккредитации образовательной программы', 'DateEnd', '')
		";
		$this->AppendUnAttr($rows);
		
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Образование', '', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Образовательная программа', '', 'program'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Уровень образования', 'EduLavel', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Код специальности, направления подготовки', 'EduCode', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация об описании образовательной программы', 'OOP_main', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация об учебном плане', 'education_plan', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация об аннотации к рабочим программам дисциплин (по каждой дисциплине в составе образовательной программы)', 'education_annotation', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о календарном учебном графике', 'education_shedule', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о методических и об иных документах, разработанных образовательной организацией для обеспечения образовательного процесса', 'methodology', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Информация о практиках, предусмотренных соответствующей образовательной программой', 'EduPr', '')
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация о численности обучающихся по реализуемым образовательным программам и результаты приема', '', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование специальности/направления подготовки', '', 'program'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Форма обучения', '', '')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Численность обучающихся, чел.', '', '')
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'за счет бюджетных ассигнований федерального бюджета', 'BudgAmount', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'за счет бюджетов субъектов Российской Федерации', 'BudgAmount', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'за счет местных бюджетов', 'BudgAmount', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'за счет средств физических и (или) юридических лиц', 'PaidAmount', '')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Средняя сумма набранных баллов по всем вступительным испытаниям', '', '')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация о результатах перевода, восстановления и отчисления', 'http://obrnadzor.gov.ru/microformats/Perevod', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование специальности/направления подготовки', '', 'program'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Форма обучения', '', '')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Численность обучающихся, чел.', '', '')
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'переведено в другие образовательные организации', '', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'переведено из других образовательных организаций', '', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'восстановлено', '', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'отчислено', '', '')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillEduStandartsSection($idSection){
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Образовательные стандарты', '', '')
		";
	
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование специальности/направления подготовки', '', 'program'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Копии федеральных государственных образовательных стандартов', 'EduStandartDoc', '')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillEmployeesSection($idSection){
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Руководство', '', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Ф.И.О. руководителя образовательной организации', 'fio', 'employees'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Должность руководителя образовательной организации', 'Post', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Контактные телефоны руководителя образовательной организации', 'Telephone', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адреса электронной почты руководителя образовательной организации', 'e-mail', '')
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Заместители руководителя', '', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Ф.И.О. заместителей руководителя образовательной организации', 'fio', 'employees'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Должность заместителей руководителя образовательной организации', 'Post', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Контактные телефоны заместителей руководителя образовательной организации', 'Telephone', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адреса электронной почты заместителей руководителя образовательной организации', 'e-mail', '')
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Руководители филиалов', '', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Ф.И.О. руководителей филиалов образовательной организации', 'fio', 'employees'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Должность руководителей филиалов образовательной организации', 'Post', ''),
			(".$idSection.", ".$complexid.", 0,'composite', 'Контактные телефоны руководителей филиалов образовательной организации', 'Telephone', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Адреса электронной почты руководителей филиалов образовательной организации', 'e-mail', '')
		";
		
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Преподаватели', '', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Ф.И.О. педагогического работника образовательной организации', 'fio', 'employees'),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Занимаемая должность (должности) педагогического работника', 'Post', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Преподаваемые педагогическим работником дисциплины', 'TeachingDiscipline', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Ученая степень педагогического работника', 'Degree', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Ученое звание педагогического работника', 'AcademStat', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование направления подготовки и (или) специальности педагогического работника', 'EmployeeQualification', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Данные о повышении квалификации и (или) профессиональной переподготовке педагогического работника', 'ProfDevelopment', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Общий стаж работы педагогического работника', 'GenExperience', ''),
			(".$idSection.", ".$complexid.", 0, 'composite', 'Стаж работы педагогического работника по специальности', 'SpecExperience', '')
		";
		
		$this->AppendUnAttr($rows);
	}
	
	private function FillObjectsSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Сведения о наличии оборудованных учебных кабинетов', 'PurposeKab', ''),
			(".$idSection.", 0, 0, 'simple', 'Сведения о наличии объектов для проведения практических занятий', 'PurposePrac', ''),
			(".$idSection.", 0, 0, 'simple', 'Сведения о наличии библиотек', 'PurposeLibr', ''),
			(".$idSection.", 0, 0, 'simple', 'Сведения о наличии объектов спорта', 'PurposeSport', ''),
			(".$idSection.", 0, 0, 'simple', 'Сведения о наличии средств обучения и воспитания', 'PurposeSport', ''),
			(".$idSection.", 0, 0, 'simple', 'Сведения об условиях питания и охраны здоровья обучающихся', 'Meals', ''),
			(".$idSection.", 0, 0, 'simple', 'Сведения о доступе к информационным системам и информационно-телекоммуникационным сетям', 'ComNet', ''),
			(".$idSection.", 0, 0, 'simple', 'Сведения об электронных образовательных ресурсах', 'ERList', '')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillGrantsSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Информация о наличии и условиях предоставления стипендий, в том числе локальные нормативные акты', 'http://obrnadzor.gov.ru/microformats/Grant', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация о наличии общежития, интерната', 'HostelInfo', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация о количестве жилых помещений в общежитии, интернате для иногородних обучающихся', 'HostelNum', ''),
			(".$idSection.", 0, 0, 'simple', 'Копия локального нормативного акта, регламентирующего размер платы за пользование жилым помещением и коммунальные услуги в общежитии', 'LocalActObSt', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация об иных видах материальной поддержки обучающихся', 'Support', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация о трудоустройстве выпускников', 'GraduateJob', '')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillPaidEduSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Документ о порядке оказания платных образовательных услуг, в том числе образец договора об оказании платных образовательных услуг, документ об утверждении стоимости обучения', 'PaidEdu_DocLink', '')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillBudgetSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'simple', 'Информация об объеме образовательной деятельности, финансовое обеспечение которой осуществляется за счет бюджетных ассигнований федерального бюджета, бюджетов субъектов Российской Федерации, местных бюджетов, по договорам об образовании за счет средств физических и (или) юридических лиц', 'http://obrnadzor.gov.ru/microformats/Volume', ''),
			(".$idSection.", 0, 0, 'simple', 'Информация о поступлении и расходовании финансовых и материальных средств', 'http://obrnadzor.gov.ru/microformats/FinRec', '')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillVacantSection($idSection){
		$rows = "
			(".$idSection.", 0, 0, 'complex', 'Информация о количестве вакантных мест для приема (перевода) по каждой образовательной программе, специальности, направлению подготовки', 'http://obrnadzor.gov.ru/microformats/Vacant', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Наименование образовательной программы, специальности, направления подготовки', '', 'program')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", ".$complexid.", 0, 'composite', 'Количество вакантных мест для приема (перевода)', '', '')
		";
		$compisteid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'За счет бюджетных ассигнований федерального бюджета', '', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'За счет бюджетных ассигнований бюджетов субъекта Российской Федерации', '', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'За счет бюджетных ассигнований местных бюджетов', '', ''),
			(".$idSection.", ".$complexid.", ".$compisteid.", 'subcomposite', 'За счет средств физических и (или) юридических лиц', '', '')
		";
		$this->AppendUnAttr($rows);
		
		
	}
	
	private function AppendUnAttr($rows, $ret = false){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."un_attribute(sectionid, complexid, compositeid, typeattribute, nameattribute, applyattribute, tablename)
			VALUES ".$rows."
		");
		
		if($ret){
			return $this->db->insert_id();
		}
	}
}
?>