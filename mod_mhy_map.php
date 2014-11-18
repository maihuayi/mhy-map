<?php
/**
 * @copyright Copyright (C) 2014 maihuayi.com, All rights reserved.
 * @license GNU/GPL V2 http://gnu.org/licenses/gpl-2.0.html
 * @author bsdnemo at gmail dot com
 * @link https://github.com/maihuayi/mhy-map
 */

defined('_JEXEC') or die;

require dirname(__FILE__) . '/helper.php';

$map = ModMHYMapHelper::getMap($params);

$moduleclass_sfx = htmlspecialchars($params->get("moduleclass_sfx"));
if($map) {
	require JModuleHelper::getLayoutPath('mod_mhy_map', 'default');
}
