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

	public $menu = null;
	const SVEDEN = 'Сведения об образовательной организации';
	
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

	public function AppendSysMenu($menu, $name, $parentmenuid = 0, $i = 0){

		$this->db->query_write("
			INSERT INTO ".$this->pfx."sys_menu
			(parentmenuid, menutype, name, title, descript, link, language, menuorder, level, off, dateline, deldate) VALUES
			(".$parentmenuid.", 0, '".$menu."', '".$name."', '', '', '".Abricos::$LNG."', ".$i.", 0, 0, 0, 0)
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

	public function AppendSectionMenu($parentmenuid, $menu, $name, $i){
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
			(".$idSection.", 0, 'simple', 'Дата создания образовательной организации', 'RegDate'),
			(".$idSection.", 0, 'simple', 'Информация об учредителе (учредителях) образовательной организации', 'http://obrnadzor.gov.ru/microformats/UchredLaw'),
			(".$idSection.", 0, 'simple', 'Информация о месте нахождения образовательной организации', 'Address'),
			(".$idSection.", 0, 'simple', 'Информация о месте нахождения филиалов образовательной организации', 'AddressFil'),
			(".$idSection.", 0, 'simple', 'Информация о режиме и графике работы образовательной организации', 'WorkTime'),
			(".$idSection.", 0, 'simple', 'Информация о контактных телефонах образовательной организации', 'Telephone'),
			(".$idSection.", 0, 'simple', 'Информация об адресах электронной почты образовательной организации', 'E-mail')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillStructSection($idSection){
		$rows = "
			(".$idSection.", 0, 'complex', 'Структура университета', '')
		";

		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 'composite', 'Наименования структурных подразделений (органов управления)', 'Name'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация о руководителях структурных подразделений', 'Fio'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация о местах нахождения структурных подразделений', 'AddressStr'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация об адресах официальных сайтов в сети Интернет структурных подразделений', 'Site'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация об адресах электронной почты структурных подразделений', 'E-mail'),
			(".$idSection.", ".$complexid.", 'composite', 'Сведения о наличии положений о структурных подразделениях (об органах управления) с приложением копий указанных положений', 'DivisionClause_DocLink')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillDocumentSection($idSection){

		$rows = "
			(".$idSection.", 0, 'simple', 'Копия устава образовательной организации', 'Ustav_DocLink'),
			(".$idSection.", 0, 'simple', 'Копия лицензии на осуществление образовательной деятельности', 'License_DocLink'),
			(".$idSection.", 0, 'simple', 'Копия свидетельства о государственной аккредитации', 'Accreditation_DocLink'),
			(".$idSection.", 0, 'simple', 'Копия локального нормативного акта, регламентирующего правила приема обучающихся', 'Priem_DocLink'),
			(".$idSection.", 0, 'simple', 'Копия локального нормативного акта, регламентирующего режим занятий обучающихся', 'Mode_DocLink'),
			(".$idSection.", 0, 'simple', 'Копия локального нормативного акта, регламентирующего формы, периодичность и порядок текущего контроля успеваемости и промежуточной аттестации обучающихся', 'Tek_kontrol_DocLink'),
			(".$idSection.", 0, 'simple', 'Копия локального нормативного акта, регламентирующего порядок и основания перевода, отчисления и восстановления обучающихся', 'Perevod_DocLink'),
			(".$idSection.", 0, 'simple', 'Копия локального нормативного акта, регламентирующего порядок оформления возникновения, приостановления и прекращения отношений между образовательной организацией и обучающимися и (или) родителями (законными представителями) несовершеннолетних обучающихся', 'Voz_DocLink'),
			(".$idSection.", 0, 'simple', 'Копия плана финансово-хозяйственной деятельности образовательной организации, утвержденного в установленном законодательством Российской Федерации порядке, или бюджетных смет образовательной организации', 'FinPlan_DocLink'),
			(".$idSection.", 0, 'simple', 'Копия правил внутреннего распорядка обучающихся', 'LocalActStud'),
			(".$idSection.", 0, 'simple', 'Копия правил внутреннего трудового распорядка', 'LocalActOrder'),
			(".$idSection.", 0, 'simple', 'Копия коллективного договора', 'LocalActCollec'),
			(".$idSection.", 0, 'simple', 'Копия локального нормативного акта, регламентирующего размер платы за пользование жилым помещением и коммунальные услуги в общежитии', 'LocalActObSt'),
			(".$idSection.", 0, 'simple', 'Отчет о результатах самообследования', 'ReportEdu_DocLink'),
			(".$idSection.", 0, 'simple', 'Документ о порядке оказания платных образовательных услуг, в том числе образец договора об оказании платных образовательных услуг, документ об утверждении стоимости обучения по каждой образовательной программе', 'PaidEdu_DocLink'),
			(".$idSection.", 0, 'simple', 'Предписания органов, осуществляющих государственный контроль (надзор) в сфере образования', 'Prescription_DocLink'),
			(".$idSection.", 0, 'simple', 'Отчеты об исполнении предписаний органов, осуществляющих государственный контроль (надзор) в сфере образования', 'Prescription_Otchet_DocLink')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillEducationSection($idSection){
		
		$rows = "
			(".$idSection.", 0, 'simple', 'Информация о реализуемых уровнях образования', 'EduLevel'),
			(".$idSection.", 0, 'simple', 'Информация о формах обучения', 'EduForm'),
			(".$idSection.", 0, 'simple', 'Информация о нормативных сроках обучения', 'LearningTerm'),
			(".$idSection.", 0, 'simple', 'Информация о сроке действия государственной аккредитации образовательной программы', 'DateEnd'),
			(".$idSection.", 0, 'simple', 'Cведения о численности лиц, обучающихся за счет бюджета по образовательной программе', 'BudgAmount'),
			(".$idSection.", 0, 'simple', 'Cведения о численности лиц, находящихся на платном обучении по образовательной программе', 'PaidAmount'),
			(".$idSection.", 0, 'simple', 'Информация о языках, на которых осуществляется образование (обучение)', 'language'),
			(".$idSection.", 0, 'simple', 'Информация о направлениях и результатах научной (научно-исследовательской) деятельности и научно-исследовательской базе для ее осуществления', 'http://obrnadzor.gov.ru/microformats/NIR'),
			(".$idSection.", 0, 'simple', 'Информация о результатах приема по каждому направлению подготовки или специальности высшего образования с различными условиями приема', 'http://obrnadzor.gov.ru/microformats/priem'),
			(".$idSection.", 0, 'simple', 'Информация о результатах перевода, восстановления и отчисления', 'http://obrnadzor.gov.ru/microformats/Perevod')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 'complex', 'Образование', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 'composite', 'Уровень образования', 'EduLavel'),
			(".$idSection.", ".$complexid.", 'composite', 'Код специальности, направления подготовки', 'EduCode'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация об описании образовательной программы', 'OOP_main'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация об учебном плане', 'education_plan'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация об аннотации к рабочим программам дисциплин (по каждой дисциплине в составе образовательной программы)', 'education_annotation'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация о календарном учебном графике', 'education_shedule'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация о методических и об иных документах, разработанных образовательной организацией для обеспечения образовательного процесса', 'methodology'),
			(".$idSection.", ".$complexid.", 'composite', 'Информация о практиках, предусмотренных соответствующей образовательной программой', 'EduPr')
		";
		
		$this->AppendUnAttr($rows);
	}
	
	private function FillEduStandartsSection($idSection){
		
		$rows = "
			(".$idSection.", 0, 'simple', 'Копии федеральных государственных образовательных стандартов', 'EduStandartDoc')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillEmployeesSection($idSection){
		
		$rows = "
			(".$idSection.", 0, 'simple', 'Ф.И.О. руководителя образовательной организации', 'fio'),
			(".$idSection.", 0, 'simple', 'Должность руководителя образовательной организации', 'Post'),
			(".$idSection.", 0, 'simple', 'Контактные телефоны руководителя образовательной организации', 'Telephone'),
			(".$idSection.", 0, 'simple', 'Адреса электронной почты руководителя образовательной организации', 'e-mail'),
			(".$idSection.", 0, 'simple', 'Ф.И.О. заместителей руководителя образовательной организации', 'fio'),
			(".$idSection.", 0, 'simple', 'Должность заместителей руководителя образовательной организации', 'Post'),
			(".$idSection.", 0, 'simple', 'Контактные телефоны заместителей руководителя образовательной организации', 'Telephone'),
			(".$idSection.", 0, 'simple', 'Адреса электронной почты заместителей руководителя образовательной организации', 'e-mail'),
			(".$idSection.", 0, 'simple', 'Ф.И.О. руководителей филиалов образовательной организации', 'fio'),
			(".$idSection.", 0, 'simple', 'Должность руководителей филиалов образовательной организации', 'Post'),
			(".$idSection.", 0, 'simple', 'Контактные телефоны руководителей филиалов образовательной организации', 'Telephone'),
			(".$idSection.", 0, 'simple', 'Адреса электронной почты руководителей филиалов образовательной организации', 'e-mail')
		";
		$this->AppendUnAttr($rows);
		
		$rows = "
			(".$idSection.", 0, 'complex', 'Преподаватели', '')
		";
		
		$complexid = $this->AppendUnAttr($rows, true);
		
		$rows = "
			(".$idSection.", ".$complexid.", 'composite', 'Ф.И.О. педагогического работника образовательной организации', 'fio'),
			(".$idSection.", ".$complexid.", 'composite', 'Занимаемая должность (должности) педагогического работника', 'Post'),
			(".$idSection.", ".$complexid.", 'composite', 'Преподаваемые педагогическим работником дисциплины', 'TeachingDiscipline'),
			(".$idSection.", ".$complexid.", 'composite', 'Ученая степень педагогического работника', 'Degree'),
			(".$idSection.", ".$complexid.", 'composite', 'Ученое звание педагогического работника', 'AcademStat'),
			(".$idSection.", ".$complexid.", 'composite', 'Наименование направления подготовки и (или) специальности педагогического работника', 'EmployeeQualification'),
			(".$idSection.", ".$complexid.", 'composite', 'Данные о повышении квалификации и (или) профессиональной переподготовке педагогического работника', 'ProfDevelopment'),
			(".$idSection.", ".$complexid.", 'composite', 'Общий стаж работы педагогического работника', 'GenExperience'),
			(".$idSection.", ".$complexid.", 'composite', 'Стаж работы педагогического работника по специальности', 'SpecExperience')
		";
		
		$this->AppendUnAttr($rows);
	}
	
	private function FillObjectsSection($idSection){
		$rows = "
			(".$idSection.", 0, 'simple', 'Сведения о наличии оборудованных учебных кабинетов', 'PurposeKab'),
			(".$idSection.", 0, 'simple', 'Сведения о наличии объектов для проведения практических занятий', 'PurposePrac'),
			(".$idSection.", 0, 'simple', 'Сведения о наличии библиотек', 'PurposeLibr'),
			(".$idSection.", 0, 'simple', 'Сведения о наличии объектов спорта', 'PurposeSport'),
			(".$idSection.", 0, 'simple', 'Сведения о наличии средств обучения и воспитания', 'PurposeSport'),
			(".$idSection.", 0, 'simple', 'Сведения об условиях питания и охраны здоровья обучающихся', 'Meals'),
			(".$idSection.", 0, 'simple', 'Сведения о доступе к информационным системам и информационно-телекоммуникационным сетям', 'ComNet'),
			(".$idSection.", 0, 'simple', 'Сведения об электронных образовательных ресурсах', 'ERList')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillGrantsSection($idSection){
		$rows = "
			(".$idSection.", 0, 'simple', 'Информация о наличии и условиях предоставления стипендий, в том числе локальные нормативные акты', 'http://obrnadzor.gov.ru/microformats/Grant'),
			(".$idSection.", 0, 'simple', 'Информация о наличии общежития, интерната', 'HostelInfo'),
			(".$idSection.", 0, 'simple', 'Информация о количестве жилых помещений в общежитии, интернате для иногородних обучающихся', 'HostelNum'),
			(".$idSection.", 0, 'simple', 'Копия локального нормативного акта, регламентирующего размер платы за пользование жилым помещением и коммунальные услуги в общежитии', 'LocalActObSt'),
			(".$idSection.", 0, 'simple', 'Информация об иных видах материальной поддержки обучающихся', 'Support'),
			(".$idSection.", 0, 'simple', 'Информация о трудоустройстве выпускников', 'GraduateJob')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillPaidEduSection($idSection){
		$rows = "
			(".$idSection.", 0, 'simple', 'Документ о порядке оказания платных образовательных услуг, в том числе образец договора об оказании платных образовательных услуг, документ об утверждении стоимости обучения', 'PaidEdu_DocLink')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillBudgetSection($idSection){
		$rows = "
			(".$idSection.", 0, 'simple', 'Информация об объеме образовательной деятельности, финансовое обеспечение которой осуществляется за счет бюджетных ассигнований федерального бюджета, бюджетов субъектов Российской Федерации, местных бюджетов, по договорам об образовании за счет средств физических и (или) юридических лиц', 'http://obrnadzor.gov.ru/microformats/Volume'),
			(".$idSection.", 0, 'simple', 'Информация о поступлении и расходовании финансовых и материальных средств', 'http://obrnadzor.gov.ru/microformats/FinRec')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function FillVacantSection($idSection){
		$rows = "
			(".$idSection.", 0, 'simple', 'Информация о количестве вакантных мест для приема (перевода) по каждой образовательной программе, специальности, направлению подготовки', 'http://obrnadzor.gov.ru/microformats/Vacant')
		";
		$this->AppendUnAttr($rows);
	}
	
	private function AppendUnAttr($rows, $ret = false){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."un_attribute(sectionid, complexid, typeattribute, nameattribute, applyattribute)
			VALUES ".$rows."
		");
		
		if($ret){
			return $this->db->insert_id();
		}
	}
}
?>