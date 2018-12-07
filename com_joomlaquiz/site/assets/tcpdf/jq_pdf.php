<?php
/**
 * Joomlaquiz Component for Joomla 3
 * @package Joomlaquiz
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class jq_pdf {
	var $_engine	= null;

	var $_name		= 'joomla';

	var $_header	= null;

	var $_margin_header	= 5;
	var $_margin_footer	= 10;
	var $_margin_top	= 15;
	var $_margin_bottom	= 15;
	var $_margin_left	= 15;
	var $_margin_right	= 15;

	// Scale ratio for images [number of points in user unit]
	var $_image_scale	= 2;
	
	var $_isRTL			= false;
	
	
	function jq_pdf($options = array()) {
		$config = new JConfig();
		
		if (isset($options['margin-header'])) {
			$this->_margin_header = $options['margin-header'];
		}

		if (isset($options['margin-footer'])) {
			$this->_margin_footer = $options['margin-footer'];
		}

		if (isset($options['margin-top'])) {
			$this->_margin_top = $options['margin-top'];
		}

		if (isset($options['margin-bottom'])) {
			$this->_margin_bottom = $options['margin-bottom'];
		}

		if (isset($options['margin-left'])) {
			$this->_margin_left = $options['margin-left'];
		}

		if (isset($options['margin-right'])) {
			$this->_margin_right = $options['margin-right'];
		}

		if (isset($options['image-scale'])) {
			$this->_image_scale = $options['image-scale'];
		}

		//set mime type
		$this->_mime = 'application/pdf';

		//set document type
		$this->_type = 'pdf'; 
		
		/*
		 * Setup external configuration options
		 */
		define('K_TCPDF_EXTERNAL_CONFIG', true);
	
		/*
		 * Path options
		 */
	
		// Installation path
		define("K_PATH_MAIN", JPATH_SITE . "/components/com_joomlaquiz/assets/tcpdf");
	
		// URL path
		define("K_PATH_URL", JURI::root());
	
		// Fonts path
		define("K_PATH_FONTS", JPATH_SITE . '/components/com_joomlaquiz/assets/tcpdf/fonts/');
	
		// Cache directory path
		define("K_PATH_CACHE", K_PATH_MAIN."/cache");
	
		// Cache URL path
		define("K_PATH_URL_CACHE", K_PATH_URL."/cache");
	
		// Images path
		define("K_PATH_IMAGES", K_PATH_MAIN."/images");
	
		// Blank image path
		define("K_BLANK_IMAGE", K_PATH_IMAGES."/_blank.png");
	
		/*
		 * Format options
		 */
	
		// Cell height ratio
		define("K_CELL_HEIGHT_RATIO", 1.25);
	
		// Magnification scale for titles
		define("K_TITLE_MAGNIFICATION", 1.3);
	
		// Reduction scale for small font
		define("K_SMALL_RATIO", 2/3);
	
		// Magnication scale for head
		define("HEAD_MAGNIFICATION", 1.1);
	
		/*
		 * Create the pdf document
		 */
	
		require_once(JPATH_SITE . '/components/com_joomlaquiz/assets/tcpdf/tcpdf.php');
		
		// Default settings are a portrait layout with an A4 configuration using millimeters as units
		$this->_engine = new TCPDF();

		//set margins
		$this->_engine->SetMargins($this->_margin_left, $this->_margin_top, $this->_margin_right);
		//set auto page breaks
		
		$this->_engine->SetAutoPageBreak(TRUE, $this->_margin_bottom);
		$this->_engine->SetHeaderMargin($this->_margin_header);
		$this->_engine->SetFooterMargin($this->_margin_footer);
		$this->_engine->setImageScale($this->_image_scale); 
		$this->_engine->setRTL($this->_isRTL);

		$this->_engine->setHeaderData('', 0, $config->sitename, $config->live_site." . "._PDF_GENERATED .' '. JHtml::_('date', time() , 'j F, Y, H:i' ) );	
		
		// Set PDF Header and Footer fonts
        $lang = \JFactory::getLanguage()->getTag();
        $alt_lang = array(
            'ar-AA', //Arabic (Unitag)
            'ar-SA', //Arabic (Saudi Arabia)
            'he-IL', //Hebrew (Israel)
            'ja-JP', //Japanese (Japan)
            'zh-CN', //Chinese (China)
            'zh-HK', //Chinese (Hong Kong)
            'zh-TW'  //Chinese (Taiwan)
        );

        if(in_array($lang, $alt_lang)){
            $font = 'javiergb';
        } else {
            $font = 'dejavusans';
        }

		$this->_engine->setHeaderFont(array($font, '', 7));
		$this->_engine->setFooterFont(array($font, '', 7));
		
	}
	
	function cleanText($text)
    {
		$text = trim(strip_tags($text));
        $text = preg_replace('/^\s+$/m', '', $text);
		$text = str_replace("\t",'', $text);
		$text = str_replace("&nbsp;",' ', $text);
		$text = $this->decodeHTML($text);
		return $text;
	}
	
	function get_html_translation_table_my() {
		$trans = get_html_translation_table(HTML_ENTITIES);		
		$trans[chr(32)] = '&nbsp;';    // Space
		$trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
		$trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
		$trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
		$trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
		$trans[chr(134)] = '&dagger;';    // Dagger
		$trans[chr(135)] = '&Dagger;';    // Double Dagger
		$trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
		$trans[chr(137)] = '&permil;';    // Per Mille Sign
		$trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
		$trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
		$trans[chr(140)] = '&OElig;    ';    // Latin Capital Ligature OE
		$trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
		$trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
		$trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
		$trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
		$trans[chr(149)] = '&bull;';    // Bullet
		$trans[chr(150)] = '&ndash;';    // En Dash
		$trans[chr(151)] = '&mdash;';    // Em Dash
		$trans[chr(152)] = '&tilde;';    // Small Tilde
		$trans[chr(153)] = '&trade;';    // Trade Mark Sign
		$trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
		$trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
		$trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
		$trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
		ksort($trans);
		return $trans;
	}
	
	function decodeHTML( $string ) {
		$string = strtr( $string, array_flip($this->get_html_translation_table_my( ) ) );
		//$string = preg_replace( "/&#([0-9]+);/me", "chr('\\1')", $string );
		return $string;
	}
}
?>