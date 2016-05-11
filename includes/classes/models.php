<?php
/**
 * @package Abricos
 * @subpackage university
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

class SectionItem extends AbricosModel {
	protected $_structModule = 'university';
	protected $_structName = 'SectionItem';
}

class SectionList extends AbricosModelList {

}

class AttributeItem extends AbricosModel {
	protected $_structModule = 'university';
	protected $_structName = 'AttributeItem';
}

class AttributeList extends AbricosModelList {

}

class ValueAttributeItem extends AbricosModel {
	protected $_structModule = 'university';
	protected $_structName = 'ValueItem';
}

class ValueAttributeList extends AbricosModelList {

}

class ProgramItem extends AbricosModel {
	protected $_structModule = 'university';
	protected $_structName = 'ProgramItem';
}

class ProgramList extends AbricosModelList {

}
?>