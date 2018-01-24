<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.application.component.controlleradmin');
 
/**
 * Quizzes Controller
 */
class JoomlaquizControllerQuizzes extends JControllerAdmin
{
	
	/**
    * Proxy for getModel.
    * @since       1.6
    */
    public function getModel($name = 'Quizzes', $prefix = 'JoomlaquizModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
	}
	
	public function quizcategories(){
		$this->setRedirect('index.php?option=com_categories&extension=com_joomlaquiz');
	}
	
	public function move_quiz_sel(){
		$cid = $this->input->get('cid', array(), 'array');
		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_ITEM_TO_MOVE')."'); window.history.go(-1);</script>\n";
			exit;
		}
		
		$_SESSION['com_joomlaquiz.move.quizzes.cids'] = $cid;
		$this->setRedirect('index.php?option=com_joomlaquiz&view=quizzes&layout=move_quizzes');
	}
	
	public function move_quizzes(){
		$database = JFactory::getDBO();
		$cid = $_SESSION['com_joomlaquiz.move.quizzes.cids'];
		
		$categoryMove = intval(JFactory::getApplication()->input->get('categorymove'));

		$cids = implode( ',', $cid );
		$total = count( $cid );
		
		$query = "SELECT a.c_title as quiz_name, b.c_category as category_name"
		. "\n FROM #__quiz_t_quiz AS a LEFT JOIN #__quiz_t_category AS b ON b.c_id = a.c_category_id"
		. "\n WHERE a.c_id IN ( $cids ) AND a.c_category_id = '".$categoryMove."'"
		;
		$database->setQuery( $query );
		$items = $database->loadObjectList();
		
		$query = "UPDATE #__quiz_t_quiz"
		. "\n SET c_category_id = '$categoryMove'"
		. "WHERE c_id IN ( $cids )"
		;
		$database->setQuery( $query );
		if ( !$database->execute() ) {
			echo "<script> alert('". $database->getErrorMsg() ."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$query = "SELECT *"
		. "\n FROM #__quiz_t_category"
		. "\n WHERE c_id = '".$categoryMove."'"
		;
		$database->setQuery( $query );
		$categoryNew = $database->loadObject();
		
		$msg = JText::_('COM_JOOMLAQUIZ_QUIZZES_MOVED_TO'). $categoryNew->c_category.".";
		$cats_names="";
		$msg2 = '';
		for($i=0;$i<count($items);$i++)
		{
			if ($i==0) $cats_names .= $items[$i]->quiz_name;
			else
			$cats_names .= ",".$items[$i]->quiz_name;
			$query = "SELECT COUNT(*) FROM #__quiz_t_quiz"
			. "\n WHERE c_category_id = '$categoryMove'"
			. "AND c_title = '".$items[$i]->quiz_name."'"
			;
			$database->setQuery( $query );

			if($database->loadResult() > 1)
			{
				$msg2 = JText::_('COM_JOOMLAQUIZ_NOTE_CATEGORY');
			}
		}
		if($cats_names)
		{
			$msg .= " ".$cats_names.JText::_('COM_JOOMLAQUIZ_MOVED_FROM').$items[0]->category_name.JText::_('COM_JOOMLAQUIZ_TO').$items[0]->category_name.". ";
		}
		
		unset($_SESSION['com_joomlaquiz.move.quizzes.cids']);
		$this->setRedirect( 'index.php?option=com_joomlaquiz&view=quizzes', $msg.$msg2);
	}
	
	public function copy_quiz_sel(){
		$cid = $this->input->get('cid', array(), 'array');
		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('".JText::_('COM_JOOMLAQUIZ_SELECT_AN_ITEM_TO_MOVE')."'); window.history.go(-1);</script>\n";
			exit;
		}
		
		$_SESSION['com_joomlaquiz.copy.quizzes.cids'] = $cid;
		$this->setRedirect('index.php?option=com_joomlaquiz&view=quizzes&layout=copy_quizzes');
	}
	
	public function copy_quizzes()
	{
		$model = $this->getModel();
		$msg = $model->copyQuizzes();
		$this->setRedirect( 'index.php?option=com_joomlaquiz&view=quizzes', $msg);
	}
	
	public function export_quizzes_all()
	{
		$this->export_quizzes(true);
		return true;
	}
	
	public function export_quizzes($all_quizzes = false){
	
	$cid = $this->input->get('cid', array(), 'array');
	if($all_quizzes) $cid = -1;
	$database = JFactory::getDBO();
	
	if (!empty($cid)) {
		require_once(JPATH_BASE."/components/com_joomlaquiz/assets/pcl/pclzip.lib.php");
		
		////////////////////////////////////////////////////////////////////////////////
		//create XML file
		$xml_encoding = 'utf-8';
		if (defined('_ISO')) {
			$iso = explode( '=', _ISO );
			$xml_encoding = $iso[1];
		}
		if($cid != -1)
		{
			$q_cids = implode(',',$cid);	
			$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id IN (".$q_cids.")";
		}
		else
		{
			$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id!=0";
		}	
		$database->SetQuery($query);
		$quiz_data = $database->LoadObjectList();
		$query = "SELECT * FROM #__quiz_t_question WHERE c_quiz_id = 0 ";
		$database->SetQuery($query);
		$pool_data = $database->LoadObjectList();
		$quest_choice = '';
		$quest_match = '';
		$quest_blank = '';
		$quest_distr_blank = '';
		$quest_hotspot = '';
		$quizesname = '';
		$all_images = array();
		$quiz_xml = "";
		$quiz_xml .= "<?xml version=\"1.0\" encoding=\"".$xml_encoding."\" ?>\r\n";
		$quiz_xml .= "\t<course_backup lms_version=\"1.0.0\">\r\n";
		$quiz_xml .= "\n\t\t<name><![CDATA[HJKHJK]]></name>\r\n";
		$quiz_xml .= "\n\t\t<description><![CDATA[JoomlaQuizDelux]]></description>\r\n";
		///-- categories ----///
		
		$query = "SELECT * FROM `#__categories` WHERE `extension`='com_joomlaquiz' AND `published` IN (0,1)";
		$database->SetQuery($query);
		$quiz_cat = $database->LoadObjectList();
		
		$query = "SELECT * FROM `#__categories` WHERE `extension`='com_joomlaquiz.questions' AND `published` IN (0,1)";
		$database->SetQuery($query);
		$quest_cat = $database->LoadObjectList();
		
		$quiz_xml .= "\n\t\t<quiz_categories>";
		if(count($quiz_cat)) {
			for ($i=0, $n=count($quiz_cat); $i < $n; $i++) {
				$quizcat = $quiz_cat[$i];			
				$quiz_xml .= "\n\t\t\t<quiz_category c_id=\"".$quizcat->id."\">";
				$quiz_xml .= "\n\t\t\t<c_category><![CDATA[".$quizcat->title."]]></c_category>";
				$quiz_xml .= "\n\t\t\t<c_instruction><![CDATA[".$quizcat->desc."]]></c_instruction>";
				$quiz_xml .= "\n\t\t\t</quiz_category>";
			}	
		} else { 	
			$query = "SELECT * FROM #__quiz_t_category";
			$database->SetQuery($query);
			$quiz_cat = $database->LoadObjectList();
			for ($i=0, $n=count($quiz_cat); $i < $n; $i++) {
				$quizcat = $quiz_cat[$i];
				$quiz_xml .= "\n\t\t\t<quiz_category c_id=\"".$quizcat->c_id."\">";
				$quiz_xml .= "\n\t\t\t<c_category><![CDATA[".$quizcat->c_category."]]></c_category>";
				$quiz_xml .= "\n\t\t\t<c_instruction><![CDATA[".$quizcat->c_instruction."]]></c_instruction>";
				$quiz_xml .= "\n\t\t\t</quiz_category>";
			}	
		}	
		$quiz_xml .= "\n\t\t</quiz_categories>";
		$quiz_xml .= "\n\t\t<quest_categories>";
		if(count($quest_cat)){
			for ($i=0, $n=count($quest_cat); $i < $n; $i++) {
				$quizcat = $quest_cat[$i];
				$quiz_xml .= "\n\t\t\t<quest_category c_id=\"".$quizcat->id."\">";
				$quiz_xml .= "\n\t\t\t<c_category><![CDATA[".$quizcat->title."]]></c_category>";
				$quiz_xml .= "\n\t\t\t<c_instruction><![CDATA[".$quizcat->desc."]]></c_instruction>";
				$quiz_xml .= "\n\t\t\t</quest_category>";
			}	
		} else {
			$query = "SELECT * FROM #__quiz_q_cat";
			$database->SetQuery($query);
			$quest_cat = $database->LoadObjectList();	
			for ($i=0, $n=count($quest_cat); $i < $n; $i++) {
				$quizcat = $quest_cat[$i];
				$quiz_xml .= "\n\t\t\t<quest_category c_id=\"".$quizcat->qc_id."\">";
				$quiz_xml .= "\n\t\t\t<c_category><![CDATA[".$quizcat->qc_category."]]></c_category>";
				$quiz_xml .= "\n\t\t\t<c_instruction><![CDATA[".$quizcat->qc_instruction."]]></c_instruction>";
				$quiz_xml .= "\n\t\t\t</quest_category>";
			}
		}
		$quiz_xml .= "\n\t\t</quest_categories>";	
		
		///--- certificates ---///
		$query = "SELECT * FROM #__quiz_certificates";
		$database->SetQuery($query);
		$quiz_certificate = $database->LoadObjectList();
		$quiz_xml .= "\n\t\t\t<quiz_certificates>";
		if(count($quiz_certificate))
		for ($i=0, $n=count($quiz_certificate); $i < $n; $i++) {
			$qcert = $quiz_certificate[$i];
			$quiz_xml .= "\n\t\t\t\t<quiz_certificate id=\"".$qcert->id."\" crtf_align=\"".$qcert->crtf_align."\" crtf_shadow=\"".$qcert->crtf_shadow."\"  text_x=\"".$qcert->text_x."\" text_y=\"".$qcert->text_y."\" text_size=\"".$qcert->text_size."\">";
			$quiz_xml .= "\n\t\t\t\t<crtf_text><![CDATA[".$qcert->crtf_text."]]></crtf_text>";
			$quiz_xml .= "\n\t\t\t\t<cert_name><![CDATA[".$qcert->cert_name."]]></cert_name>";
			$quiz_xml .= "\n\t\t\t\t<cert_file><![CDATA[".$qcert->cert_file."]]></cert_file>";
			$quiz_xml .= "\n\t\t\t\t<cert_offset><![CDATA[".$qcert->cert_offset."]]></cert_offset>";
			$quiz_xml .= "\n\t\t\t\t</quiz_certificate>";
			if($qcert->cert_file) 
			{
				if(!in_array($qcert->cert_file,$all_images))
				$all_images[] = $qcert->cert_file;
			}
		}
		$quiz_xml .= "\n\t\t\t</quiz_certificates>";
		///--quizess -----///
		$quiz_xml .= "\n\t\t<quizess_pool>";
		$quiz_xml .= "\n\t\t\t<quizess_poolos>";
		///-- pools --- ///
		$quiz_xml .= "\n\t\t\t<quizzes_question_pool>";
		if(count($pool_data))
		{
			for ($i=0, $n=count($pool_data); $i < $n; $i++) {
			$pool = $pool_data[$i];
			$quiz_xml .= "\n\t\t\t\t\t<quiz_question id=\"".$pool->c_id."\" c_point=\"".$pool->c_point."\" c_attempts=\"".$pool->c_attempts."\" c_type=\"".$pool->c_type."\" c_ques_cat=\"".$pool->c_ques_cat."\" cq_id=\"".$pool->cq_id."\" ordering=\"".$pool->ordering."\" c_random=\"".$pool->c_random."\" c_feedback=\"".$pool->c_feedback."\" c_qform=\"" . $pool->c_qform . "\">";
				$quiz_xml .= "\n\t\t\t\t\t\t<question_text><![CDATA[".$pool->c_question."]]></question_text>";
				$quiz_xml .= "\n\t\t\t\t\t\t<question_image><![CDATA[".$pool->c_image."]]></question_image>";
				$quiz_xml .= "\n\t\t\t\t\t\t<question_rmess><![CDATA[".$pool->c_right_message."]]></question_rmess>";
				$quiz_xml .= "\n\t\t\t\t\t\t<question_wmess><![CDATA[".$pool->c_wrong_message."]]></question_wmess>";
				$quiz_xml .= "\n\t\t\t\t\t</quiz_question>";
				if($pool->c_image) 
				{
					if(!in_array($pool->c_image,$all_images))
					$all_images[] = $pool->c_image;
				}
				switch($pool->c_type)
				{
					case 1:
					case 2:
					case 3:
							$query = "SELECT * FROM #__quiz_t_choice WHERE c_question_id = ".$pool->c_id;
							$database->SetQuery($query);
							$choice_data = $database->LoadObjectList();
							for ($k=0, $nk=count($choice_data); $k < $nk; $k++) {
								$choice = $choice_data[$k];
								$quest_choice .= "\n\t\t\t\t\t<quest_choice c_question_id=\"".$pool->c_id."\" c_right=\"".$choice->c_right."\" ordering=\"".$choice->ordering."\">";
								$quest_choice .= "\n\t\t\t\t\t\t<choice_text><![CDATA[".$choice->c_choice."]]></choice_text>";
								$quest_choice .= "\n\t\t\t\t\t\t<choice_feed><![CDATA[".$choice->c_incorrect_feed."]]></choice_feed>";
								$quest_choice .= "\n\t\t\t\t\t\t<choice_point><![CDATA[".$choice->a_point."]]></choice_point>";
								$quest_choice .= "\n\t\t\t\t\t</quest_choice>";
							}	
							break;
					case 4:
					case 5:
							$query = "SELECT * FROM #__quiz_t_matching WHERE c_question_id = ".$pool->c_id;
							$database->SetQuery($query);
							$choice_data = $database->LoadObjectList();
							for ($k=0, $nk=count($choice_data); $k < $nk; $k++) {
								$choice = $choice_data[$k];
								$quest_match .= "\n\t\t\t\t\t<quest_match c_question_id=\"".$pool->c_id."\" ordering=\"".$choice->ordering."\">";
								$quest_match .= "\n\t\t\t\t\t\t<match_text_left><![CDATA[".$choice->c_left_text."]]></match_text_left>";
								$quest_match .= "\n\t\t\t\t\t\t<match_text_right><![CDATA[".$choice->c_right_text."]]></match_text_right>";
								$quest_match .= "\n\t\t\t\t\t\t<match_points><![CDATA[".$choice->a_points."]]></match_points>";
								$quest_match .= "\n\t\t\t\t\t</quest_match>";
							}	
							break;
					case 6:
							$query = "SELECT t.ordering as ordering, t.c_text as c_text, t.c_blank_id AS c_blank_id, b.points, b.css_class FROM #__quiz_t_blank as b, #__quiz_t_text as t WHERE b.c_id=t.c_blank_id AND b.c_question_id = ".$pool->c_id;
							$database->SetQuery($query);
							$choice_data = $database->LoadObjectList();
							for ($k=0, $nk=count($choice_data); $k < $nk; $k++) {
								$choice = $choice_data[$k];
								$quest_blank .= "\n\t\t\t\t\t<quest_blank c_question_id=\"".$pool->c_id."\" c_blank_id=\"".$choice->c_blank_id."\" points=\"".$choice->points."\" css_class=\"".$choice->css_class."\" ordering=\"".$choice->ordering."\">";
								$quest_blank .= "\n\t\t\t\t\t\t<blank_text><![CDATA[".$choice->c_text."]]></blank_text>";
								$quest_blank .= "\n\t\t\t\t\t</quest_blank>";
							}
							$query = "SELECT c_text, c_id, c_blank_id FROM #__quiz_t_faketext WHERE c_quest_id = ".$pool->c_id;
							$database->SetQuery($query);
							$choice_data = $database->LoadObjectList();
							for ($k=0, $nk=count($choice_data); $k < $nk; $k++) {
								$choice = $choice_data[$k];
								$quest_distr_blank .= "\n\t\t\t\t\t<quest_distr_blank c_question_id=\"".$pool->c_id."\" c_distr_id=\"".$choice->c_id."\">";
								$quest_distr_blank .= "\n\t\t\t\t\t\t<distr_text><![CDATA[".$choice->c_text."]]></distr_text>";
                                $quest_distr_blank .= "\n\t\t\t\t\t\t<distr_blank_id><![CDATA[".$choice->c_blank_id."]]></distr_blank_id>";
								$quest_distr_blank .= "\n\t\t\t\t\t</quest_distr_blank>";
							}
							break;		
					case 7:
							$query = "SELECT * FROM #__quiz_t_hotspot as h WHERE h.c_question_id = ".$pool->c_id;
							$database->SetQuery($query);
							$choice_data = $database->LoadObjectList();
							for ($k=0, $nk=count($choice_data); $k < $nk; $k++) {
								$choice = $choice_data[$k];
								$quest_hotspot .= "\n\t\t\t\t\t<quest_hotspot c_question_id=\"".$pool->c_id."\">";
								$quest_hotspot .= "\n\t\t\t\t\t\t<hs_start_x><![CDATA[".$choice->c_start_x."]]></hs_start_x>";
								$quest_hotspot .= "\n\t\t\t\t\t\t<hs_start_y><![CDATA[".$choice->c_start_y."]]></hs_start_y>";
								$quest_hotspot .= "\n\t\t\t\t\t\t<hs_width><![CDATA[".$choice->c_width."]]></hs_width>";
								$quest_hotspot .= "\n\t\t\t\t\t\t<hs_height><![CDATA[".$choice->c_height."]]></hs_height>";
								$quest_hotspot .= "\n\t\t\t\t\t</quest_hotspot>";
							}
							break;				
				}
			}
		}
		$quiz_xml .= "\n\t\t\t</quizzes_question_pool>";
		$quiz_xml .= "\n\t\t\t\t<choice_data>";
		$quiz_xml .= $quest_choice;
		$quiz_xml .= "\n\t\t\t\t</choice_data>";
		$quiz_xml .= "\n\t\t\t\t<match_data>";
		$quiz_xml .= $quest_match;
		$quiz_xml .= "\n\t\t\t\t</match_data>";
		$quiz_xml .= "\n\t\t\t\t<blank_data>";
		$quiz_xml .= $quest_blank;
		$quiz_xml .= "\n\t\t\t\t</blank_data>";
		$quiz_xml .= "\n\t\t\t\t<hotspot_data>";
		$quiz_xml .= $quest_hotspot;
		$quiz_xml .= "\n\t\t\t\t</hotspot_data>";
		$quiz_xml .= "\n\t\t\t</quizess_poolos>";
		$quiz_xml .= "\n\t\t</quizess_pool>";
		$quest_choice = '';
		$quest_match = '';
		$quest_blank = '';
		$quest_distr_blank = '';
		$quest_hotspot = '';
		//-end pool

		$quiz_xml .= "\n\t\t<quizess>";
		for ($i=0, $n=count($quiz_data); $i < $n; $i++) {
			$quiz = $quiz_data[$i];
			$quizesname .= $quiz->c_title.',';
			$quiz_xml .= "\n\t\t\t<quiz id=\"".$quiz->c_id."\" published=\"".$quiz->published."\">";
			$quiz_xml .= "\n\t\t\t\t<quiz_category>".$quiz->c_category_id."</quiz_category>";
			$quiz_xml .= "\n\t\t\t\t<quiz_userid>".$quiz->c_user_id."</quiz_userid>";
			$quiz_xml .= "\n\t\t\t\t<quiz_author><![CDATA[".$quiz->c_author."]]></quiz_author>";
			$quiz_xml .= "\n\t\t\t\t<quiz_full_score>".$quiz->c_full_score."</quiz_full_score>";
			$quiz_xml .= "\n\t\t\t\t<quiz_title><![CDATA[".$quiz->c_title."]]></quiz_title>";
			$quiz_xml .= "\n\t\t\t\t<quiz_description><![CDATA[".$quiz->c_description."]]></quiz_description>";
            $quiz_xml .= "\n\t\t\t\t<quiz_short_description><![CDATA[".$quiz->c_short_description."]]></quiz_short_description>";
			$quiz_xml .= "\n\t\t\t\t<quiz_image><![CDATA[".$quiz->c_image."]]></quiz_image>";
			$quiz_xml .= "\n\t\t\t\t<quiz_timelimit><![CDATA[".$quiz->c_time_limit."]]></quiz_timelimit>";
			$quiz_xml .= "\n\t\t\t\t<quiz_minafter><![CDATA[".$quiz->c_min_after."]]></quiz_minafter>";
			$quiz_xml .= "\n\t\t\t\t<quiz_number_times><![CDATA[".$quiz->c_number_times."]]></quiz_number_times>";
			$quiz_xml .= "\n\t\t\t\t<quiz_onceperday><![CDATA[".$quiz->c_once_per_day."]]></quiz_onceperday>";
			$quiz_xml .= "\n\t\t\t\t<quiz_passcore><![CDATA[".$quiz->c_passing_score."]]></quiz_passcore>";
			$quiz_xml .= "\n\t\t\t\t<quiz_createtime><![CDATA[".$quiz->c_created_time."]]></quiz_createtime>";
			$quiz_xml .= "\n\t\t\t\t<quiz_rmess><![CDATA[".$quiz->c_right_message."]]></quiz_rmess>";
			$quiz_xml .= "\n\t\t\t\t<quiz_wmess><![CDATA[".$quiz->c_wrong_message."]]></quiz_wmess>";
			$quiz_xml .= "\n\t\t\t\t<quiz_pass_message><![CDATA[".$quiz->c_pass_message."]]></quiz_pass_message>";
			$quiz_xml .= "\n\t\t\t\t<quiz_unpass_message><![CDATA[".$quiz->c_unpass_message."]]></quiz_unpass_message>";
			$quiz_xml .= "\n\t\t\t\t<quiz_enable_review>".$quiz->c_enable_review."</quiz_enable_review>";
			$quiz_xml .= "\n\t\t\t\t<quiz_email_to>".$quiz->c_email_to."</quiz_email_to>";
			$quiz_xml .= "\n\t\t\t\t<quiz_enable_print><![CDATA[".$quiz->c_enable_print."]]></quiz_enable_print>";
			$quiz_xml .= "\n\t\t\t\t<quiz_enable_sertif><![CDATA[".$quiz->c_enable_sertif."]]></quiz_enable_sertif>";
			$quiz_xml .= "\n\t\t\t\t<quiz_skin><![CDATA[".$quiz->c_skin."]]></quiz_skin>";
			$quiz_xml .= "\n\t\t\t\t<quiz_random>".$quiz->c_random."</quiz_random>";
			$quiz_xml .= "\n\t\t\t\t<quiz_guest>".$quiz->c_guest."</quiz_guest>";
			$quiz_xml .= "\n\t\t\t\t<quiz_published><![CDATA[".$quiz->published."]]></quiz_published>";
			$quiz_xml .= "\n\t\t\t\t<quiz_slide><![CDATA[".$quiz->c_slide."]]></quiz_slide>";
			$quiz_xml .= "\n\t\t\t\t<quiz_language><![CDATA[".$quiz->c_language."]]></quiz_language>";
			$quiz_xml .= "\n\t\t\t\t<quiz_certificate><![CDATA[".$quiz->c_certificate."]]></quiz_certificate>";
			$quiz_xml .= "\n\t\t\t\t<quiz_feedback><![CDATA[".$quiz->c_feedback."]]></quiz_feedback>";
			$quiz_xml .= "\n\t\t\t\t<quiz_pool><![CDATA[".$quiz->c_pool."]]></quiz_pool>";
			$quiz_xml .= "\n\t\t\t\t<quiz_auto_breaks>".$quiz->c_auto_breaks."</quiz_auto_breaks>";
			$quiz_xml .= "\n\t\t\t\t<quiz_resbycat>".$quiz->c_resbycat."</quiz_resbycat>";
			$quiz_xml .= "\n\t\t\t\t<quiz_feed_option>".$quiz->c_feed_option."</quiz_feed_option>";
			$quiz_xml .= "\n\t\t\t\t<quiz_paid_check>".$quiz->paid_check."</quiz_paid_check>";
			$quiz_xml .= "\n\t\t\t\t<quiz_pagination>".$quiz->c_pagination."</quiz_pagination>";

			$query = "SELECT * FROM #__quiz_t_question WHERE c_quiz_id = ".$quiz->c_id;
			$database->SetQuery($query);
			$quest_data = $database->LoadObjectList();

			$query = "SELECT `c_question_id` FROM #__quiz_t_pbreaks";
			$database->SetQuery($query);
			$pbreaks_data = $database->LoadObjectList();

			$pbreaks = array();

			foreach ($pbreaks_data as $array) {
				array_push($pbreaks, $array->c_question_id);
			}

			$quiz_xml .= "\n\t\t\t\t<quiz_questions>";
			for ($j=0, $nj=count($quest_data); $j < $nj; $j++) {
				$quest = $quest_data[$j];
				$quiz_xml .= "\n\t\t\t\t\t<quiz_question id=\"".$quest->c_id."\" c_point=\"".$quest->c_point."\" c_attempts=\"".$quest->c_attempts."\" c_type=\"".$quest->c_type."\" c_ques_cat=\"".$quest->c_ques_cat."\" cq_id=\"".$quest->cq_id."\" ordering=\"".$quest->ordering."\" c_random=\"".$quest->c_random."\" c_feedback=\"".$quest->c_feedback."\" c_qform=\"" . $quest->c_qform . "\">";
				//$quiz_xml .= "\n\t\t\t\t\t\t<c_qform><![CDATA[" . $quest->c_qform . "]]></c_qform>";;
				$quiz_xml .= "\n\t\t\t\t\t\t<question_text><![CDATA[".$quest->c_question."]]></question_text>";
				$quiz_xml .= "\n\t\t\t\t\t\t<question_image><![CDATA[".$quest->c_image."]]></question_image>";
				$quiz_xml .= "\n\t\t\t\t\t\t<question_rmess><![CDATA[".$quest->c_right_message."]]></question_rmess>";
				$quiz_xml .= "\n\t\t\t\t\t\t<question_wmess><![CDATA[".$quest->c_wrong_message."]]></question_wmess>";


				if (array_search($quest->c_id, $pbreaks) === false) $quiz_xml .= "\n\t\t\t\t\t\t<question_pbeaks><![CDATA[0]]></question_pbeaks>";
				else $quiz_xml .= "\n\t\t\t\t\t\t<question_pbeaks><![CDATA[1]]></question_pbeaks>";


				$quiz_xml .= "\n\t\t\t\t\t</quiz_question>";
				if($quest->c_image) 
				{
					if(!in_array($quest->c_image,$all_images))
					$all_images[] = $quest->c_image;
				}
				
				switch($quest->c_type)
				{
					case 1:
					case 2:
					case 3:
							$query = "SELECT * FROM #__quiz_t_choice WHERE c_question_id = ".$quest->c_id;
							$database->SetQuery($query);
							$choice_data = $database->LoadObjectList();
							for ($k=0, $nk=count($choice_data); $k < $nk; $k++) {
								$choice = $choice_data[$k];
								$quest_choice .= "\n\t\t\t\t\t<quest_choice c_question_id=\"".$quest->c_id."\" c_right=\"".$choice->c_right."\" ordering=\"".$choice->ordering."\">";
								$quest_choice .= "\n\t\t\t\t\t\t<choice_text><![CDATA[".$choice->c_choice."]]></choice_text>";
								$quest_choice .= "\n\t\t\t\t\t\t<choice_feed><![CDATA[".$choice->c_incorrect_feed."]]></choice_feed>";
								$quest_choice .= "\n\t\t\t\t\t\t<choice_point><![CDATA[".$choice->a_point."]]></choice_point>";
								$quest_choice .= "\n\t\t\t\t\t</quest_choice>";
							}	
							break;
					case 4:
					case 5:
							$query = "SELECT * FROM #__quiz_t_matching WHERE c_question_id = ".$quest->c_id;
							$database->SetQuery($query);
							$choice_data = $database->LoadObjectList();
							for ($k=0, $nk=count($choice_data); $k < $nk; $k++) {
								$choice = $choice_data[$k];
								$quest_match .= "\n\t\t\t\t\t<quest_match c_question_id=\"".$quest->c_id."\" ordering=\"".$choice->ordering."\">";
								$quest_match .= "\n\t\t\t\t\t\t<match_text_left><![CDATA[".$choice->c_left_text."]]></match_text_left>";
								$quest_match .= "\n\t\t\t\t\t\t<match_text_right><![CDATA[".$choice->c_right_text."]]></match_text_right>";
								$quest_match .= "\n\t\t\t\t\t\t<match_points><![CDATA[".$choice->a_points."]]></match_points>";
								$quest_match .= "\n\t\t\t\t\t</quest_match>";
							}	
							break;
					case 6:
							$query = "SELECT t.ordering as ordering,t.c_text as c_text, t.c_blank_id AS c_blank_id, b.points, b.css_class FROM #__quiz_t_blank as b, #__quiz_t_text as t WHERE b.c_id=t.c_blank_id AND b.c_question_id = ".$quest->c_id;
							$database->SetQuery($query);
							$choice_data = $database->LoadObjectList();
							for ($k=0, $nk=count($choice_data); $k < $nk; $k++) {
								$choice = $choice_data[$k];
								$quest_blank .= "\n\t\t\t\t\t<quest_blank c_question_id=\"".$quest->c_id."\" c_blank_id=\"".$choice->c_blank_id."\"  points=\"".$choice->points."\" css_class=\"".$choice->css_class."\" ordering=\"".$choice->ordering."\">";
								$quest_blank .= "\n\t\t\t\t\t\t<blank_text><![CDATA[".$choice->c_text."]]></blank_text>";
								$quest_blank .= "\n\t\t\t\t\t</quest_blank>";
							}
							break;		
					case 7:
							$query = "SELECT * FROM #__quiz_t_hotspot as h WHERE h.c_question_id = ".$quest->c_id;
							$database->SetQuery($query);
							$choice_data = $database->LoadObjectList();
							for ($k=0, $nk=count($choice_data); $k < $nk; $k++) {
								$choice = $choice_data[$k];
								$quest_hotspot .= "\n\t\t\t\t\t<quest_hotspot c_question_id=\"".$quest->c_id."\">";
								$quest_hotspot .= "\n\t\t\t\t\t\t<hs_start_x><![CDATA[".$choice->c_start_x."]]></hs_start_x>";
								$quest_hotspot .= "\n\t\t\t\t\t\t<hs_start_y><![CDATA[".$choice->c_start_y."]]></hs_start_y>";
								$quest_hotspot .= "\n\t\t\t\t\t\t<hs_width><![CDATA[".$choice->c_width."]]></hs_width>";
								$quest_hotspot .= "\n\t\t\t\t\t\t<hs_height><![CDATA[".$choice->c_height."]]></hs_height>";
								$quest_hotspot .= "\n\t\t\t\t\t</quest_hotspot>";
							}
							break;				
				}
			}
			$quiz_xml .= "\n\t\t\t\t</quiz_questions>";
			$quiz_xml .= "\n\t\t\t\t<choice_data>";
			$quiz_xml .= $quest_choice;
			$quiz_xml .= "\n\t\t\t\t</choice_data>";
			$quiz_xml .= "\n\t\t\t\t<match_data>";
			$quiz_xml .= $quest_match;
			$quiz_xml .= "\n\t\t\t\t</match_data>";
			$quiz_xml .= "\n\t\t\t\t<blank_data>";
			$quiz_xml .= $quest_blank;
			$quiz_xml .= "\n\t\t\t\t</blank_data>";
			$quiz_xml .= "\n\t\t\t\t<hotspot_data>";
			$quiz_xml .= $quest_hotspot;
			$quiz_xml .= "\n\t\t\t\t</hotspot_data>";
			$quiz_xml .= "\n\t\t\t</quiz>";
		}
		$quiz_xml .= "\n\t\t</quizess>";
		$quiz_xml .= "\n\t\t</course_backup>";	
		
		$filename_xml = JPATH_SITE.'/tmp/export.xml';
		$handle = fopen($filename_xml, 'w+');

		// try to write in XML file our xml-contents.
		if (fwrite($handle, $quiz_xml) === FALSE) {
			echo JText::_('COM_JOOMLAQUIZ_COULD_NOT_CREATE');
			exit;
		}
		fclose($handle);

		$uniq = strtotime(JFactory::getDate());
		$dir = JPATH_SITE."/tmp/";
		$backup_zip = $dir.'course_export_'.$uniq.'.zip';
		$pz = new PclZip($backup_zip);
		//----insert into database-----//
		$curdata = date("Y-m-d");
		$query = "INSERT INTO #__quiz_export(eid,e_filename,e_date,e_quizes) values('','course_export_".$uniq.".zip','".$curdata."','".$database->escaped($this->jq_substr($quizesname,0,strlen($quizesname)-1))."')";
		$database->setQuery($query);
		$database->execute();
		//add _lms_course_files_ catalog
		$pz->create($filename_xml, PCLZIP_OPT_REMOVE_PATH, $filename_xml = JPATH_SITE.'/tmp/');
		
		if(count($all_images))
		foreach($all_images as $quiz_image){
			$filename = JPATH_SITE . '/images/joomlaquiz/images/'.$quiz_image;
			$pz->add($filename,PCLZIP_OPT_REMOVE_PATH, JPATH_SITE . '/images/joomlaquiz/images/',PCLZIP_OPT_ADD_PATH, 'quiz_images');
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
		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
		@ob_end_clean();
		header('Content-Type: ' . $mime_type);
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		if ($UserBrowser == 'IE') {
			header('Content-Disposition: attachment; filename="exportquiz.zip"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="exportquiz.zip"');
			header('Pragma: no-cache');
		}
		readfile($backup_zip);
		die();
	}
	
	function extractBackupArchive($archivename , $extractdir) {
		$base_Dir = JPATH_SITE . '/tmp/';
		
		if (preg_match( '/.zip$/i', $archivename )) {
			// Extract functions
			if (file_exists(JPATH_BASE.'/components/com_joomlaquiz/assets/pcl/pclzip.lib.php')) {
				require_once(JPATH_BASE.'/components/com_joomlaquiz/assets/pcl/pclzip.lib.php' );
				require_once(JPATH_BASE.'/components/com_joomlaquiz/assets/pcl/pclerror.lib.php' );
			} 
			$backupfile = new PclZip( $archivename );
			$ret = $backupfile->extract( PCLZIP_OPT_PATH, $extractdir );	
		}
		return true;
	}
	
	function uploadFile( $filename, $userfile_name, &$msg ) {
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$baseDir = JPATH_SITE . '/tmp/';
		if (file_exists( $baseDir )) {
			if (is_writable( $baseDir )) {
				if (JFile::move( $filename, $baseDir . $userfile_name )) {
					jimport('joomla.filesystem.path');
					if (JPath::setPermissions( $baseDir . $userfile_name )) {
						return true;
					} else {
						$msg = JText::_('COM_JOOMLAQUIZ_FAILED_TO_CHANGE');
					}
				} else {
					if(move_uploaded_file($filename, $baseDir . $userfile_name)){
						jimport('joomla.filesystem.path');
						if (JPath::setPermissions( $baseDir . $userfile_name )) {
							return true;
						} else {
							$msg = JText::_('COM_JOOMLAQUIZ_FAILED_TO_CHANGE');
						}
					} else {
						$msg = JText::_('COM_JOOMLAQUIZ_FAILED_TO_MOVE');
					}
				}
			} else {
				$msg = JText::_('COM_JOOMLAQUIZ_DIRECTORY_IS_NOT_WRITE');
			}
		} else {
			$msg = JText::_('COM_JOOMLAQUIZ_DIRECTIRY_IS_NOT_EXISTS');
		}
		return false;
	}
	
	function createCategory($extension, $title, $desc, $parent_id=1, $note='', $published=1, $access = 1, $params = '{"target":"","image":""}', $metadata = '{"page_title":"","author":"","robots":""}', $language = '*'){	
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
		   JTable::addIncludePath(JPATH_PLATFORM . 'joomla/database/table');
		}

		// Initialize a new category
		$category = JTable::getInstance('Category');
		$category->extension = $extension;
		$category->title = $title;
		$category->description = $desc;
		$category->note = $note;
		$category->published = $published;
		$category->access = $access;
		$category->params = $params;
		$category->metadata = $metadata;
		$category->language = $language;

		$category->setLocation($parent_id, 'last-child');
		if (!$category->check())
		{
		   JError::raiseNotice(500, $category->getError());
		   return false;
		}
		if (!$category->store(true))
		{
		   JError::raiseNotice(500, $category->getError());
		   return false;
		}
		
		$category->rebuildPath($category->id);
		
		return $category;
	}
	
	public function import_quizzes(){
		$database = JFactory::getDBO();
		
		$quiz_images = array();
		require_once(JPATH_BASE."/components/com_joomlaquiz/assets/pcl/pclzip.lib.php");
		if(!extension_loaded('zlib')) {
			JError::raiseError(100, JText::_('COM_JOOMLAQUIZ_ZLIB_LIBRARY'));
			return false;
		}
		
		$backupfile = JRequest::getVar('importme', '', 'files', 'array');
		
		if (!$backupfile) {
			JError::raiseError(100, JText::_('COM_JOOMLAQUIZ_SELECT_FILE'));
			return false;
		}
		$backupfile_name = $backupfile['name'];
		$filename = explode(".", $backupfile_name);
		if (empty($backupfile_name)) {
			JError::raiseError(100, JText::_('COM_JOOMLAQUIZ_SELECT_FILE'));
			return false;
		}
			
		if (strcmp($this->jq_substr($backupfile_name,-4,1),".")) {
			JError::raiseError(100, JText::_('COM_JOOMLAQUIZ_BAD_FILEEXT'));
			return false;
		}
		if (strcmp($this->jq_substr($backupfile_name,-4),".zip")) {
			JError::raiseError(100, JText::_('COM_JOOMLAQUIZ_BAD_FILEEXT'));
			return false;
		}
		$tmp_name = $backupfile['tmp_name'];
		if (!file_exists($tmp_name)) {
			JError::raiseError(100, JText::_('COM_JOOMLAQUIZ_SIZE_ERROR'));
			return false;
		}
		if (preg_match("/.zip$/", strtolower($backupfile_name))) {
				
			$zipFile = new pclZip($tmp_name);
			$zipContentArray = $zipFile->listContent();
			$exp_xml_file = false;
			foreach($zipContentArray as $thisContent) {
				if ( preg_match('~.(php.*|phtml)$~i', $thisContent['filename']) ) {
					JError::raiseError(100, JText::_('COM_JOOMLAQUIZ_READ_PACKAGE_ERROR'));
					return false;
				}
				if ($thisContent['filename'] == 'export.xml'){
					$exp_xml_file = true;
				}
			}
			if ($exp_xml_file == false){
				JError::raiseError(100, JText::_('COM_JOOMLAQUIZ_NOT_FIND_COURSE'));
				return false;
			}
		} else {
			JError::raiseError(100, JText::_('COM_JOOMLAQUIZ_BAD_FILEEXT'));
			return false;
		}
		$msg = '';

		//copy upload file to /tmp
		$this->uploadFile( $backupfile['tmp_name'], $backupfile['name'], $msg );
		
		$extract_dir = JPATH_SITE."/tmp/course_backup_".uniqid(rand(), true)."/";	
		$archive = JPATH_SITE."/tmp/".$backupfile['name'];
		//exstract archive in uniqfolder tmp
		$this->extractBackupArchive( $archive, $extract_dir);

// BEGIN IMPORT
		require_once(JPATH_BASE . '/components/com_joomlaquiz/assets/qxmlimport.php' );
		$xmlReader = new qXMLImport( $extract_dir . "export.xml" );

		$quiz_cat = $xmlReader->quiz_categories();
		
		$db = JFactory::getDbo();
		$categories_relations_quiz = array();
		if(count($quiz_cat))
			foreach($quiz_cat as &$qcat)
			{
				$query = $db->getQuery(true);
				$query->select('`id`')
					->from('`#__categories`')
					->where('`extension` = "com_joomlaquiz"')
					->where('`title` = "'.$qcat->c_category.'"')
					->where('`published` = 1 OR `published` = 0');
				$qcat->new_id = $db->setQuery($query)->loadResult();
			
				if(!$qcat->new_id){				
					$extension = 'com_joomlaquiz';
					$title     = $qcat->c_category;
					$desc      = $qcat->c_instruction;
					$parent_id = 1;
					$new_category = $this->createCategory($extension, $title, $desc, $parent_id, 'import '.$qcat->c_id);
					$qcat->new_id = $new_category->id;
				}
				
				$categories_relations_quiz[$qcat->c_id] = $qcat->new_id;
			}

		$quest_cat = $xmlReader->quest_categories();
		
		$categories_relations_questions = array();
		if(count($quest_cat))
			foreach($quest_cat as $qcat)
			{
				$query = $db->getQuery(true);
				$query->select('`id`')
					->from('`#__categories`')
					->where('`extension` = "com_joomlaquiz.questions"')
					->where('`title` = "'.$qcat->c_category.'"')
					->where('`published` = 1 OR `published` = 0');
				$qcat->new_id = $db->setQuery($query)->loadResult();

				if(!$qcat->new_id){				
					$extension = 'com_joomlaquiz.questions';
					$title     = ($qcat->qc_category)?$qcat->qc_category:$qcat->c_category;
					$desc      = $qcat->instruction;
					$parent_id = 1;
					$new_category = $this->createCategory($extension, $title, $desc, $parent_id, 'import '.$qcat->c_id);
					$qcat->new_id = $new_category->id;
				}
				
				$categories_relations_questions[$qcat->c_id] = $qcat->new_id;
				
			}
		
		$certificates = $xmlReader->certificates();

		if(count($certificates))
			foreach($certificates as $qcat)
			{
				$query = "SELECT * FROM #__quiz_certificates WHERE id=".$qcat->id;
				$database->setQuery($query);
				$dubl_row = $database->LoadObjectList();
				if(count($dubl_row))
				{
					if($dubl_row[0]->cert_name != $qcat->cert_name || $dubl_row[0]->cert_file != $qcat->cert_file)
					{
						$query = "INSERT INTO #__quiz_certificates VALUES('',".$db->quote($qcat->cert_name).",".$db->quote($qcat->cert_file).",".$db->quote($qcat->crtf_align).",".$db->quote($qcat->crtf_shadow).",".$db->quote($qcat->text_x).",".$db->quote($qcat->text_y).",".$db->quote($qcat->text_size).", ".$db->quote($qcat->crtf_text)." ,".$db->quote($qcat->text_font)." ,".$db->quote($qcat->cert_offset).")";
						$database->setQuery($query);
						$database->execute();
						if($qcat->cert_file) $quiz_images[] = $qcat->cert_file;
					}
				}
				else
				{
					$query = "INSERT INTO #__quiz_certificates VALUES(".$db->quote($qcat->id).",".$db->quote($qcat->cert_name).",".$db->quote($qcat->cert_file).",".$db->quote($qcat->crtf_align).",".$db->quote($qcat->crtf_shadow).",".$db->quote($qcat->text_x).",".$db->quote($qcat->text_y).",".$db->quote($qcat->text_size).",".$db->quote($qcat->crtf_text).",".$db->quote($qcat->text_font)." ,".$db->quote($qcat->cert_offset).")";
					$database->setQuery($query);
					$database->execute();
					if($qcat->cert_file) $quiz_images[] = $qcat->cert_file;
				}
				
			}

			if ( $xmlReader->isDomit )
				$quizzes = $xmlReader->quizess();
			else
				$quizzes = 1;

			$quizis_titles = array();

			if( !empty($quizzes))
				while ( !empty($quizzes) )
				{
					if ( !$xmlReader->isDomit )
					{
						$qcat = $xmlReader->quizess_get_one();
						if ( empty($qcat) )
							break;
					}
					else
					{
						$qcat = array_shift($quizzes);
					}
					$quizis_titles[] = $qcat->quiz_title;
					$query = "SELECT * FROM #__quiz_t_quiz WHERE c_id=".$qcat->id;
					$database->setQuery($query);
					$dubl_row = $database->LoadObjectList();
					
					if(count($dubl_row))
					{
						if($dubl_row[0]->c_title != $qcat->quiz_title || $dubl_row[0]->c_created_time != $qcat->quiz_createtime)
						{
							$query = "INSERT INTO #__quiz_t_quiz(
								c_id, c_category_id, c_number_times, 
								c_user_id, c_author, c_full_score, 
								c_title, c_description, c_short_description, c_image, 
								c_time_limit, c_min_after, c_once_per_day, 
								c_passing_score, c_created_time, c_published, 
								c_right_message, c_wrong_message, c_pass_message, 
								c_unpass_message, c_enable_review, c_email_to, 
								c_enable_print, c_enable_sertif, c_skin, 
								c_random, c_guest, published, 
								c_slide, c_language, c_certificate, 
								c_feedback, c_pool, c_auto_breaks,
								c_resbycat,	c_feed_option, paid_check, c_pagination)  ";
							$query .= "VALUES(
								'',".$db->quote($categories_relations_quiz[$qcat->quiz_category]).",".$db->quote($qcat->quiz_number_times).",
								".$db->quote($qcat->quiz_userid).",".$db->quote($qcat->quiz_author).",".$db->quote($qcat->quiz_full_score).",
								".$db->quote($qcat->quiz_title).",".$db->quote($qcat->quiz_description).",".$db->quote($qcat->quiz_short_description).",".$db->quote($qcat->quiz_image).",
								".$db->quote($qcat->quiz_timelimit).",".$db->quote($qcat->quiz_minafter).", ".$db->quote($qcat->quiz_onceperday).",
								".$db->quote($qcat->quiz_passcore).",".$db->quote($qcat->quiz_createtime).",".$db->quote($qcat->published).",
								".$db->quote($qcat->quiz_rmess).",".$db->quote($qcat->quiz_wmess).",".$db->quote($qcat->quiz_pass_message).",
								".$db->quote($qcat->quiz_unpass_message).", ".$db->quote(@$qcat->quiz_enable_review).", ".$db->quote($qcat->quiz_email_to).",
								".$db->quote($qcat->quiz_enable_print).",".$db->quote($qcat->quiz_enable_sertif).",".$db->quote($qcat->quiz_skin).",
								".$db->quote($qcat->quiz_random).",".$db->quote($qcat->quiz_guest).",".$db->quote($qcat->quiz_published).",
								".$db->quote($qcat->quiz_slide).",".$db->quote($qcat->quiz_language).",".$db->quote($qcat->quiz_certificate).",
								".$db->quote($qcat->quiz_feedback).",".$db->quote($qcat->quiz_pool).",".$db->quote(@$qcat->quiz_auto_breaks).",".$db->quote(@$qcat->quiz_resbycat).",
								".$db->quote($qcat->quiz_feed_option).", ".$db->quote($qcat->quiz_paid_check).", ".$db->quote($qcat->quiz_pagination).")";
								$database->setQuery($query);
							if(!$database->execute()){
								echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
								exit();
							}
							if($qcat->quiz_image) $quiz_images[] = $qcat->quiz_image;
							$query = "SELECT max(c_id) FROM #__quiz_t_quiz";
							$database->setQuery($query);
							$new_quiz_id = $database->loadResult();
							if(count(@$qcat->quiz_questions))
							{
								foreach($qcat->quiz_questions as $q_quest)
								{
									$query = "SELECT * FROM #__quiz_t_question WHERE c_id=".$q_quest->id;
									$database->setQuery($query);
									$dubl_rowq = $database->LoadObjectList();
									if(count($dubl_rowq))
									{
										$query = "INSERT INTO #__quiz_t_question(c_id,c_quiz_id,c_point,c_attempts,c_question,c_image,c_type,ordering,c_right_message,c_wrong_message,c_feedback,cq_id,c_ques_cat,c_random,c_qform) ";
										$query .= " VALUES ('',".$db->quote($new_quiz_id).",".$db->quote($q_quest->c_point).",".$db->quote($q_quest->c_attempts).",".$db->quote($q_quest->question_text).",".$db->quote($q_quest->question_image).",".$db->quote($q_quest->c_type).",".$db->quote($q_quest->ordering).",".$db->quote($q_quest->question_rmess).",".$db->quote($q_quest->question_wmess).",".$db->quote($q_quest->c_feedback).",".$db->quote($q_quest->cq_id).",".$db->quote($categories_relations_questions[$q_quest->c_ques_cat]).",".$db->quote($q_quest->c_random).",".$db->quote($q_quest->c_qform).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
											exit();
										}
										if($q_quest->question_image) $quiz_images[] = $q_quest->question_image;
										$new_quest_id = $database->insertid();
									}
									else
									{
										$query = "INSERT INTO #__quiz_t_question(c_id,c_quiz_id,c_point,c_attempts,c_question,c_image,c_type,ordering,c_right_message,c_wrong_message,c_feedback,cq_id,c_ques_cat,c_random,c_qform) ";
										$query .= " VALUES (".$db->quote($q_quest->id).",".$db->quote($new_quiz_id).",".$db->quote($q_quest->c_point).",".$db->quote($q_quest->c_attempts).",".$db->quote($q_quest->question_text).",".$db->quote($q_quest->question_image).",".$db->quote($q_quest->c_type).",".$db->quote($q_quest->ordering).",".$db->quote($q_quest->question_rmess).",".$db->quote($q_quest->question_wmess).",".$db->quote($q_quest->c_feedback).",".$db->quote($q_quest->cq_id).",".$db->quote($categories_relations_questions[$q_quest->c_ques_cat]).",".$db->quote($q_quest->c_random).",".$db->quote($q_quest->c_qform).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
											exit();
										}
										if($q_quest->question_image) $quiz_images[] = $q_quest->question_image;
										$new_quest_id = $q_quest->id;
									}
										if(count(@$qcat->choice_data))
										{
											foreach($qcat->choice_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{
													$query = "INSERT INTO #__quiz_t_choice(c_id, c_choice, c_right, c_question_id, ordering, c_incorrect_feed, a_point) ";
													$query .= " VALUES('',".$db->quote($ch_data->choice_text).",".$db->quote($ch_data->c_right).",".$db->quote($new_quest_id).",".$db->quote($ch_data->ordering).",".$db->quote($ch_data->choice_feed).", ".$db->quote($ch_data->choice_point).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
												}
											}
										}
										if(count(@$qcat->match_data))
										{
											foreach($qcat->match_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{
													$query = "INSERT INTO #__quiz_t_matching(c_id,c_question_id,c_left_text,c_right_text,ordering,a_points) ";
													$query .= " VALUES('',".$db->quote($new_quest_id).",".$db->quote($ch_data->match_text_left).",".$db->quote($ch_data->match_text_right).",".$db->quote($ch_data->ordering).", ".$db->quote($ch_data->match_points).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
												}
											}
										}
										if(count(@$qcat->blank_data))
										{
											$c_blank_id = 0;
											$new_blank_id = 0;
											foreach($qcat->blank_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{
													if ($c_blank_id != $ch_data->c_blank_id) {
														$c_blank_id = $ch_data->c_blank_id;
														$query = "INSERT INTO #__quiz_t_blank(c_id, c_question_id, points, css_class) ";
														$query .= " VALUES('',".$db->quote($new_quest_id).", ".$db->quote($ch_data->points).", ".$db->quote($ch_data->css_class).")";
														$database->setQuery($query);
														if(!$database->execute())
														{
															echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
															exit();
														}
														$query = "SELECT max(c_id) FROM #__quiz_t_blank";
														$database->setQuery($query);
														$new_blank_id = $database->loadResult();
													}
													if ($new_blank_id) {
														$query = "INSERT INTO #__quiz_t_text(c_id,c_blank_id,c_text,ordering) ";
														$query .= " VALUES('',".$db->quote($new_blank_id).",".$db->quote($ch_data->blank_text).",".$db->quote($ch_data->ordering).")";
														$database->setQuery($query);
														if(!$database->execute())
														{
															echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
															exit();
														}
													}
												}
											}
										}

										if(count(@$qcat->blank_distr_data))
										{
											foreach($qcat->blank_distr_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{
													$query = "INSERT INTO #__quiz_t_faketext(c_id, c_quest_id, c_text, c_blank_id) ";
													$query .= " VALUES('',".$db->quote($new_quest_id).",".$db->quote($ch_data->distr_text).",".$db->quote($ch_data->distr_blank_id).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
												}
											}
										}

										if(count(@$qcat->hotspot_data))
										{
											foreach($qcat->hotspot_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{

													$query = "INSERT INTO #__quiz_t_hotspot (c_id, c_question_id, c_start_x, c_start_y, c_width, c_height) ";
													$query .= " VALUES ('', ".$db->quote($new_quest_id).", ".$db->quote($ch_data->hs_start_x).", ".$db->quote($ch_data->hs_start_y).", ".$db->quote($ch_data->hs_width).", ".$db->quote($ch_data->hs_height).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
												}
											}
										}
								}

							}
						}

					}
					else
					{
							$query = "INSERT INTO #__quiz_t_quiz(
								c_id, c_category_id, c_number_times,
								c_user_id, c_author, c_full_score,
								c_title, c_description, c_short_description, c_image,
								c_time_limit, c_min_after, c_once_per_day, 
								c_passing_score, c_created_time, c_published, 
								c_right_message, c_wrong_message, c_pass_message, 
								c_unpass_message, c_enable_review, c_email_to, 
								c_enable_print, c_enable_sertif, c_skin, 
								c_random, c_guest, published, 
								c_slide, c_language, c_certificate, 
								c_feedback, c_pool, c_auto_breaks,
								c_resbycat, c_feed_option, paid_check, c_pagination)  ";
							$query .= "VALUES(
								".$db->quote($qcat->id).",".$db->quote($categories_relations_quiz[$qcat->quiz_category]).",".$db->quote($qcat->quiz_number_times).",
								".$db->quote($qcat->quiz_userid).",".$db->quote($qcat->quiz_author).",".$db->quote($qcat->quiz_full_score).",
								".$db->quote($qcat->quiz_title).",".$db->quote($qcat->quiz_description).",".$db->quote($qcat->quiz_short_description).",".$db->quote($qcat->quiz_image).",
								".$db->quote($qcat->quiz_timelimit).",".$db->quote($qcat->quiz_minafter).", ".$db->quote($qcat->quiz_onceperday).",
								".$db->quote($qcat->quiz_passcore).",".$db->quote($qcat->quiz_createtime).",".$db->quote($qcat->published).",
								".$db->quote($qcat->quiz_rmess).",".$db->quote($qcat->quiz_wmess).",".$db->quote($qcat->quiz_pass_message).",
								".$db->quote($qcat->quiz_unpass_message).", ".$db->quote(@$qcat->quiz_enable_review).",".$db->quote($qcat->quiz_email_to).",
								".$db->quote($qcat->quiz_enable_print).",".$db->quote($qcat->quiz_enable_sertif).",".$db->quote($qcat->quiz_skin).",
								".$db->quote($qcat->quiz_random).",".$db->quote($qcat->quiz_guest).",".$db->quote($qcat->quiz_published).",
								".$db->quote($qcat->quiz_slide).",".$db->quote($qcat->quiz_language).",".$db->quote($qcat->quiz_certificate).",
								".$db->quote($qcat->quiz_feedback).",".$db->quote($qcat->quiz_pool).",".$db->quote(@$qcat->quiz_auto_breaks).",".$db->quote($qcat->quiz_resbycat).",
								".$db->quote($qcat->quiz_feed_option).", ".$db->quote($qcat->quiz_paid_check).", ".$db->quote($qcat->quiz_pagination).")";
							$database->setQuery($query);
							if(!$database->execute())
							{
								echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
								exit();
							}
							if($qcat->quiz_image) $quiz_images[] = $qcat->quiz_image;
							$new_quiz_id = $qcat->id;

							if(count(@$qcat->quiz_questions))
							{
								foreach($qcat->quiz_questions as $q_quest)
								{
									$query = "SELECT * FROM #__quiz_t_question WHERE c_id=".$q_quest->id;
									$database->setQuery($query);
									$dubl_rowq = $database->LoadObjectList();
									if(count($dubl_rowq))
									{
										$query = "INSERT INTO #__quiz_t_question(c_id,c_quiz_id,c_point,c_attempts,c_question,c_image,c_type,ordering,c_right_message,c_wrong_message,c_feedback,cq_id,c_ques_cat,c_random, c_qform) ";
										$query .= " VALUES ('',".$db->quote($new_quiz_id).",".$db->quote($q_quest->c_point).",".$db->quote($q_quest->c_attempts).",".$db->quote($q_quest->question_text).",".$db->quote($q_quest->question_image).",".$db->quote($q_quest->c_type).",".$db->quote($q_quest->ordering).",".$db->quote($q_quest->question_rmess).",".$db->quote($q_quest->question_wmess).",".$db->quote($q_quest->c_feedback).",".$db->quote($q_quest->cq_id).",".$db->quote($categories_relations_questions[$q_quest->c_ques_cat]).",".$db->quote($q_quest->c_random).",".$db->quote($q_quest->c_qform).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
											exit();
										}

										if ($q_quest->question_pbeaks) {
											/*Input pbreaks for questions*/
											$lastId = $database->insertid();
											$query = "INSERT INTO `#__quiz_t_pbreaks` (`c_id`, `c_quiz_id`, `c_question_id`) VALUES ('', " . $db->quote($new_quiz_id) . ", " . $db->quote($lastId) . ")";
											$database->setQuery($query);
											if (!$database->execute()) {
												echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
												exit();
											}
										}

										if($q_quest->question_image) $quiz_images[] = $q_quest->question_image;
										$new_quest_id = $database->insertid();
									}
									else
									{
										$query = "INSERT INTO #__quiz_t_question(c_id,c_quiz_id,c_point,c_attempts,c_question,c_image,c_type,ordering,c_right_message,c_wrong_message,c_feedback,cq_id,c_ques_cat,c_random, c_qform) ";
										$query .= " VALUES (".$db->quote($q_quest->id).",".$db->quote($new_quiz_id).",".$db->quote($q_quest->c_point).",".$db->quote($q_quest->c_attempts).",".$db->quote($q_quest->question_text).",".$db->quote($q_quest->question_image).",".$db->quote($q_quest->c_type).",".$db->quote($q_quest->ordering).",".$db->quote($q_quest->question_rmess).",".$db->quote($q_quest->question_wmess).",".$db->quote($q_quest->c_feedback).",".$db->quote($q_quest->cq_id).",".$db->quote($categories_relations_questions[$q_quest->c_ques_cat]).",".$db->quote($q_quest->c_random).",".$db->quote($q_quest->c_qform).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
											exit();
										}

										if ($q_quest->question_pbeaks) {
											/*Input pbreaks for questions*/
											$query = "INSERT INTO `#__quiz_t_pbreaks` (`c_id`, `c_quiz_id`, `c_question_id`) VALUES ('', " . $db->quote($new_quiz_id) . ", " . $db->quote($q_quest->id) . ")";
											$database->setQuery($query);
											if (!$database->execute()) {
												echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
												exit();
											}
										}

										if($q_quest->question_image) $quiz_images[] = $q_quest->question_image;
										$new_quest_id = $q_quest->id;
									}
										if(count($qcat->choice_data))
										{
											foreach($qcat->choice_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{
													$query = "INSERT INTO #__quiz_t_choice(c_id,c_choice,c_right,c_question_id,ordering,c_incorrect_feed, a_point) ";
													$query .= " VALUES('',".$db->quote($ch_data->choice_text).",".$db->quote($ch_data->c_right).",".$db->quote($new_quest_id).",".$db->quote($ch_data->ordering).",".$db->quote($ch_data->choice_feed).", ".$db->quote($ch_data->choice_point).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
												}
											}
										}
										if(count($qcat->match_data))
										{
											foreach($qcat->match_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{
													$query = "INSERT INTO #__quiz_t_matching(c_id,c_question_id,c_left_text,c_right_text,ordering,a_points) ";
													$query .= " VALUES('',".$db->quote($new_quest_id).",".$db->quote($ch_data->match_text_left).",".$db->quote($ch_data->match_text_right).",".$db->quote($ch_data->ordering).",".$db->quote($ch_data->match_points).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
												}
											}
										}
										if(count($qcat->blank_data))
										{
											foreach($qcat->blank_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{
													$query = "INSERT INTO #__quiz_t_blank(c_id,c_question_id) ";
													$query .= " VALUES('',".$db->quote($new_quest_id).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script>alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
													$query = "SELECT max(c_id) FROM #__quiz_t_blank";
													$database->setQuery($query);
													$new_blank_id = $database->loadResult();
													$query = "INSERT INTO #__quiz_t_text(c_id,c_blank_id,c_text,ordering) ";
													$query .= " VALUES('',".$db->quote($new_blank_id).",".$db->quote($ch_data->blank_text).",".$db->quote($ch_data->ordering).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script>alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
												}
											}
										}
										if(count($qcat->blank_distr_data))
										{
											foreach($qcat->blank_distr_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{
													$query = "INSERT INTO #__quiz_t_faketext(c_id, c_quest_id, c_text, c_blank_id)";
													$query .= " VALUES('',".$db->quote($new_quest_id).",".$db->quote($ch_data->distr_text).",".$db->quote($ch_data->distr_blank_id).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
												}
											}
										}
										if(count($qcat->hotspot_data))
										{
											foreach($qcat->hotspot_data as $ch_data)
											{
												if($ch_data->c_question_id == $q_quest->id)
												{
													$query = "INSERT INTO #__quiz_t_hotspot(c_id, c_question_id, c_start_x, c_start_y, c_width, c_height) ";
													$query .= " VALUES ('', ".$db->quote($new_quest_id).", ".$db->quote($ch_data->hs_start_x).", ".$db->quote($ch_data->hs_start_y).", ".$db->quote($ch_data->hs_width).", ".$db->quote($ch_data->hs_height).")";
													$database->setQuery($query);
													if(!$database->execute())
													{
														echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
														exit();
													}
												}
											}

										}
								}
							}
					}
				}

			if( !empty($_POST['jform']['imp_pool']))
			{
				$quizzes_poolk = $xmlReader->quizess_pool();

				if(count($quizzes_poolk))
				foreach($quizzes_poolk as $qcat)
				{
					$qcat->id = 0;

					$new_quiz_id = 0;

					if(count($qcat->quizzes_question_pool))
					{
						foreach($qcat->quizzes_question_pool as $q_quest)
						{
							$query = "SELECT * FROM #__quiz_t_question WHERE c_id=".$db->quote($q_quest->id)."";
							$database->setQuery($query);
							$dubl_rowq = $database->LoadObjectList();

							if(count($dubl_rowq))
							{
								if($dubl_rowq[0]->c_question != $q_quest->question_text)
								{
									$query = "INSERT INTO #__quiz_t_question(c_id,c_quiz_id,c_point,c_attempts,c_question,c_image,c_type,ordering,c_right_message,c_wrong_message,c_feedback,cq_id,c_ques_cat,c_random,c_qform) ";
									$query .= " VALUES ('',".$db->quote($new_quiz_id).",".$db->quote($q_quest->c_point).",".$db->quote($q_quest->c_attempts).",".$db->quote($q_quest->question_text).",".$db->quote($q_quest->question_image).",".$db->quote($q_quest->c_type).",".$db->quote($q_quest->ordering).",".$db->quote($q_quest->question_rmess).",".$db->quote($q_quest->question_wmess).",".$db->quote($q_quest->c_feedback).",".$db->quote($q_quest->cq_id).",".$db->quote($categories_relations_questions[$q_quest->c_ques_cat]).",".$db->quote($q_quest->c_random).",".$db->quote($q_quest->c_qform).")";
									$database->setQuery($query);
									if(!$database->execute())
									{
										echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
										exit();
									}
									if($qcat->c_image) $quiz_images[] = $qcat->c_image;
									$new_quest_id = $database->insertid();
								}
								else
									$new_quest_id = $dubl_rowq[0]->c_id;
							}
							else
							{
								$query = "INSERT INTO #__quiz_t_question(c_id,c_quiz_id,c_point,c_attempts,c_question,c_image,c_type,ordering,c_right_message,c_wrong_message,c_feedback,cq_id,c_ques_cat,c_random,c_qform) ";
								$query .= " VALUES (".$db->quote($q_quest->id).",".$db->quote($new_quiz_id).",".$db->quote($q_quest->c_point).",".$db->quote($q_quest->c_attempts).",".$db->quote($q_quest->question_text).",".$db->quote($q_quest->question_image).",".$db->quote($q_quest->c_type).",".$db->quote($q_quest->ordering).",".$db->quote($q_quest->question_rmess).",".$db->quote($q_quest->question_wmess).",".$db->quote($q_quest->c_feedback).",".$db->quote($q_quest->cq_id).",".$db->quote($categories_relations_questions[$q_quest->c_ques_cat]).",".$db->quote($q_quest->c_random).",".$db->quote(@$q_quest->c_qform).")";
								$database->setQuery($query);
								if(!$database->execute())
								{
									echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
									exit();
								}
								if(@$qcat->c_image) $quiz_images[] = $qcat->c_image;
								$new_quest_id = $q_quest->id;

							}

							if(count($qcat->choice_data))
							{
								foreach($qcat->choice_data as $ch_data)
								{
									if($ch_data->c_question_id == $q_quest->id)
									{
										$query = "INSERT INTO #__quiz_t_choice(c_id,c_choice,c_right,c_question_id,ordering,c_incorrect_feed,a_point) ";
										$query .= " VALUES('',".$db->quote($ch_data->choice_text).",".$db->quote($ch_data->c_right).",".$db->quote($new_quest_id).",".$db->quote($ch_data->ordering).",".$db->quote($ch_data->choice_feed).",".$db->quote(@$ch_data->choice_point).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
											exit();
										}
									}
								}
							}
							if(count($qcat->match_data))
							{
								foreach($qcat->match_data as $ch_data)
								{
									if($ch_data->c_question_id == $q_quest->id)
									{
										$query = "INSERT INTO #__quiz_t_matching(c_id,c_question_id,c_left_text,c_right_text,ordering,a_points) ";
										$query .= " VALUES('',".$db->quote($new_quest_id).",".$db->quote($ch_data->match_text_left).",".$db->quote($ch_data->match_text_right).",".$db->quote($ch_data->ordering).",".$db->quote($ch_data->match_points).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
											exit();
										}
									}
								}
							}
							if(count($qcat->blank_data))
							{
								foreach($qcat->blank_data as $ch_data)
								{
									if($ch_data->c_question_id == $q_quest->id)
									{
										$query = "INSERT INTO #__quiz_t_blank(c_id,c_question_id) ";
										$query .= " VALUES('',".$db->quote($new_quest_id).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert(".$db->quote($database->getErrorMsg())."); window.history.go(-1); </script>\n";
											exit();
										}
										$query = "SELECT max(c_id) FROM #__quiz_t_blank";
										$database->setQuery($query);
										$new_blank_id = $database->loadResult();
										$query = "INSERT INTO #__quiz_t_text(c_id,c_blank_id,c_text,ordering) ";
										$query .= " VALUES('',".$db->quote($new_blank_id).",".$db->quote($ch_data->blank_text).",".$db->quote($ch_data->ordering).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
											exit();
										}
									}
								}
							}

							if(count(@$qcat->blank_distr_data))
							{
								foreach($qcat->blank_distr_data as $ch_data)
								{
									if($ch_data->c_question_id == $q_quest->id)
									{
										$query = "INSERT INTO #__quiz_t_faketext(c_id, c_quest_id, c_text, c_blank_id) ";
										$query .= " VALUES('',".$db->quote($new_quest_id).",".$db->quote($ch_data->distr_text).",".$db->quote($ch_data->distr_blank_id).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
											exit();
										}
									}
								}
							}
							if(count($qcat->hotspot_data))
							{
								foreach($qcat->hotspot_data as $ch_data)
								{
									if($ch_data->c_question_id == $q_quest->id)
									{
										$query = "INSERT INTO #__quiz_t_hotspot(c_id,c_question_id,c_start_x,c_start_y,c_width,c_height) ";
										$query .= " VALUES('',".$db->quote($new_quest_id).",".$db->quote($ch_data->hs_start_x).",".$db->quote($ch_data->hs_start_y).",".$db->quote($ch_data->hs_width).",".$db->quote($ch_data->hs_height).")";
										$database->setQuery($query);
										if(!$database->execute())
										{
											echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
											exit();
										}
									}
								}
							}
						}
					}

				}
		
			}
												
						// Copy quiz images
						if (count($quiz_images)) {
							$fromDir = $extract_dir."quiz_images/";
							$toDir   = JPATH_SITE."/images/joomlaquiz/images/";
							$i = 0;
							while( $i < count($quiz_images) ){
								if (file_exists($fromDir.$quiz_images[$i])) {
									if(!JFile::move($fromDir.$quiz_images[$i], $toDir.$quiz_images[$i])){
										move_uploaded_file($fromDir.$quiz_images[$i], $toDir.$quiz_images[$i]);
									}
								}
								$i ++;
							}
						}
						
						// delete temporary files
						//$this->deldir_my($extract_dir);
						$this->delzip(JPATH_SITE.'/tmp/');
						
						$msg2 = '';
						for($i=0; $i<count($quizis_titles);$i++)
						{
							$query = "SELECT COUNT(*) FROM #__quiz_t_quiz WHERE c_title=".$db->quote($quizis_titles[$i])."";
							$database->setQuery($query);
							if($database->loadResult() > 1)
							{
								$msg2 .= " ".$database->loadResult().JText::_('COM_JOOMLAQUIZ_QUIZES_QUIZZES').$quizis_titles[$i].JText::_('COM_JOOMLAQUIZ_AFTER_IMPORT');
							}
						}	
						
		$this->setRedirect( "index.php?option=com_joomlaquiz&view=quizzes", JText::_('COM_JOOMLAQUIZ_QUIZES_SUCCESSFULY_IMPORT').$msg2 );
	}
	
	function delzip($basedir){
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		if(!JFile::delete($basedir . 'exportquiz.zip')){
			@chmod($basedir . 'exportquiz.zip', 0777);
			@unlink( $basedir . 'exportquiz.zip' );
		}
		
		return true;
	}
	
	function deldir_my( $dir ) {
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$current_dir = opendir( $dir );
		$old_umask = umask(0);
		while ($entryname = readdir( $current_dir )) {
			if ($entryname != '.' and $entryname != '..') {
				if (is_dir( $dir . $entryname )) {
					$this->deldir_my( $dir . $entryname );
				} else {
					if(!JFile::delete($dir . $entryname)){
						@chmod($dir . $entryname, 0777);
						unlink( $dir . $entryname );
					}
				}
			}
		}
		umask($old_umask);
		closedir( $current_dir );
		if(!JFolder::delete($dir)){
			return rmdir( $dir );
		}
		
		return true;
	} 
	
	function jq_substr($str, $start, $length=null) {
		if (function_exists('mb_substr')) {
			if ($length!==null)
				return mb_substr($str, $start, $length);
			else
				return mb_substr($str, $start);
		} else {
			if ($length!==null)
				return substr($str, $start, $length);
			else
				return substr($str, $start);
		}
	}
}


/*  following 2 functions don't need at all
	function migrateCategories(){
	
		$this->defaultCategoryCheck();
		
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__quiz_t_category');
		$quiz_categories = $db->setQuery($query)->loadObjectList('c_id');
		
		$error = false;
		foreach($quiz_categories as $key => $qzc){
			$extension = 'com_joomlaquiz';
			$title     = $qzc->c_category;
			$desc      = $qzc->instruction;
			$parent_id = 1;
			$quiz_categories[$key] = $this->createCategory($extension, $title, $desc, $parent_id, $qzc->c_id);
			if(!$quiz_categories[$key]->id){
				$error = true;
			}else{
				$query->clear();
				$query->update('#__quiz_t_quiz')
					->set('`c_category_id` = "'.$quiz_categories[$key]->id.'"')
					->where('`c_category_id` = "'.$quiz_categories[$key]->note.'"');
				$db->setQuery($query)->execute();
			}
		}
		if(!$error){
			$query->clear();
			$query->delete('#__quiz_t_category');
			$db->setQuery($query)->execute();
		}
		
		/* create pseudo-tree *//*
		$query = $db->getQuery(true);
		$query->select('DISTINCT(qc_tag) AS value, qc_tag')
			->from('#__quiz_q_cat')
			->where('TRIM(qc_tag) <> \'\'');
		$head_categories = $db->setQuery($query)->loadObjectList('qc_tag');
		
		foreach($head_categories as $key => $hqc){
			$extension = 'com_joomlaquiz.questions';
			$title     = $hqc->qc_tag;
			$desc      = '';
			$parent_id = 1;
			$head_categories[$key] = $this->createCategory($extension, $title, $desc, $parent_id, $hqc->c_id);
		}
		/* pseudo-tree done */
	/*	
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__quiz_q_cat');
		$quest_categories = $db->setQuery($query)->loadObjectList('qc_id');
		
		$error = false;
		foreach($quest_categories as $key => $quc){
			$extension = 'com_joomlaquiz.questions';
			$title     = $quc->qc_category;
			$desc      = $quc->instruction;
			$parent_id = $head_categories[$quc->qc_tag]->id;
			$quest_categories[$key] = $this->createCategory($extension, $title, $desc, $parent_id, $quc->c_id);
			if(!$quest_categories[$key]->id){
				$error = true;
			}else{
				$query->clear();
				$query->update('#__quiz_t_question')
					->set('`c_ques_cat` = "'.$quiz_categories[$key]->id.'"')
					->where('`c_ques_cat` = "'.$quiz_categories[$key]->note.'"');
				$db->setQuery($query)->execute();
			}
		}
		if(!$error){
			$query->clear();
			$query->delete('#__quiz_q_cat');
			$db->setQuery($query)->execute();
		}
		
	}
	
	function defaultCategoryCheck()
	{
		/* checking default category quizzes *//*
		$extension = 'com_joomlaquiz';
		$title     = 'Uncategorised';
		$desc      = 'A default category for the joomlaquiz quizzes.';
		$parent_id = 1;
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__categories')
			->where('`extension`="'.$extension.'"')
			->where('`parent_id`="'.$parent_id.'"');
		$exists = count($db->setQuery($query)->loadObjectList());
		
		if(!$exists)
			$this->createCategory($extension, $title, $desc, $parent_id);
		
		/* checking default category questions *//*
		$extension = 'com_joomlaquiz.questions';
		$title     = 'Uncategorised';
		$desc      = 'A default category for the joomlaquiz questions.';
		$parent_id = 1;
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__categories')
			->where('`extension`="'.$extension.'"')
			->where('`parent_id`="'.$parent_id.'"');
		$exists = count($db->setQuery($query)->loadObjectList());
		
		if(!$exists)
			$this->createCategory($extension, $title, $desc, $parent_id);
	}
	*/
	

