<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

require_once( JPATH_ROOT .'/components/com_joomlaquiz/libraries/apps.php' ); 

/**
 * Print Result(PDF) Model.
 *
 */
class JoomlaquizModelPrintresult extends JModelList
{
	public function JQ_PrintResult(){
		
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		
		$stu_quiz_id = intval( JFactory::getApplication()->input->get('stu_quiz_id', 0 ) );
		$user_unique_id = JFactory::getApplication()->input->get( 'user_unique_id', '', 'STRING');
		$unique_pass_id = JFactory::getApplication()->input->get( 'unique_pass_id', '', 'STRING');
		
		$query = "SELECT c_quiz_id, c_student_id, unique_id, unique_pass_id FROM #__quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
		$database->SetQuery($query);
		$st_quiz_data = $database->LoadObjectList();
		
		if (count($st_quiz_data)) {
			$st_quiz_data = $st_quiz_data[0];
			if ( (($user_unique_id == $st_quiz_data->unique_id) && ($my->id == $st_quiz_data->c_student_id || $unique_pass_id == $st_quiz_data->unique_pass_id))  ||  $my->authorise('core.manage','com_joomlaquiz')) {			
				$this->JQ_PrintPDF($stu_quiz_id);
				die();
			}
		}
		echo JText::_('COM_QUIZ_MES_NOTAVAIL');
	}
	
	public function JQ_PrintPDF($sid){

		$pdf = $this->generatePDF($sid);

		$data = $pdf->Output('', 'S'); 
		
		@ob_end_clean();
		header("Content-type: application/pdf");
		header("Content-Length: ".strlen(ltrim($data)));
		header("Content-Disposition: attachment; filename=report.pdf");
		echo $data;
		die;
	}
	
	public static function JQ_GetResults($id) {
		
		$appsLib = JqAppPlugins::getInstance();
		$database = JFactory::getDBO();
		
		$query = "SELECT q.c_id c_id, c_question, is_correct, c_point, c_type, c_score"
		. "\n FROM #__quiz_r_student_question AS sq, #__quiz_t_question AS q"
		. "\n WHERE sq.c_id = '".$id."' AND sq.c_question_id = q.c_id AND q.published = 1";
		$database->setQuery( $query );
		$info = $database->LoadAssocList();
		$info = $info[0];
		JoomlaquizHelper::JQ_GetJoomFish($info['c_question'], 'quiz_t_question', 'c_question', $info['c_id']);
		$type_id = $info['c_type'];
		$qid = $info['c_id'];
		
		$type = JoomlaquizHelper::getQuestionType($type_id);
		$data = array();
		$data['quest_type'] = $type;
		$data['id'] = $id;
		$data['qid'] = $qid;
		$data['info'] = $info;
		
		$appsLib->triggerEvent( 'onGetResult' , $data );
		$info = $data['info'];
		
		return $info;
	}	

	public static function JQ_PrintResultForMail($sid) {
		
		$appsLib = JqAppPlugins::getInstance();
		$plugins = $appsLib->loadApplications();
		$database = JFactory::getDBO();
		
		$str = "";
		$query = "SELECT sq.*, q.*, u.*, q.c_id AS quiz_id FROM #__quiz_t_quiz AS q, #__quiz_r_student_quiz AS sq LEFT JOIN #__users AS u ON sq.c_student_id = u.id"
		. "\n WHERE sq.c_id = '".$sid."' AND sq.c_quiz_id = q.c_id";
		$database->SetQuery( $query );
		$info = $database->LoadAssocList();
		$info = $info[0];
		JoomlaquizHelper::JQ_GetJoomFish($info['c_title'], 'quiz_t_quiz', 'c_title', $info['quiz_id']);
		
		$quiz_id = $info['c_quiz_id'];
		$query = "SELECT q_chain FROM #__quiz_q_chain "
				. "\n WHERE s_unique_id = '".$info['unique_id']."'";
		$database->SetQuery($query);
		$qch_ids = $database->LoadResult();
		$qch_ids = str_replace('*',',',$qch_ids);
				
		$total = JoomlaquizHelper::getTotalScore($qch_ids, $quiz_id);
		
		$str .= "\n";
		$str .= JText::_('COM_QUIZ_PDF_QTITLE')." ".$info['c_title']."\n";
		$str .= JText::_('COM_QUIZ_PDF_UNAME')." ".(($info['username'])?($info['username']):JText::_('COM_QUIZ_USERNAME_ANONYMOUS'))."\n";
		$str .= JText::_('COM_QUIZ_PDF_NAME')." ".(($info['name'])?$info['name']:$info['user_name'])."\n";
		$str .= JText::_('COM_QUIZ_PDF_UEMAIL')." ".(($info['email'])?$info['email']:$info['user_email'])."\n";
		$str .= JText::_('COM_QUIZ_PDF_USCORE')." ".number_format($info['c_total_score'],1)."\n";
		$str .= JText::_('COM_QUIZ_PDF_TOTSCORE')." ".number_format($total,1)."\n";
		$str .= JText::_('COM_QUIZ_PDF_PASSCORE')." ".$info['c_passing_score']."\n";
		$tot_min = floor($info['c_total_time'] / 60);
		$tot_sec = $info['c_total_time'] - $tot_min*60;
		$str .= JText::_('COM_QUIZ_PDF_USPENT')." ".str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT)." ".JText::_('COM_QUIZ_PDF_QTIME')." "."\n";
		if ($info['c_passed'] == 1) {
			$str .= $info['name']." ".JText::_('COM_QUIZ_PDF_PASQUIZ')." "."\n";
		}
		else {
			$str .= $info['name']." ".JText::_('COM_QUIZ_PDF_NPASSQUIZ')." "."\n";
		}		
		$str .= " \n";
		
		$query = $database->getQuery(true);
		$query->select('`rq`.`c_id`, `rq`.`remark`')
			->from('`#__quiz_r_student_question` AS `rq`')
			->join('LEFT', '`#__quiz_t_question` AS `tq` ON `rq`.`c_question_id` = `tq`.`c_id`')
			->order('`c_id`');
		if(JComponentHelper::getParams('com_joomlaquiz')->get('hide_boilerplates')){
			$query->where('`tq`.`c_type` != 9');
		}
		$query->where('`rq`.`c_stu_quiz_id` = "'.$sid.'"');
		$database->SetQuery( $query );
		$info = $database->LoadObjectList();
		$total = count($info);
		
		for($i=0;$i < $total;$i++) {
			$data = array();
			$data = JoomlaquizModelPrintresult::JQ_GetResults($info[$i]->c_id);
			$str .= "".($i+1).".[".number_format($data['c_score'],1).'/'.number_format($data['c_point'],1)."] ".$data['c_question']."\n";
			$type = JoomlaquizHelper::getQuestionType($data['c_type']);
			$answer = '';
			
			$email_data = array();
			$email_data['quest_type'] = $type;
			$email_data['data'] = $data;
			$email_data['str'] = $str;
			$email_data['answer'] = $answer;
			
			$appsLib->triggerEvent( 'onSendEmail' , $email_data );
			$str = $email_data['str'];
			
			$str .= "\n";
		}
		$str .= " ";
		
		return nl2br($str);
	}

	/**
	 * @param $sid
	 *
	 * @return array
	 */
	public function generatePDF($sid)
	{
		$appsLib = JqAppPlugins::getInstance();
		$plugins = $appsLib->loadApplications();

		defined(_PDF_GENERATED) or define(
			'_PDF_GENERATED', JText::_('COM_JOOMLAQUIZ_PDF_GENERATED')
		);
		$database = JFactory::getDBO();

		$str = "";
		$query
		     = "SELECT sq.*, q.*, u.* FROM #__quiz_t_quiz AS q, #__quiz_r_student_quiz AS sq LEFT JOIN #__users AS u ON sq.c_student_id = u.id"
			. "\n WHERE sq.c_id = '" . $sid . "' AND sq.c_quiz_id = q.c_id";
		$database->SetQuery($query);
		$info = $database->LoadAssocList();
		$info = $info[0];

		$info['username'] = ($info['username'])
			? $info['username']
			: JText::_(
				'COM_JOOMLAQUIZ_ANONYMOUS'
			);
		$info['name']     = ($info['name']) ? $info['name']
			: $info['user_name'] . ' ' . $info['user_surname'];
		$info['email']    = ($info['email']) ? $info['email']
			: $info['user_email'];

		JoomlaquizHelper::JQ_GetJoomFish(
			$info['c_title'], 'quiz_t_quiz', 'c_title', $info['c_quiz_id']
		);
		$quiz_id = $info['c_quiz_id'];

		$query = "SELECT q_chain FROM #__quiz_q_chain "
			. "\n WHERE s_unique_id = '" . $info['unique_id'] . "'";
		$database->SetQuery($query);
		$qch_ids = $database->LoadResult();
		$qch_ids = str_replace('*', ',', $qch_ids);

		$total = JoomlaquizHelper::getTotalScore($qch_ids, $quiz_id);

		chdir(JPATH_SITE);

		require_once(JPATH_SITE
			. '/components/com_joomlaquiz/assets/tcpdf/jq_pdf.php');

		$pdf_doc = new jq_pdf();

		$pdf = &$pdf_doc->_engine;

		$pdf->SetFont('dejavusans');
		$fontFamily = $pdf->getFontFamily();

		$pdf->getAliasNbPages();
		$pdf->AddPage();

		$pdf->SetFontSize(10);

		$pdf->setFont($fontFamily, 'B');
		$str = JText::_('COM_QUIZ_PDF_QTITLE') . '&nbsp;';
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

		$pdf->setFont($fontFamily);
		$str = $info['c_title'];
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$str = JText::_('COM_QUIZ_PDF_UNAME') . "&nbsp;";
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

		$pdf->setFont($fontFamily);
		$str =  $info['username'];
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$str = JText::_('COM_QUIZ_PDF_NAME') . "&nbsp;";
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

		$pdf->setFont($fontFamily);
		$str =  $info['name'];
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$str = JText::_('COM_QUIZ_PDF_UEMAIL') . "&nbsp;";
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

		$pdf->setFont($fontFamily);
		$str = $info['email'];
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$str = JText::_('COM_QUIZ_PDF_USCORE') . "&nbsp;";
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

		$pdf->setFont($fontFamily);
		$str = $info['c_total_score'];
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
		$pdf->Ln();

		/* results by category */
		if ($info['c_resbycat'] == 1
			&& JComponentHelper::getParams(
				'com_joomlaquiz'
			)->get('res_by_cats_pdf', 0)
		)
		{

			$q_cate = JoomlaquizHelper::getResultsByCategories($sid);

			$pdf->Write(
				5, $pdf_doc->cleanText(
				JText::_('COM_QUIZ_RES_SCORE_BY_CATEGORIES')
			), '', 0
			);
			$pdf->Ln();
			foreach ($q_cate as $curcat)
			{
				if ($curcat[2] || $i)
				{
					$percent = ($curcat[2]) ? number_format(
						($curcat[1] / $curcat[2]) * 100, 0, '.', ','
					) : 0;
					$cat_str
					         = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
						. $curcat[0] . ': ' . sprintf(
							JText::_('COM_QUIZ_RES_MES_SCORE_TPL'), $curcat[1],
							$curcat[2], $percent
						) . "<br />";
					$pdf->Write(5, $pdf_doc->cleanText($cat_str), '', 0);
					$pdf->Ln();
				}
				$i++;
			}
		}

		$pdf->setFont($fontFamily, 'B');
		$str = JText::_('COM_QUIZ_PDF_TOTSCORE') . "&nbsp;";
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

		$pdf->setFont($fontFamily);
		$str = $total;
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		$str = JText::_('COM_QUIZ_PDF_PASSCORE') . "&nbsp;";
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

		$pdf->setFont($fontFamily);
		$str = $info['c_passing_score'] . "%";
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
		$pdf->Ln();


		$tot_min = floor($info['c_total_time'] / 60);
		$tot_sec = $info['c_total_time'] - $tot_min * 60;

		$pdf->setFont($fontFamily, 'B');
		$str = JText::_('COM_QUIZ_PDF_USPENT') . "&nbsp;";
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

		$pdf->setFont($fontFamily);
		$str = '&nbsp;'.str_pad($tot_min, 2, "0", STR_PAD_LEFT) . ":" . str_pad(
				$tot_sec, 2, "0", STR_PAD_LEFT
			).'&nbsp;';
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

		$pdf->setFont($fontFamily);
		$str = JText::_('COM_QUIZ_PDF_QTIME');
		$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
		$pdf->Ln();

		$pdf->setFont($fontFamily, 'B');
		if ($info['c_passed'] == 1)
		{
			$str = $info['name'] . " " . JText::_('COM_QUIZ_PDF_PASQUIZ_BOLD');
			$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
			$pdf->Ln();
		}
		else
		{
			$str = $info['name'] . " " . JText::_(
					'COM_QUIZ_PDF_NPASSQUIZ_BOLD'
				);
			$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);
			$pdf->Ln();
		}
		$query = $database->getQuery(true);
		$query->select('`rq`.`c_id`')->from(
				'`#__quiz_r_student_question` AS `rq`'
			)->join(
				'LEFT',
				'`#__quiz_t_question` AS `tq` ON `rq`.`c_question_id` = `tq`.`c_id`'
			)->order('`c_id`');
		if (JComponentHelper::getParams('com_joomlaquiz')->get(
			'hide_boilerplates'
		)
		)
		{
			$query->where('`tq`.`c_type` != 9');
		}
		$query->where('`rq`.`c_stu_quiz_id` = "' . $sid . '"');
		$database->SetQuery($query);
		$info  = $database->LoadObjectList();
		$total = count($info);

		for ($i = 0; $i < $total; $i++)
		{
			$data = JoomlaquizModelPrintresult::JQ_GetResults($info[$i]->c_id);

			$pdf->Ln();
			$pdf->setFont($fontFamily, 'B');
			//$pdf->setStyle('b', true);
			$str = ($i + 1) . ".[" . number_format($data['c_score'],1) . '/' . number_format($data['c_point'],1)
				. "]";
			$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

			$pdf->setFont($fontFamily, 'B');
			//$pdf->setStyle('b', false);
			$str = $data['c_question'];
			$pdf->Write(5, $pdf_doc->cleanText($str), '', 0);

			$type           = $data['c_type'];
			$answer         = '';
			$correct_answer = '';

			$t                      = JoomlaquizHelper::getQuestionType($type);
			$pdf_data               = array();
			$pdf_data['quest_type'] = $t;
			$pdf_data['pdf_doc']    = $pdf_doc;
			$pdf_data['data']       = $data;
			$pdf_data['pdf']        = $pdf;

			$appsLib->triggerEvent('onGetPdf', $pdf_data);
			$pdf = $pdf_data['pdf'];

			$pdf->Ln();
		}

		return $pdf;
	}
}
?>