<?php
/**
 * @copyright Copyright (C) 2014 maihuayi.com, All rights reserved.
 * @license GNU/GPL V2 http://gnu.org/licenses/gpl-2.0.html
 * @author bsdnemo at gmail dot com
 * @link https://github.com/maihuayi/mhy-map
 */

defined('_JEXEC') or die;

class ModMHYMapHelper
{
	/**
	 * generate map data
	 * 
	 * @static
	 * @param $params mhy map module settings
	 * @return boolean
	 */
	public static function getMap($params)
	{
		$baidu_coordinate = self::getGeocoding($params);
		$params->set('baidu_coordinate', $baidu_coordinate);
		if (empty($baidu_coordinate))
		{
			return false;
		}
		switch ($params->get("map_type"))
		{
			case "baidu":
					self::getBaiduMap($params);
					break;
		}
		return true;
	}
	
	public static function getGeocoding($params) {
		$url = "http://api.map.baidu.com/geocoder/v2/?address=" . urlencode($params->get('baidu_address')) . "&output=json&ak=" . $params->get('baidu_ak');
		$result = file_get_contents($url);
		$result_json = json_decode($result);
		if($result_json->status == 0 && !empty($result_json->result)) {
			return $result_json->result->location->lng . ',' . $result_json->result->location->lat;
		}
		else {
			return false;	
		}
	}
	
	/**
	 * generate baidu map javascript script
	 * 
	 * @static
	 * @param JFactory $params baidu map settings
	 * @return null
	 */
	public static function getBaiduMap($params)
	{
		$doc = JFactory::getDocument();
		$script = "
				function initialize()
				{
					var map = new BMap.Map('mhy_map');
					var point = new BMap.Point({$params->get('baidu_coordinate')});
					map.centerAndZoom(point, {$params->get('baidu_zoom_level', 18)});";
		if ($params->get("baidu_show_marker"))
		{
			$script .= "
					map.addOverlay(new BMap.Marker(point));
					";
		}
		if ($params->get("baidu_show_navigation"))
		{
			$script .= "
					map.addControl(new BMap.NavigationControl());
					";
		}
		if ($params->get("baidu_show_overview"))
		{
			$script .= "
					map.addControl(new BMap.OverviewMapControl());
					";
		}
		if ($params->get("baidu_show_scale"))
		{
			$script .= "
					map.addControl(new BMap.ScaleControl());
					";
		}
		if ($params->get("baidu_show_map_type"))
		{
			$script .= "
					map.addControl(new BMap.MapTypeControl());
					map.setCurrentCity('{$params->get('baidu_city', '')}');
					";
		}
		if ($params->get("baidu_show_info"))
		{
			$baidu_info_title = htmlspecialchars($params->get("baidu_info_title", ''));
			$baidu_info_content = htmlspecialchars($params->get("baidu_info_content", ''));
			if (!empty($baidu_info_title) || !empty($baidu_info_title))
			{
				$script .= "
						var opts = {
							width : {$params->get('baidu_info_width', 80)},
							height : {$params->get('baidu_info_height', 50)},
							title : '{$baidu_info_title}'
						};
						var infoWindow = new BMap.InfoWindow('{$baidu_info_content}', opts);
						map.openInfoWindow(infoWindow, map.getCenter());
						";
				if ($params->get("baidu_show_marker"))
				{
					$script .= "
							marker.addEventListener('click', function() {this.openInfoWindow(infoWindow);});
							";
				}
			}
		}
		
		// close javascript function
		$script .= "
				}
				";
		
		// set map width and height
		$script .= "window.addEvent('domready', function() {
						$('mhy_map').set('styles', {
							'width' : {$params->get("baidu_width", 600)},
							'height' : {$params->get("baidu_height", 400)}
						});
					});";
		$doc->addScriptDeclaration($script);
		$doc->addScript("http://api.map.baidu.com/api?v=1.5&ak=" . $params->get('baidu_ak') ."&callback=initialize", "text/javascript", true);
	}
}
