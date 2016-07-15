<?php
/**
 * @package Abricos
 * @subpackage University
 * @copyright 2016 Kirill Kosaev
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Kirill Kosaev <kosaev-kira@mail.ru>
 */

$man = Abricos::GetModule('university')->GetManager();
$brick = Brick::$builder->brick;

$builder = $man->GetBuildSection($brick, 'common');
$brick->content = $builder->Build();

?>