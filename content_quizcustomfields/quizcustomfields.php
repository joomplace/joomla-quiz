<?php
/**
* JoomlaQuiz plugin for Joomla
* @version $Id: quizcont.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage quizcont.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

/*

JHtml::_('content.prepare','text',array('param1','param2'),'context');

*/

class plgContentQuizCustomFields extends JPlugin {

	protected function getVisibility(){
		/*
		* 0 - all
		* 1 - registred
		* 2 - guest
		*/
		$visible = $this->params->get('visible', '0');
		switch($visible){
			case 1:
				if(JFactory::getUser()->id){
					return true;
				}else{
					return false;
				}
				break;
			case 2:
				if(JFactory::getUser()->id){
					return false;
				}else{
					return true;
				}
				break;
			default:
				return true;
		}
	}

	function onContentPrepare( $context, &$article, &$params ) {
		
		$html = array();
		switch($context){
			case 'admin.results.table.head':
					if($article->text){
						$fields = explode(',',$article->text);
					}else{
						$fields = array();
						foreach($params as $item){
							if($item->params)
								$fields = array_merge($fields,array_keys(json_decode(str_replace('custom_','',$item->params),true)));
						}
						$fields = array_unique($fields);
					}
					foreach($fields as $field){
						$html[] = '<th>'.JText::_('COM_JOOMLAQUIZ_CUSTOM_FIELD_'.strtoupper($field)).'</th>';
					}
				break;
			case 'admin.results.table.count':
					if($article->text){
						$fields = explode(',',$article->text);
					}else{
						$fields = array();
						foreach($params as $item){
							if($item->params)
								$fields = array_merge($fields,array_keys(json_decode(str_replace('custom_','',$item->params),true)));
						}
						$fields = array_unique($fields);
					}
					$html[] = count($fields);
				break;
			case 'admin.results.table.row':
					if($article->text){
						$keys = explode(',',$article->text);
					}
					
					$fields = array();
					if($params->params){
						$fields = json_decode(str_replace('custom_','',$params->params),true);
					}
					if($keys){
						foreach($keys as $key){
							$html[] = '<td>'.$fields[$key].'</td>';
						}
					}else foreach($fields as $field){
						$html[] = '<td>'.$field.'</td>';
					}
				break;
			case 'admin.results.csv.head':
					if($article->text){
						$fields = explode(',',$article->text);
					}else{
						$fields = array();
						foreach($params as $item){
							if($item->params)
								$fields = array_merge($fields,array_keys(json_decode(str_replace('custom_','',$item->params),true)));
						}
						$fields = array_unique($fields);
					}
					foreach($fields as $field){
						$html[] = ',"'.JText::_('COM_JOOMLAQUIZ_CUSTOM_FIELD_'.strtoupper($field)).'"';
					}
				break;
			case 'admin.results.csv.row':
					if($article->text){
						$keys = explode(',',$article->text);
					}
					
					$fields = array();
					if($params->params){
						$fields = json_decode(str_replace('custom_','',$params->params),true);
					}
					if($keys){
						foreach($keys as $key){
							$html[] = ',"'.$fields[$key].'"';
						}
					}else foreach($fields as $field){
						$html[] = ',"'.$field.'"';
					}
				break;
			case 'admin.results.report.row':
					if($params) {
						$fields = json_decode(str_replace('custom_','',$params),true);
					}
					$fields = array_unique($fields);
					foreach($fields as $field => $value){
						$html[] = '<tr><td align="left">'. JText::_('COM_JOOMLAQUIZ_'.strtoupper($field)).'</td><td>'. $value .'</td></tr>';
					}
				break;
			default:
				
				// Simple performance check to determine whether bot should process further
				if (strpos($article->text, '#') === false)
				{
					return true;
				}
				
				// Expression to search for fields
				$regex		= '/#(.*?)#/i';

				// Find all instances of plugin and put in $matches for loadposition
				// $matches[0] is full pattern match, $matches[1] is the field
				preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

				// No matches, skip this
				if ($matches)
				{
					foreach($matches as $match){
						$fields = array();
						if($params->params){
							$fields = json_decode(str_replace('custom_','',$params->params),true);
						}

						// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
						if($fields[$match[1]]){
							$article->text = preg_replace("|$match[0]|", addcslashes($fields[$match[1]], '\\$'), $article->text, 1);
						}
						
					}
				}
				return true;
		}
		
		$article->text = implode('',$html);
		
		return true;
	}
	
	function onQuizCustomFieldsRender($text) {
		if(strpos($text,'#custom')){
			$regex		= '/#custom\s(.*?)#/i';
			preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
			foreach($matches as $entry){
				$rep_back = '';
				if($this->getVisibility()){
					list($name,$type,$options) = explode(';',$entry[1]);
					if(!$type) $type = 'text';
					list($type,$required) = explode('-', $type);
					
					list($name,$label) = explode(':',$name);
					if(!$label) $label = ucfirst($name);
					if($required) $required = 'required="required"';
					switch($type){
						case 'select':
							$options = explode(',',$options);
							foreach($options as &$opt){
								$opt = explode(':',trim($opt,"()"));
								if(!$opt[1]) $opt[1] = ucfirst($opt[1]);
								$opt = '<option value="'.$opt[0].'">'.$opt[1].'</option>';
							}
							$rep_back = '<label for="jq_cust_'.$name.'">'.$label.': <select id="jq_cust_'.$name.'" '.$required.' name="custom_params['.$name.']" class="custom_params">'. implode("\r",$options) .'</select></label>';
							break;
						default:
							$rep_back = '<label for="jq_cust_'.$name.'">'.$label.': <input type="'.$type.'" id="jq_cust_'.$name.'" '.$required.' name="custom_params['.$name.']" class="custom_params" /></label>';
					}
				}
				$text = str_replace($entry[0], addcslashes($rep_back, '\\$'), $text);
			}
		}
			
		return $text;
	}

	function onQuizCustomFieldsRetrieve() {
		
		$custom_params = JFactory::getApplication()->input->get('custom_params',array(),'array');
		if($custom_params){
			$cust_params = json_encode((object)$custom_params);
		}else{
			$cust_params = $this->onQuizCustomFieldsFromUser();
		}
			
		return $cust_params;
	}

	function onQuizCustomFieldsFromUser() {
		
		$cust_params = json_encode((object)JFactory::getUser()->getParam('custom_profile',array()));
			
		return $cust_params;
	}

	function onQuizCustomFieldsRenderJS() {
		if($this->getVisibility()){
		?>
	var required_check = true;
	jQuery('#jq_quiz_container .custom_params').each(function(){
		if(jQuery( this ).prop('required')){
			if(!jQuery( this ).val()){
				jQuery( this ).focus();
				required_check = false;
			}
		}
	});
	
	if(!required_check){
		jQuery('#jq_quiz_container1').css('opacity', 1);
		jQuery('#jq_quiz_container1').removeClass('jq_ajax_loader');
		return false;
	}
	
	custom_info = jQuery.param(jQuery('#jq_quiz_container .custom_params').serializeArray());
	if(custom_info) custom_info = "&"+custom_info;
		<?php
		}
		return true;
	}

}
