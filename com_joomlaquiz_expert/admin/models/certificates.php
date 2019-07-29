<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
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
		switch(intval($certif->crtf_align)) {
			case 1:
				for ($i = 0; $i< $count_lines; $i++) {
					$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
					$ad = intval(($max_w - $cur_w)/2) - intval($max_w/2);
					if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
					imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
					$font_y = $font_y + $text_lines_heights[$i] + 3;
				}
			break;
			case 2:
				for ($i = 0; $i< $count_lines; $i++) {
					$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
					$ad = intval($max_w - $cur_w) - intval($max_w);
					if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
					imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
					$font_y = $font_y + $text_lines_heights[$i] + 3;
				}
			break;
			default:
				for ($i = 0; $i< $count_lines; $i++) {
					$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
					$ad = 0;
					if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
					imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
					$font_y = $font_y + $text_lines_heights[$i] + 3;
				}
			break;
		}
			
			
		$query = "SELECT * FROM #__quiz_cert_fields WHERE cert_id = '{$certif->id}' ORDER BY c_id";
		$database->setQuery($query);
		$fields = $database->loadObjectList();

		$ad = 0;			
		if (is_array($fields) && !empty($fields)) {
			foreach($fields as $field){
				$font = JPATH_SITE . "/media/".(isset($field->font)? $field->font: 'arial.ttf');
                if($field->text_x_center){
                    $box_text =imagettfbbox($field->text_h, 0,$font,$field->f_text);
                    if ($field->shadow)
                    {
                        imagettftext($im, $field->text_h, 0,  $im_fullsize[0]/2 - ($box_text[2]/2) + $ad+2,
                            $field->text_y+2,$grey, $font, $field->f_text);
                    }
                    imagettftext($im, $field->text_h, 0,  $im_fullsize[0]/2 - ($box_text[2]/2) , $field->text_y,
                        $black, $font,
                        $field->f_text);
                }
                else
                {
                    if ($field->shadow)
                    {imagettftext($im, $field->text_h, 0,  $field->text_x + $ad+2, $field->text_y+2,
                        $grey, $font, $field->f_text);
                    }
				imagettftext($im, $field->text_h, 0,  $field->text_x + $ad, $field->text_y, $black, $font, $field->f_text);
			}
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
}
