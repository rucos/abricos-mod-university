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
			(".$idSection.", 'Дата создания образовательной организации', 'RegDate'),
			(".$idSection.", 'Информация об учредителе (учредителях) образовательной организации', 'http://obrnadzor.gov.ru/microformats/UchredLaw'),
			(".$idSection.", 'Информация о месте нахождения образовательной организации', 'Address'),
			(".$idSection.", 'Информация о месте нахождения филиалов образовательной организации (при наличии)', 'AddressFil'),
			(".$idSection.", 'Информация о режиме и графике работы образовательной организации', 'WorkTime'),
			(".$idSection.", 'Информация о контактных телефонах образовательной организации', 'Telephone'),
			(".$idSection.", 'Информация об адресах электронной почты образовательной организации', 'E-mail')
		";
		$this->AppendUnSimpleAttr($rows);
	}
	
	private function FillStructSection($idSection){

		$idComplex = $this->AppendUnComplexAttr($idSection, 'struct');
		
		$rows = "
			(".$idComplex.", 'Наименования структурных подразделений (органов управления)', 'Name'),
			(".$idComplex.", 'Информация о руководителях структурных подразделений', 'Fio'),
			(".$idComplex.", 'Информация о местах нахождения структурных подразделений', 'AddressStr'),
			(".$idComplex.", 'Информация об адресах официальных сайтов в сети Интернет структурных подразделений (при наличии)', 'Site'),
			(".$idComplex.", 'Информация об адресах электронной почты структурных подразделений (при наличии)', 'E-mail'),
			(".$idComplex.", 'Сведения о наличии положений о структурных подразделениях (об органах управления) с приложением копий указанных положений', 'DivisionClause_DocLink')
		";
		
		$this->AppendUnCompositeAttr($rows);
		
	}
	
	private function FillDocumentSection($idSection){
		$rows = "
			(".$idSection.", 'Копия устава образовательной организации', 'Ustav_DocLink'),
			(".$idSection.", 'Копия лицензии на осуществление образовательной деятельности (с приложениями)', 'License_DocLink'),
			(".$idSection.", 'Копия свидетельства о государственной аккредитации (с приложениями)', 'Accreditation_DocLink'),
			(".$idSection.", 'Копия локального нормативного акта, регламентирующего правила приема обучающихся', 'Priem_DocLink'),
			(".$idSection.", 'Копия локального нормативного акта, регламентирующего режим занятий обучающихся', 'Mode_DocLink'),
			(".$idSection.", 'Копия локального нормативного акта, регламентирующего формы, периодичность и порядок текущего контроля успеваемости и промежуточной аттестации обучающихся', 'Tek_kontrol_DocLink'),
			(".$idSection.", 'Копия локального нормативного акта, регламентирующего порядок и основания перевода, отчисления и восстановления обучающихся', 'Perevod_DocLink'),
			(".$idSection.", 'Копия локального нормативного акта, регламентирующего порядок оформления возникновения, приостановления и прекращения отношений между образовательной организацией и обучающимися и (или) родителями (законными представителями) несовершеннолетних обучающихся', 'Voz_DocLink'),
			(".$idSection.", 'Копия плана финансово-хозяйственной деятельности образовательной организации, утвержденного в установленном законодательством Российской Федерации порядке, или бюджетных смет образовательной организации', 'FinPlan_DocLink'),
			(".$idSection.", 'Копия правил внутреннего распорядка обучающихся', 'LocalActStud'),
			(".$idSection.", 'Копия правил внутреннего трудового распорядка', 'LocalActOrder'),
			(".$idSection.", 'Копия коллективного договора', 'LocalActCollec'),
			(".$idSection.", 'Копия локального нормативного акта, регламентирующего размер платы за пользование жилым помещением и коммунальные услуги в общежитии', 'LocalActObSt'),
			(".$idSection.", 'Отчет о результатах самообследования', 'ReportEdu_DocLink'),
			(".$idSection.", 'Документ о порядке оказания платных образовательных услуг, в том числе образец договора об оказании платных образовательных услуг, документ об утверждении стоимости обучения по каждой образовательной программе', 'PaidEdu_DocLink'),
			(".$idSection.", 'Предписания органов, осуществляющих государственный контроль (надзор) в сфере образования', 'Prescription_DocLink'),
			(".$idSection.", 'Отчеты об исполнении предписаний органов, осуществляющих государственный контроль (надзор) в сфере образования', 'Prescription_Otchet_DocLink')
		";
		$this->AppendUnSimpleAttr($rows);
	}
	
	private function FillEducationSection($idSection){
		$rows = "
			(".$idSection.", 'Информация о реализуемых уровнях образования', 'EduLevel'),
			(".$idSection.", 'Информация о формах обучения', 'EduForm'),
			(".$idSection.", 'Информация о нормативных сроках обучения', 'LearningTerm'),
			(".$idSection.", 'Информация о сроке действия государственной аккредитации образовательной программы (при наличии государственной аккредитации)', 'DateEnd'),
			(".$idSection.", 'Cведения о численности лиц, обучающихся за счет бюджета по образовательной программе', 'BudgAmount'),
			(".$idSection.", 'Cведения о численности лиц, находящихся на платном обучении по образовательной программе', 'PaidAmount'),
			(".$idSection.", 'Информация о языках, на которых осуществляется образование (обучение)', 'language'),
			(".$idSection.", 'Информация о направлениях и результатах научной (научно-исследовательской) деятельности и научно-исследовательской базе для ее осуществления', 'http://obrnadzor.gov.ru/microformats/NIR'),
			(".$idSection.", 'Информация о результатах приема по каждому направлению подготовки или специальности высшего образования с различными условиями приема', 'http://obrnadzor.gov.ru/microformats/priem'),
			(".$idSection.", 'Информация о результатах перевода, восстановления и отчисления', 'http://obrnadzor.gov.ru/microformats/Perevod')
		";
		$this->AppendUnSimpleAttr($rows);
		
		$idComplex = $this->AppendUnComplexAttr($idSection, 'edu');
		
		$rows = "
			(".$idComplex.", 'Уровень образования', 'EduLavel'),
			(".$idComplex.", 'Код специальности, направления подготовки', 'EduCode'),
			(".$idComplex.", 'Информация об описании образовательной программы', 'OOP_main'),
			(".$idComplex.", 'Информация об учебном плане', 'education_plan'),
			(".$idComplex.", 'Информация об аннотации к рабочим программам дисциплин (по каждой дисциплине в составе образовательной программы)', 'education_annotation'),
			(".$idComplex.", 'Информация о календарном учебном графике', 'education_shedule'),
			(".$idComplex.", 'Информация о методических и об иных документах, разработанных образовательной организацией для обеспечения образовательного процесса', 'methodology'),
			(".$idComplex.", 'Информация о практиках, предусмотренных соответствующей образовательной программой', 'EduPr')
		";
		
		$this->AppendUnCompositeAttr($rows);
	}
	
	private function FillEduStandartsSection($idSection){
		$rows = "
			(".$idSection.", 'Копии федеральных государственных образовательных стандартов (при их использовании, допускается размещение в подразделе гиперссылки на соответствующие документы на сайте Министерства образования и науки Российской Федерации)', 'EduStandartDoc')
		";
		$this->AppendUnSimpleAttr($rows);
	}
	
	private function FillEmployeesSection($idSection){
		$rows = "
			(".$idSection.", 'Ф.И.О. руководителя образовательной организации', 'fio'),
			(".$idSection.", 'Должность руководителя образовательной организации', 'Post'),
			(".$idSection.", 'Контактные телефоны руководителя образовательной организации', 'Telephone'),
			(".$idSection.", 'Адреса электронной почты руководителя образовательной организации', 'e-mail'),
			(".$idSection.", 'Ф.И.О. заместителей руководителя образовательной организации', 'fio'),
			(".$idSection.", 'Должность заместителей руководителя образовательной организации', 'Post'),
			(".$idSection.", 'Контактные телефоны заместителей руководителя образовательной организации', 'Telephone'),
			(".$idSection.", 'Адреса электронной почты заместителей руководителя образовательной организации', 'e-mail'),
			(".$idSection.", 'Ф.И.О. руководителей филиалов образовательной организации', 'fio'),
			(".$idSection.", 'Должность руководителей филиалов образовательной организации', 'Post'),
			(".$idSection.", 'Контактные телефоны руководителей филиалов образовательной организации', 'Telephone'),
			(".$idSection.", 'Адреса электронной почты руководителей филиалов образовательной организации', 'e-mail')
		";
		$this->AppendUnSimpleAttr($rows);
		
		$idComplex = $this->AppendUnComplexAttr($idSection, 'pedag');
		
		$rows = "
			(".$idComplex.", 'Ф.И.О. педагогического работника образовательной организации', 'fio'),
			(".$idComplex.", 'Занимаемая должность (должности) педагогического работника', 'Post'),
			(".$idComplex.", 'Преподаваемые педагогическим работником дисциплины', 'TeachingDiscipline'),
			(".$idComplex.", 'Ученая степень педагогического работника', 'Degree'),
			(".$idComplex.", 'Ученое звание педагогического работника', 'AcademStat'),
			(".$idComplex.", 'Наименование направления подготовки и (или) специальности педагогического работника', 'EmployeeQualification'),
			(".$idComplex.", 'Данные о повышении квалификации и (или) профессиональной переподготовке педагогического работника', 'ProfDevelopment'),
			(".$idComplex.", 'Общий стаж работы педагогического работника', 'GenExperience'),
			(".$idComplex.", 'Стаж работы педагогического работника по специальности', 'SpecExperience')
		";
		
		$this->AppendUnCompositeAttr($rows);
	}
	
	private function FillObjectsSection($idSection){
		$rows = "
			(".$idSection.", 'Сведения о наличии оборудованных учебных кабинетов', 'PurposeKab'),
			(".$idSection.", 'Сведения о наличии объектов для проведения практических занятий', 'PurposePrac'),
			(".$idSection.", 'Сведения о наличии библиотек', 'PurposeLibr'),
			(".$idSection.", 'Сведения о наличии объектов спорта', 'PurposeSport'),
			(".$idSection.", 'Сведения о наличии средств обучения и воспитания', 'PurposeSport'),
			(".$idSection.", 'Сведения об условиях питания и охраны здоровья обучающихся', 'Meals'),
			(".$idSection.", 'Сведения о доступе к информационным системам и информационно-телекоммуникационным сетям', 'ComNet'),
			(".$idSection.", 'Сведения об электронных образовательных ресурсах', 'ERList')
		";
		$this->AppendUnSimpleAttr($rows);
	}
	
	private function FillGrantsSection($idSection){
		$rows = "
			(".$idSection.", 'Информация о наличии и условиях предоставления стипендий, в том числе локальные нормативные акты', 'http://obrnadzor.gov.ru/microformats/Grant'),
			(".$idSection.", 'Информация о наличии общежития, интерната', 'HostelInfo'),
			(".$idSection.", 'Информация о количестве жилых помещений в общежитии, интернате для иногородних обучающихся', 'HostelNum'),
			(".$idSection.", 'Копия локального нормативного акта, регламентирующего размер платы за пользование жилым помещением и коммунальные услуги в общежитии', 'LocalActObSt'),
			(".$idSection.", 'Информация об иных видах материальной поддержки обучающихся', 'Support'),
			(".$idSection.", 'Информация о трудоустройстве выпускников', 'GraduateJob')
		";
		$this->AppendUnSimpleAttr($rows);
	}
	
	private function FillPaidEduSection($idSection){
		$rows = "
			(".$idSection.", 'Документ о порядке оказания платных образовательных услуг, в том числе образец договора об оказании платных образовательных услуг, документ об утверждении стоимости обучения по каждой образовательной программе', 'PaidEdu_DocLink')
		";
		$this->AppendUnSimpleAttr($rows);
	}
	
	private function FillBudgetSection($idSection){
		$rows = "
			(".$idSection.", 'Информация об объеме образовательной деятельности, финансовое обеспечение которой осуществляется за счет бюджетных ассигнований федерального бюджета, бюджетов субъектов Российской Федерации, местных бюджетов, по договорам об образовании за счет средств физических и (или) юридических лиц', 'http://obrnadzor.gov.ru/microformats/Volume'),
			(".$idSection.", 'Информация о поступлении и расходовании финансовых и материальных средств', 'http://obrnadzor.gov.ru/microformats/FinRec')
		";
		$this->AppendUnSimpleAttr($rows);
	}
	
	private function FillVacantSection($idSection){
		$rows = "
			(".$idSection.", 'Информация о количестве вакантных мест для приема (перевода) по каждой образовательной программе, специальности, направлению подготовки (на места, финансируемые за счет бюджетных ассигнований федерального бюджета, бюджетов субъектов Российской Федерации, местных бюджетов, по договорам об образовании за счет средств физических и (или) юридических лиц)', 'http://obrnadzor.gov.ru/microformats/Vacant')
		";
		$this->AppendUnSimpleAttr($rows);
	}
	
	private function AppendUnComplexAttr($idSection, $name){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."un_complex_attribute (sectionid, nameattribute)
			VALUES (".$idSection.", '".$name."')
		");
		return $this->db->insert_id();
	}
	
	private function AppendUnCompositeAttr($rows){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."un_composite_attribute (complexid, nameattribute, applyattribute)
			VALUES ".$rows."
		");
	}
	
	private function AppendUnSimpleAttr($rows){
		$this->db->query_write("
			INSERT INTO ".$this->pfx."un_simple_attribute (sectionid, nameattribute, applyattribute)
			VALUES ".$rows."
		");
	}
}
?>