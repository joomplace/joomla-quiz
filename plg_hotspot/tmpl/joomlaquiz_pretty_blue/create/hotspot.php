<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaquiz Deluxe class
 */
class JoomlaquizViewCreateHotspot
{
    public static function getQuestionContent($hotspot, $data){

        $live_site = JURI::root();

        $jq_tmpl_html = <<<HTMLEND
		
		var fragment = document.createDocumentFragment();
		var image = document.createElement('img');
		image.src = '{$live_site}images/joomlaquiz/images/{$data['q_data']->c_image}';
		image.classList.add("hotspot__canvas");
		image.addEventListener("click", function(event){
			var circle = this.nextElementSibling
			x = Math.round(event.layerX * 100 / this.getWidth());
			y = Math.round(event.layerY * 100 / this.getHeight());
			
			circle.style.cssText = "left: "+ x +"%; top:"+ y +"%";
			
			document.querySelector('[name=hotspot_x]').value = x
			document.querySelector('[name=hotspot_y]').value = y
		});
		fragment.appendChild(image);
		
		var circle = document.createElement('div');
		circle.classList.add("hotspot__circle");
		fragment.appendChild(circle);
		
		var wrapper = document.createElement('div');
		wrapper.classList.add("hotspot__wrapper");
		wrapper.appendChild(fragment);
		document.getElementById('foo').appendChild(wrapper)
HTMLEND;
        return $jq_tmpl_html;
    }
}
?>
