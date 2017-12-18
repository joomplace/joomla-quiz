<?php defined('_JEXEC') or die;
require_once(JPATH_BASE . '/components/com_joomlaquiz/assets/qxmlreader.php' );
require_once(JPATH_BASE . '/components/com_joomlaquiz/assets/domit/xml_domit_lite_include.php' );


/**
 * @license http://www.gnu.org/copyleft/lesser.html LGPL License
 **/

class qXMLImport
{
	var $xmlDoc;		// XML Doc Class (Domit or XMLReader)

	var $xmlFile = '';	// XML File full path

	var $isDomit = 0;	// libxml or XMLReader class is not exists
	var $domitRoot;		// rootTag Domit

	var $xmlReaderEmptyContents = false;	// contents error
	var $xmlReaderRootFail = false;			// root tag error

	function __construct( $xmlFile )
	{
		$this->xmlFile = $xmlFile;
		$this->xmlDoc = new QuizXMLReader();

		if ( !$this->xmlDoc->canRun )
		{
			$this->isDomit = 1;
			$this->xmlDoc = new DOMIT_Lite_Document();
			$this->xmlDoc->resolveErrors( true );

			$this->xmlReaderEmptyContents = !$this->xmlDoc->loadXML( $this->xmlFile, false, true );
			$this->domitRoot = &$this->xmlDoc->documentElement;	// set Domit Root tag and check is exists

			$this->xmlReaderRootFail = $this->domitRoot->getTagName() != 'course_backup';
		}
		else
		{
			$contents = file_get_contents($this->xmlFile );

			// Set Contents to XMLReader function, if error return false
			$this->xmlReaderEmptyContents = !$this->xmlDoc->setContents( $contents );

			if ( empty($contents) )
				$this->xmlReaderEmptyContents = true;

			if ( !strpos($contents, '<course_backup') )
				$this->xmlReaderRootFail = true;
		}
		// after init class check error
		$this->checkXMLfile();
	}

	function checkXMLfile()
	{
		if ( $this->xmlReaderEmptyContents )
		{
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_ERROR_DURING')."'); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $this->xmlReaderRootFail )
		{
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_NOT_A_QUIZ_INST')."'); window.history.go(-1); </script>\n";
			exit();
		}
	}

	function JLMS_parse_XML_elements(&$elements, $arr_attrib, $arr_paths, $is_recurse = false, $rname = array(), $arr_attribr = array(), $arr_pathsr = array()) {
		$ret_array = array();
		if (!empty($elements) && is_array($elements)) {
			foreach ($elements as $element) {
				$tmp = new StdClass();
				foreach ($arr_attrib as $attrib) {
					$tmp->$attrib = $element->getAttribute ($attrib);
				}
				foreach ($arr_paths as $path) {

					$test = &$element->getElementsByPath ($path, 1);
					$tmp->$path = $test->getText();
				}
				if ( $is_recurse ) {
					$ii = 0;
					foreach ($rname as $rn) {
						$rn_elements = & $element->getElementsByPath($rn, 1);
						$tmp->$rn = $this->JLMS_parse_XML_elements( $rn_elements->childNodes, $arr_attribr[$ii], $arr_pathsr[$ii]);
						$ii ++;
					}
				}
				$ret_array[] = $tmp;
				unset($tmp);
			}
		}
		return $ret_array;
	}

	function quiz_categories()
	{
		if ( $this->isDomit )
		{
			$element = &$this->domitRoot->getElementsByPath('quiz_categories', 1);
			return $this->JLMS_parse_XML_elements($element->childNodes, array('c_id'), array('c_category', 'c_instruction'));
		}
		else
		{
			return $this->xmlDoc->quiz_getlist('quiz_categories', 'quiz_category');
		}
	}

	function quest_categories()
	{
		if ( $this->isDomit )
		{
			$element = &$this->domitRoot->getElementsByPath('quest_categories', 1);
			return $this->JLMS_parse_XML_elements($element->childNodes, array('c_id'), array('c_category', 'c_instruction'));
		}
		else
		{
			return $this->xmlDoc->quiz_getlist('quest_categories', 'quest_category');
		}
	}

	function certificates()
	{
		if ( $this->isDomit )
		{
			$element = &$this->domitRoot->getElementsByPath('certificates', 1);
			return $this->JLMS_parse_XML_elements($element->childNodes, array('id', 'crtf_align', 'crtf_shadow', 'text_x', 'text_y', 'text_size'), array('crtf_text', 'cert_name', 'cert_file'));
		}
		else
		{
			return $this->xmlDoc->quiz_getlist('quiz_certificates', 'quiz_certificate');
		}
	}

	function quizess()
	{
		if ( $this->isDomit )
		{
			$element = &$this->domitRoot->getElementsByPath('quizess', 1);
			return $this->JLMS_parse_XML_elements($element->childNodes,
				array('id', 'published'),
				array('quiz_category','quiz_userid','quiz_author','quiz_full_score','quiz_title','quiz_description','quiz_short_description','quiz_image', 'quiz_timelimit', 'quiz_minafter', 'quiz_onceperday', 'quiz_passcore', 'quiz_createtime', 'quiz_rmess', 'quiz_wmess', 'quiz_pass_message', 'quiz_unpass_message', 'quiz_enable_review', 'quiz_email_to', 'quiz_enable_print', 'quiz_enable_sertif', 'quiz_skin', 'quiz_random', 'quiz_guest', 'quiz_published', 'quiz_slide', 'quiz_language', 'quiz_certificate', 'quiz_feedback', 'quiz_pool', 'quiz_resbycat', 'quiz_feed_option' ),
				true,
				array('quiz_questions', 'choice_data','match_data','blank_data','hotspot_data'),
				array(
					array('id', 'c_point', 'c_attempts', 'c_type', 'c_ques_cat', 'cq_id', 'ordering','c_random','c_feedback', 'c_qform'),
					array('c_question_id', 'c_right', 'ordering'),
					array('c_question_id', 'ordering'),
					array('c_question_id', 'ordering', 'c_blank_id', 'points', 'css_class'),
					array('c_question_id')
				),
				array(
					array('question_text', 'question_image', 'question_rmess', 'question_wmess'),
					array('choice_text', 'choice_feed', 'choice_point'),
					array('match_text_left', 'match_text_right', 'match_points'),
					array('blank_text'),
					array('hs_start_x', 'hs_start_y', 'hs_width', 'hs_height')
				)
			);
		}
		else
		{
			return $this->xmlDoc->quiz_getlist('quizess', 'quiz', 0);
		}
	}

	function quizess_get_one()
	{
		if ( !$this->isDomit )
		{
			if ( empty($this->isMooved) )
			{
				$this->xmlDoc->move_to_tag('quizess');
				$this->isMooved = 1;
			}

			$this->xmlDoc->reader->read();
			$this->xmlDoc->reader->read();
			$element = $this->xmlDoc->parseBlock('quiz', 0, true);

			if ( !empty($element) )
				return (object)$element;
			else
				return false;
		}
		else
			return false;
	}

	function quizess_pool()
	{
		if ( $this->isDomit )
		{
			$element = &$this->domitRoot->getElementsByPath('quizess_pool', 1);
			return $this->JLMS_parse_XML_elements($element->childNodes,
				array(),
				array(),
				true,
				array('quizzes_question_pool', 'choice_data','match_data','blank_data','hotspot_data'),
				array(
					array('id', 'c_point', 'c_attempts', 'c_type', 'c_ques_cat', 'cq_id', 'ordering','c_random', 'c_qform'),
					array('c_question_id', 'c_right', 'ordering'),
					array('c_question_id', 'ordering'),
					array('c_question_id', 'ordering'),
					array('c_question_id')
				),
				array(
					array('question_text', 'question_image', 'question_rmess', 'question_wmess'),
					array('choice_text', 'choice_feed', 'choice_point'),
					array('match_text_left', 'match_text_right', 'match_points'),
					array('blank_text'),
					array('hs_start_x', 'hs_start_y', 'hs_width', 'hs_height'))
			);
		}
		else
		{
			// repeat read xml file
			$this->xmlDoc->setContents( file_get_contents($this->xmlFile ) );
			return array( $this->xmlDoc->quizess_pool() );
		}
	}
}
