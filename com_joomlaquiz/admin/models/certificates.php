<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
 
/**
 * Joomlaquiz Deluxe Model
 */
class JoomlaquizModelCertificates extends JModelList
{
     /**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'cert_name',
				'cert_file',
			);
		}
		
		parent::__construct($config);
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		$search = $this->getUserStateFromRequest('categories.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		// List state information.
		parent::populateState('id', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		return parent::getStoreId($id);
	}
	
	public static function delete($cid){
		
	}
	
    /**
    * Method to build an SQL query to load the list data.
	*
    * @return      string  An SQL query
    */
    protected function getListQuery()
    {        
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
		$query->select("*");
		$query->from('`#__quiz_certificates`');
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(cert_name LIKE '.$search.')');
			}
		}
		
        $orderCol	= $this->state->get('list.ordering', 'cert_file');	
		$orderDirn	= $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol.' '.$orderDirn));
	
        return $query;
    }

    public function getCurrDate()
    {
        $db = $this->_db;
        $query = $db->getQuery(true);
        $query->select('c_par_value');
        $query->from('`#__quiz_setup`');
        $query->where("c_par_name='curr_date'");


        $result = $db->setQuery($query)->loadResult();
        if (strtotime("+2 month",strtotime($result))<=strtotime(JFactory::getDate())) {
            return true;
        } else {
            return false;
        }
    }

	public function previewCertificate($id){
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
			
		$database->SetQuery("SELECT * FROM #__quiz_certificates WHERE id = '".$id."'");
		$certif = $database->LoadObjectList();
		$certif = $certif[0];
		$loadFile = JPATH_SITE . "/images/joomlaquiz/images/" . $certif->cert_file;
		$im_fullsize = getimagesize($loadFile);
		if ($im_fullsize[2] == 1) {
			$im = imagecreatefromgif($loadFile); }
		elseif ($im_fullsize[2] == 2) {
			$im = imagecreatefromjpeg($loadFile); }
		elseif ($im_fullsize[2] == 3) {
				$im = imagecreatefrompng($loadFile); }
		else { die();}
		$white = imagecolorallocate($im, 255, 255, 255);
		$grey = imagecolorallocate($im, 128, 128, 128);
		$black = imagecolorallocate($im, 0, 0, 0);
		$font_size = $certif->text_size;
		$font_x = $certif->text_x;
		$font_y = $certif->text_y;

		$font_text = $certif->crtf_text;
		$font = JPATH_SITE . "/media/".(isset($certif->text_font)? $certif->text_font: 'arial.ttf');
		$text_array = explode("\n",$font_text);

		$count_lines = count($text_array);
		$text_lines_xlefts = array();
		$text_lines_xrights = array();
		$text_lines_heights = array();
		for ($i = 0; $i< $count_lines; $i++) {
			$font_box = imagettfbbox($font_size, 0, $font, $text_array[$i]);
			$text_lines_xlefts[$i] = $font_box[0];
			$text_lines_xrights[$i] = $font_box[2];
			$text_lines_heights[$i] = $font_box[1]-$font_box[7];
			if ($text_lines_heights[$i] < $font_size) { $text_lines_heights[$i] = $font_size; }
		}
		$min_x = 0;
		$max_x = 0;
		$max_w = 0;
		for ($i = 0; $i< $count_lines; $i++) {
			if ($min_x > $text_lines_xlefts[$i]) $min_x = $text_lines_xlefts[$i];
			if ($max_x < $text_lines_xrights[$i]) $max_x = $text_lines_xrights[$i];
			if ($max_w < ($text_lines_xrights[$i]-$text_lines_xlefts[$i])) $max_w = ($text_lines_xrights[$i] - $text_lines_xlefts[$i]);
		}
			
		$allow_shadow = ($certif->crtf_shadow == 1);
		$max_width = imagesx($im)-$certif->cert_offset;
				switch(intval($certif->crtf_align)) {
					case 1:
							$this->writeMultilineTextArea($im, $font_size, $font_x, $certif->text_y+400, $black, $font, $grey, $allow_shadow, $font_text, $max_width, $certif->cert_offset,1);
					  		break;
					case 2:		
							$this->writeMultilineTextArea($im, $font_size, $font_x, $font_y+400, $black, $font, $grey, $allow_shadow, $font_text, $max_width, $certif->cert_offset,2);
							break;
					default:
							$this->writeMultilineTextArea($im, $font_size, $font_x, $font_y+400, $black, $font, $grey, $allow_shadow, $font_text, $max_width, $certif->cert_offset);
							break;
				}
			
			
		$query = "SELECT * FROM #__quiz_cert_fields WHERE cert_id = '{$certif->id}' ORDER BY c_id";
		$database->setQuery($query);
		$fields = $database->loadObjectList();

		$ad = 0;			
		if (is_array($fields) && count($fields)) {
			foreach($fields as $field){
				$font = JPATH_SITE . "/media/".(isset($field->font)? $field->font: 'arial.ttf');
				/*if ($field->shadow) imagettftext($im, $field->text_h, 0,  $field->text_x + $ad+2, $field->text_y+2, $grey, $font, $field->f_text);
					
				imagettftext($im, $field->text_h, 0,  $field->text_x + $ad, $field->text_y, $black, $font, $field->f_text);*/
				$max_width = imagesx($im);
				$this->write_multiline_text($im, $field->text_h, $field->text_x + $ad, $field->text_y, $black, $font, $grey, $field->shadow, $field->f_text, $max_width-$certif->cert_offset);
			}
		}
			
		if (preg_match('~Opera(/| )([0-9].[0-9]{1,2})~', $_SERVER['HTTP_USER_AGENT'])) {
			$UserBrowser = "Opera";
		}
		elseif (preg_match('~MSIE ([0-9].[0-9]{1,2})~', $_SERVER['HTTP_USER_AGENT'])) {
			$UserBrowser = "IE";
		} else {
			$UserBrowser = '';
		}
		$file_name = 'Certificate.png';
		header('Content-Type: image/png');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		if ($UserBrowser == 'IE') {
			header('Content-Disposition: inline; filename="' . $file_name . '";');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: inline; filename="' . $file_name . '";');
			header('Pragma: no-cache');
		}
		@ob_end_clean();
		imagepng($im);
		imagedestroy($im);
		exit;
	}
	
	public function sample_certs() {

		$db = JFactory::getDBO();

		$file_dest = JPATH_ADMINISTRATOR . '/components/com_joomlaquiz/sql/other/scripts/question_pool.sql';

		try{
			$chitem = JSchemaChangeitem::getInstance($db, null, file_get_contents($file_dest));
		}catch (RuntimeException $e){
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			return false;
		}

		if (!$chitem){
			return false;
		}

		$chitem->checkStatus = -2;
		$chitem->fix();

		return true;
	}
	
	function write_multiline_text ($image, $font_size, $start_x, $start_y, $color, $font, $grey, $shadow, $text, $max_width)
	{ 
		$words = explode(" ", $text); 
		$string = ""; 
		$tmp_string = ""; 

		for($i = 0; $i < count($words); $i++) { 
			$tmp_string .= $words[$i]." "; 

			//check size of string 
			$dim = imagettfbbox($font_size, 0, $font, $tmp_string); 

			if($dim[4] < ($max_width - $start_x)) { 
				$string = $tmp_string;
				$curr_width = $dim[4];
			} else { 			
				$i--; 
				$tmp_string = ""; 
				//$start_xx = $start_x + round(($max_width - $curr_width - $start_x) / 2);        	
				if ($shadow) imagettftext($image, $font_size, 0, $start_x+2, $start_y+2, $grey, $font, $string);
				imagettftext($image, $font_size, 0, $start_x, $start_y, $color, $font, $string); 

				$string = ""; 
				$start_y += abs($dim[5]) * 1.2; 
				$curr_width = 0;
			} 
		} 

		//$start_xx = $start_x + round(($max_width - $dim[4] - $start_x) / 2);        
    	if ($shadow) imagettftext($image, $font_size, 0, $start_x+2, $start_y+2, $grey, $font, $string);
     	imagettftext($image, $font_size, 0, $start_x, $start_y, $color, $font, $string);
	}


    function writeMultilineTextArea($image, $font_size, $start_x, $start_y, $color, $font, $grey, $shadow, $text, $max_width, $offset, $align = 0)
	{ 
		$words = explode(" ", $text); 
		$string = ""; 
		$tmp_string = ""; 
		for($i = 0; $i < count($words); $i++) { 
			$tmp_string .= $words[$i]." "; 
			$dim = imagettfbbox($font_size, 0, $font, $tmp_string); 

			if($dim[4] < ($max_width - $start_x)) { 
				$string = $tmp_string;
				$curr_width = $dim[4];
			} else { 				
				$i--; 
				$tmp_string = ""; 
				
				switch ($align){
					case 0:
					    $start_xx = $start_x;
                        break;
                    case 1:
					    $start_xx =  $start_x + round(($max_width + $offset - $curr_width - $start_x) / 2);
                        break;
                    case 2:
					    $start_xx =  $start_x + round($max_width + $offset - $curr_width + $dim[6]);
                        break; 							
				}
				if ($shadow) imagettftext($image, $font_size, 0, $start_xx+2, $start_y+2, $grey, $font, $string);
				imagettftext($image, $font_size, 0, $start_xx, $start_y, $color, $font, $string); 

				if ($dim[3] < 10) $dim[3] = $dim[3]*5;
				$start_y += abs($dim[3] * 1.5); 
				$curr_width = 0;
				$string = ""; 
			} 

		} 
		switch ($align){
					case 0:
					    $start_xx = $start_x;
                        break;
                    case 1:
					    $start_xx = $start_x + round(($max_width + $offset - $dim[4] - $start_x) / 2);
                        break;
                    case 2:
					    $start_xx =  $start_x + round($max_width + $offset - $dim[4] + $dim[6]);
						if ($start_xx < 0) $start_xx = 0;
                        break; 							
				}
			   
    	if ($shadow) imagettftext($image, $font_size, 0, $start_xx+2, $start_y+2, $grey, $font, $string);
     	imagettftext($image, $font_size, 0, $start_xx, $start_y, $color, $font, $string);
	}
}
