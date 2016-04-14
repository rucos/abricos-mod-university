<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

class UniversityQuery {
	
	public static function SectionList(Ab_Database $db){
		$sql = "
			SELECT
					s.sectionid as id,
					m.title
			FROM ".$db->prefix."un_section s
			INNER JOIN ".$db->prefix."sys_menu m ON s.menuid = m.menuid
		";
		return $db->query_read($sql);
	}
	
	public static function AttributeList(Ab_Database $db, $sectionid){
		$sql = "
			SELECT
					attributeid as id,
					complexid,
					typeattribute,
					nameattribute,
					applyattribute,
					locate,
					remove
			FROM ".$db->prefix."un_attribute
			WHERE sectionid=".bkint($sectionid)."
			
		";
		return $db->query_read($sql);
	}
	
}

?>