<?php
/**
* JoomlaQuiz Multiple question Plugin for Joomla
* @version $Id: mchoice.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage mchoice.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('components.com_joomlaquiz.helpers.plgquestion',JPATH_SITE);

class plgJoomlaquizMchoice extends plgJoomlaquizQuestion
{
	var $name		= 'Mchoice';
	var $_name		= 'mchoice';
	
	public function onPointsForAnswer(&$data){
		$database = JFactory::getDBO();
		
		$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."' AND c_right = 1";
		$database->SetQuery( $query );
		$tmp_pointz = $database->LoadResult();
		if(floatval($tmp_pointz))
			$data['q_data']->c_point = $data['q_data']->c_point.' - '.(floatval($tmp_pointz) + $data['q_data']->c_point);
		
		return $data['q_data'];
	}
	
	public function onScoreByCategory(&$data){
		
		$database = JFactory::getDBO();
		$database->setQuery("SELECT SUM(a_point) FROM #__quiz_t_choice WHERE `c_question_id` = '".$data['score_bycat']->c_id."'");
		$data['score'] = $database->loadResult();
		
		return true;
	}
	
	public function onFeedbackQuestion(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['q_data']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$choice_data[0]->score = $data['score'];
		
		foreach($choice_data as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($choice_data[$t]->text, 'quiz_t_choice', 'c_choice', $choice_data[$t]->value);
		}
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['q_data']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();

		if($past_this) {
			$query = "SELECT count(*) FROM #__quiz_r_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
			$database->setQuery($query);
			$choice_this_one = $database->LoadResult();
			
			for($i=0;$i<count($choice_data);$i++)
			{
				$query = "SELECT count(*) FROM #__quiz_r_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['q_data']->c_id."'";
				$database->setQuery($query);
				$choice_this = $database->LoadResult();
				$temp_stat = round(($choice_this*100)/$choice_this_one);
				$choice_data[$i]->statistic = $data['q_data']->c_title_true.' '.$temp_stat.'%; '.$data['q_data']->c_title_false.' '.(100 - $temp_stat).'%';
			}
			$choice_data[0]->past_this = $past_this;
		}
		$query = "SELECT c_id FROM #__quiz_r_student_question AS sq WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' AND c_question_id = '".$data['q_data']->c_id."'";
		$database->SetQuery( $query );

		$sid = $database->loadResult( );

		$query = "SELECT * FROM #__quiz_t_choice AS c  LEFT JOIN #__quiz_r_choice AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$sid."'"
		. "\n WHERE c.c_question_id = '".$data['q_data']->c_id."' ORDER BY c.ordering";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
		
		foreach($tmp as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_choice'], 'quiz_t_choice', 'c_choice', $tmp[$t]['c_choice_id']);
		}

		$uanswer = array();
		if(is_array($tmp))
		foreach($tmp as $t) {
			if($t['c_choice_id']) {
				$uanswer[] = $t['c_choice_id'];
			} 
		}
		$choice_data[0]->c_title_true = $data['q_data']->c_title_true;
		$choice_data[0]->c_title_false = $data['q_data']->c_title_false;
		
		$feedback_data = array();
		$feedback_data['choice_data'] = $choice_data;
		$feedback_data['user_answer'] = $uanswer;
		$qhtml = JoomlaQuiz_template_class::JQ_createFeedback($feedback_data, $data);

		if(preg_match('/pretty_green/', $data['cur_template'])){
			$data['qoption'] = "\t" . $qhtml. "\n";
		} else {
			$data['qoption'] = "\t" . '<div><form  onsubmit=\'javascript: return false;\' name=\'quest_form\'>'. $qhtml .'</form></div>' . "\n";
		}
		return $data['qoption'];
	}
	
	public function onGetResult(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT *, c.c_id AS id FROM (#__quiz_t_choice AS c, #__quiz_t_question AS q) LEFT JOIN #__quiz_r_choice AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$data['id']."'"
		. "\n WHERE c.c_question_id = '".$data['qid']."' AND q.c_id = c.c_question_id ORDER BY c.ordering";
		$database->SetQuery( $query );
		$tmp = $database->LoadAssocList();
			
		foreach($tmp as $t=>$cd) {
			JoomlaquizHelper::JQ_GetJoomFish($tmp[$t]['c_choice'], 'quiz_t_choice', 'c_choice', $tmp[$t]['id']);
		}
		$data['info']['c_choice'] = $tmp;
			
		$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id = ".$data['qid']." ";
		$database->SetQuery( $query );
		$data['info']['c_point'] += $database->LoadResult();
		
		return true;
	}
	
	public function onMchoiceResultPdf($question, $result, $pdf, $score, $total, $pdf_doc, $i){
        /** @var TCPDF $pdf */
        $fontFamily = $pdf->getFontFamily();

        $qd = new stdClass();
        $qd->question_id = $question['id'];
        $qd->c_stu_quiz_id = $result;

        $rdata = JLayoutHelper::render('question.json.report', $qd, JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
        $rdata = json_decode($rdata);
        $score = array_sum(array_map(function($a){
            return $a->score;
        },$rdata));
        $total = $this->getTotalScore($question['id']);

        parent::onGetResultPdf($question, $result, $pdf, $score, $total, $pdf_doc, $i);
        $pdf->setFont($fontFamily);

        $pdf->writeHTML($question['text'], true);

        foreach ($rdata as $quest){
            $pdf->writeHTML("<br>", true);
            $pdf->write(5,$quest->text,'',false,'',true);
            $show_correct = 0;
            $pdf->setFont($fontFamily,'B');
            $pdf->write(4, 'Your answer:','',false,'',true);
            $pdf->setFont($fontFamily);
            foreach ($quest->options as $option){
                $pdf->write(6, $option->picked?(($option->right || !$show_correct)?'✔ ':'✖ '):'    ','',false,'',false);
                $pdf->write(6, $option->text,'',false,'',true);
            }
            if($show_correct){
                $pdf->setFont($fontFamily,'B');
                $pdf->write(4, 'Correct answer:','',false,'',true);
                $pdf->setFont($fontFamily);
                foreach ($quest->options as $option){
                    if($show_correct){
                        $pdf->write(6, $option->right?'✔ ':'✖ ','',false,'',false);
                    }
                    $pdf->write(6, $option->text,'',false,'',true);
                }
            }
        }
        return $pdf;
	}

	public function onMchoicePrintResult($question, $result, $score, $total, $i){
	    ob_start();
        $qd = new stdClass();
        $qd->question_id = $question['id'];
        $qd->c_stu_quiz_id = $result;

        $rdata = JLayoutHelper::render('question.json.report', $qd, JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
        $rdata = json_decode($rdata);
        $score = array_sum(array_map(function($a){
            return $a->score;
        },$rdata));
        $total = $this->getTotalScore($question['id']);

        parent::onPrintResult($question, $result, $score, $total, $i);

        echo $question['text'];

        foreach ($rdata as $quest){
            ?><br><div><?= $quest->text ?></div><div><strong>Your answer:</strong></div><?php
            $show_correct = 0;
            foreach ($quest->options as $option){
                echo $option->picked?(($option->right || !$show_correct)?'✔ ':'✖ '):'&nbsp;&nbsp;&nbsp;&nbsp;';
                echo $option->text;
                echo '<br>';
            }
            if($show_correct){
                ?><div><strong>Correct answer:</strong></div><?php
                foreach ($quest->options as $option){
                    echo $option->right?'✔ ':'&nbsp;&nbsp;&nbsp;&nbsp;';
                    echo $option->text;
                    echo '<br>';
                }
            }
        }
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
	}
	
	public function onSendEmail(&$data){
	
		$data['str'] .= "  ".JText::_('COM_QUIZ_PDF_ANSWER')." \n";
		for($j=0,$k='A';$j < count($data['data']['c_choice']);$j++,$k++) {

			$data['str'] .= "$k. ".$data['data']['c_choice'][$j]['c_choice']. ' - '. ($data['data']['c_choice'][$j]['c_choice_id']? $data['data']['c_choice'][$j]['c_title_true']: $data['data']['c_choice'][$j]['c_title_false']) ."\n";
		}
		$data['str'] .= "<hr />";
		return $data['str'];
	}
	
	public function onGetStatistic(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
		
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		$past_this += 0.0000000000001;
		$query = "SELECT count(*) FROM #__quiz_r_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
		$database->setQuery($query);
		$choice_this_one = $database->LoadResult();
		
		for($i=0;$i<count($choice_data);$i++)
		{
			$query = "SELECT count(*) FROM #__quiz_r_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
			$database->setQuery($query);
			$choice_this = $database->LoadResult();
			$temp_stat = round(($choice_this*100)/$past_this);
			
			$choice_data[$i]->statistic1 = $temp_stat.'%';
			$choice_data[$i]->statistic2 = (intval($past_this)?(100 - $temp_stat):0).'%';
			$choice_data[$i]->count = (int)$past_this;
		}		
		$data['question']->choice_data = $choice_data;		
		return $data['question'];	
	}

	public function onStatisticContent(&$data){
		
		$data['question']->c_title_true = $data['question']->c_title_true? $data['question']->c_title_true: JText::_('COM_QUIZ_SIMPLE_TRUE');
		$data['question']->c_title_false = $data['question']->c_title_false? $data['question']->c_title_false: JText::_('COM_QUIZ_SIMPLE_FALSE');
		if (isset($data['question']->choice_data) && is_array($data['question']->choice_data))
		foreach($data['question']->choice_data as $cdata){
			?>
			<tr>
				<td><?php echo $cdata->text?></td>
				<td align="center"><?php echo $cdata->count?></td>
				<td><?php echo ($cdata->c_right? '<font color="#00CC00">'.$data['question']->c_title_true.'</font>':$data['question']->c_title_true).' - '.$cdata->statistic1?><br />
					<?php echo (!$cdata->c_right? '<font color="#00CC00">'.$data['question']->c_title_false.'</font>':$data['question']->c_title_false).' - '.$cdata->statistic2?>
				</td>
				<td><div style="width:100%; border:1px solid #cccccc;margin-bottom:3px;"><div style="height: 5px; width: <?php echo $cdata->statistic1+1;?>%; " class="jq_color_1"></div></div>
					<div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $cdata->statistic2+1;?>%;" class="jq_color_2"></div></div>
				</td>
			</tr>
			<?php
		}
		
	}

	public function onAdminIsFeedback(&$data){
		return true;
	}
	
	public function onAdminIsPoints(&$data){
		return true;
	}
	
	public function onAdminIsPenalty(&$data){
		return true;
	}
	
	public function onAdminIsReportName(){
		return true;
	}

	public function onGetAdminQuestionData(&$data){
	
		$database = JFactory::getDBO();
		
		$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__quiz_t_choice WHERE c_question_id = '".$data['question']->c_id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$choice_data = $database->LoadObjectList();
				
		$query = "SELECT count(*) FROM #__quiz_r_student_question WHERE c_question_id = '".$data['question']->c_id."'";
		$database->setQuery($query);
		$past_this = $database->LoadResult();
		$past_this += 0.0000000000001;
		$query = "SELECT count(*) FROM #__quiz_r_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
		$database->setQuery($query);
		$choice_this_one = $database->LoadResult();
					
		for($i=0;$i<count($choice_data);$i++)
		{
			$query = "SELECT count(*) FROM #__quiz_r_choice as sch, #__quiz_r_student_question as qst WHERE sch.c_choice_id = '".$choice_data[$i]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".$data['question']->c_id."'";
			$database->setQuery($query);
			$choice_this = $database->LoadResult();
			$temp_stat = round(($choice_this*100)/$past_this);
						
			$choice_data[$i]->statistic1 = $temp_stat.'%';
			$choice_data[$i]->statistic2 = (intval($past_this)?(100 - $temp_stat):0).'%';
			$choice_data[$i]->count = (int)$past_this;
		}
			
		$data['question']->choice_data = $choice_data;
				
		return $data['question'];	
	}
	
	public function onGetAdminStatistic(&$data){
		$data['question']->c_title_true = $data['question']->c_title_true? $data['question']->c_title_true: 'True';
		$data['question']->c_title_false = $data['question']->c_title_false? $data['question']->c_title_false: 'False';
		if (is_array($data['question']->choice_data))
			foreach($data['question']->choice_data as $cdata){
				?>
				<tr>
					<td><?php echo $cdata->text?></td> 
					<td><?php echo $cdata->count?></td>
					<td><?php echo ($cdata->c_right? '<font color="#00CC00">'.$data['question']->c_title_true.'</font>':$data['question']->c_title_true).' - '.$cdata->statistic1?><br />
						<?php echo (!$cdata->c_right? '<font color="#00CC00">'.$data['question']->c_title_false.'</font>':$data['question']->c_title_false).' - '.$cdata->statistic2?>
					</td>
					<td><div style="width:100%; border:1px solid #cccccc;margin-bottom:3px;"><div style="height: 5px; width: <?php echo $cdata->statistic1+1;?>%; " class="jq_color_1"></div></div>
						<div style="width:100%; border:1px solid #cccccc;"><div style="height: 5px; width: <?php echo $cdata->statistic2+1;?>%;" class="jq_color_2"></div></div>
					</td>
				</tr>
				<?php												
			}
	}
	
	public function onGetAdminCsvData(&$data){
		
		$database = JFactory::getDBO();
		$query = "SELECT `a`.`c_score` FROM `#__quiz_r_student_question` AS `a` WHERE `a`.`c_stu_quiz_id` = '".$data['result']->c_id."' AND `a`.`c_question_id` = '".$data['question']->c_id."'";
		$database->setQuery( $query );
		$score = $database->loadResult();
		if ($score != null)
			$data['answer'] = 'Score - '.$score;
		
		return $data['answer'];	
	}

    /**
     * Called from JTable::store to handle type specific data.
     *
     * @param $data
     * @since 3.9
     */
    public function onStoreQuestion(&$data)
    {
        $input         = JFactory::getApplication()->input;
        $db            = JFactory::getDbo();
        $sub_questions = $input->get('subquestion', array(), 'ARRAY');
        $sub_questions = array_map(function ($question) use ($db, $data) {
            $question              = new \Joomla\Registry\Registry($question);
            $quest_row             = (object)array_fill_keys (
                array_keys($db->getTableColumns('#__quiz_t_question')),
                ''
            );
            $quest_row->c_id       = $question->get('id');
            $quest_row->parent_id  = $data->get('c_id');
            $quest_row->c_question = $question->get('text');
            $quest_row->c_random = $question->get('shuffle');
            $quest_row->c_point = $question->get('points');
            $quest_row->c_partial = $question->get('partial');
            $quest_row->c_attempts = $question->get('attempts');
            $quest_row->c_feedback  = $question->get('feedback');
            $quest_row->c_right_message  = $question->get('feedback_correct');
            $quest_row->c_wrong_message  = $question->get('feedback_incorrect');
            $quest_row->c_partially_message  = $question->get('feedback_partial');
            if ($quest_row->c_id) {
                $db->updateObject('#__quiz_t_question', $quest_row, 'c_id');
            } else {
                $db->insertObject('#__quiz_t_question', $quest_row);
                $question->set('id', $db->insertid());
            }

            // TODO: delete not needed entires
            $options = array_map(function ($option) use ($db, $question) {
                $option             = new \Joomla\Registry\Registry($option);
                $option_row         = (object)array_fill_keys(
                    array_keys($db->getTableColumns('#__quiz_options')),
                    ''
                );
                $option_row->id     = $option->get('id');
                $option_row->question = $question->get('id');
                $option_row->text   = $option->get('text');
                $option_row->right  = $option->get('right');
                $option_row->points  = $option->get('points');
                if ($option_row->id) {
                    $db->updateObject('#__quiz_options', $option_row, 'id');
                } else {
                    $db->insertObject('#__quiz_options', $option_row);
                    $option->set('id', $db->insertid());
                }

                return $option;
            }, $question->get('options'));
            $question->set('options', $options);

            return $question;
        }, $sub_questions);

        $query = $db->getQuery(true);
        $query->delete($db->qn('#__quiz_t_question'))
            ->where($db->qn('parent_id').' = '.$db->q($data->get('c_id')));
        if($sub_questions){
            $query->where($db->qn('c_id').' NOT IN ('.implode(',',array_map(function($q)use($db){return $db->q($q->get('id'));},$sub_questions)).')');
        }
        $optquesry = $db->getQuery(true);
        $db->setQuery($query);
        $query->clear('delete')
            ->select($db->qn('c_id'))
            ->from($db->qn('#__quiz_t_question'));
        $optquesry->delete($db->qn('#__quiz_options'))
            ->where($db->qn('question').' IN ('.$query.')');
        $db->execute();
        $db->setQuery($optquesry);
        $db->execute();
    }

    /**
     * Called via com_ajax while passing the quiz
     *
     * @since 3.9
     */
    public function onAjaxMchoiceAnswerRenderSubquestion(){
	    $input = JFactory::getApplication()->input;
	    $session = JFactory::getSession();
	    $session_data = $session->get('quiz.'.$input->get('stu_quiz_id',0).'.question.'.$input->get('question',0));
	    $session_answers = new \Joomla\Registry\Registry($session_data);
	    $input->set('option','com_joomlaquiz');
        $answers = $input->get('answers',array(),'ARRAY');
        $question_data = json_decode(JLayoutHelper::render('question.json.subquestions', $input->get('question',0), JPATH_SITE.'/plugins/joomlaquiz/mchoice/'));
        $question_data = array_combine(array_map(function($quest){
            return $quest->id;
        },$question_data),$question_data);

        foreach ($answers as $quest => $ans){
            /*
             * Possibly can cause some behavior
             */
            if($question_data[$quest]->attempts == 0 || $question_data[$quest]->attempts > $session_answers->get($quest.'_attempted',0)){
                $session_answers->set($quest,$ans);
            }else{
                /*
                 * This question reached it's attempts limit last time
                 * we do not need to send any feedback (already sent)
                 * and we do not need it to break the JS loop
                 */
                $question_data[$quest]->feedback = false;
                $session_answers->def($quest,$ans);
            }
            $session_answers->set($quest.'_attempted',$session_answers->get($quest.'_attempted',0)+1);

            $question_data[$quest]->again = false;

            if($question_data[$quest]->attempts == 0 || $question_data[$quest]->attempts > $session_answers->get($quest.'_attempted',0)){
                $question_data[$quest]->again = true;
            }
            $res = $this->checkAnswersFor($question_data[$quest], $ans);
            if($res->get('correct')){
                $question_data[$quest]->again = false;
                $question_data[$quest]->feedback_type = 'correct';
            }elseif($res->get('got_one_correct') && $question_data[$quest]->partial){
                $question_data[$quest]->feedback_type = 'partial';
            }else{
                $question_data[$quest]->feedback_type = 'incorrect';
            }
        }

        $session->set('quiz.'.$input->get('stu_quiz_id',0).'.question.'.$input->get('question',0), $session_answers->toString());
        $answered = array_keys($answers);

        if($question_data){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->qn('params'))
                ->from($db->qn('#__quiz_t_question'))
                ->where($db->qn('c_id').' = '.$db->q($input->get('question',0)));
            $params = new Joomla\Registry\Registry($db->setQuery($query)->loadResult());
            $splice = $params->get('onebyone')?($params->get('onebyone')+count($answered)):null;

            $question_data = array_splice($question_data, 0, $splice);
//            echo "<pre>";
//            print_r($splice);
//            echo "</pre>";
//            echo "<pre>";
//            print_r(count($answered));
//            echo "</pre>";
//            echo "<pre>";
//            print_r($question_data);
//            echo "</pre>";
//            die();
            $data = array();
            foreach ($question_data as $question){
                if($question->feedback){
                    JFactory::getApplication()->enqueueMessage(JLayoutHelper::render('question.feedback.'.$question->feedback_type, $question, JPATH_SITE.'/plugins/joomlaquiz/mchoice/'),$question->id);
                }
                $data[] = array(
                    'id'=> $question->id,
                    'again'=> $question->again,
                    'html'=> ($question->again===null?JLayoutHelper::render('question.subquestion', $question, JPATH_SITE.'/plugins/joomlaquiz/mchoice/'):'')
                );
                if($question->again){
                    break;
                }
            }
            return $data;
        }else{
            return false;
        }
    }

    /**
     * Checks result for specific sub-question
     *
     * @param $question
     * @param $answer
     *
     * @return \Joomla\Registry\Registry
     * @since 3.9
     */
    public function checkAnswersFor($question, $answer){
        $registry = new \Joomla\Registry\Registry();

        $correct_answer = array_filter(array_map(function($opt){
            if($opt->right){
                return $opt->id;
            }else{
                return false;
            }
        },$question->options));
        $registry->set('correct_answer', $correct_answer);


        $registry->set('remained', array_diff($registry->get('correct_answer', array()), $answer));
        $registry->set('redundant', array_diff($answer, $registry->get('correct_answer', array())));
        $registry->set('missed', array_merge($registry->get('redundant',array()), $registry->get('remained',array())));

        if($registry->get('remained',array()) != $registry->get('correct_answer', array())){
            $registry->set('got_one_correct', true);
        }
        if(!$registry->get('missed',array())){
            $registry->set('correct', true);
        }
        $registry->set('points-for-options',count($registry->get('correct_answer', array()))>1);

        return $registry;
    }

    /**
     * Called from models/ajaxaction to store answers for the question
     *
     * @param $data
     * @since 2.5
     * @return bool
     */
    public function onSaveQuestion(&$data){
        return $this->onSaveAnswers($data);
	}

    /**
     * Called from models/ajaxaction to store answers for the question
     *
     * @param $data
     *
     * @return bool
     * @since 3.9
     */
    public function onSaveAnswers(&$data){

        $answers = json_decode($data['answer'], true);
        $question_data = json_decode(JLayoutHelper::render('question.json.subquestions', $data['quest_id'], JPATH_SITE.'/plugins/joomlaquiz/mchoice/'));

        $db = JFactory::getDbo();
        $session = JFactory::getSession();
        $session_data = $session->get('quiz.'.$data['stu_quiz_id'].'.question.'.$data['quest_id']);
        $session_answers = new \Joomla\Registry\Registry($session_data);
	    foreach ($answers as $quest => $answer){
            $session_answers->def($quest,$answer);
        }
        $data['is_correct'] = 1;
        $total_points = 0;
        if($session_answers->toArray()){
            foreach ($question_data as $question){
                $answer = $session_answers->get($question->id, array());
                /*
                 * moved to ::checkAnswersFor($question, $answer)
                $correct_answer = array_filter(array_map(function($opt){
                    if($opt->right){
                        return $opt->id;
                    }else{
                        return false;
                    }
                },$question->options));

                $remained = array_diff($correct_answer, $answer);
                $redundant = array_diff($answer, $correct_answer);
                $missed = array_merge($redundant, $remained);
                $is_correct = false;

                if($remained != $correct_answer){
                    $data['got_one_correct'] = true;
                }
                if($missed){
                    $data['is_correct'] = 0;
                }else{
                    $is_correct = true;
                }
                */
                $res = $this->checkAnswersFor($question,$answer);
                $correct_answer = $res->get('correct_answer', array());
                $missed = $res->get('missed', array());
                $remained = $res->get('remained', array());
                $redundant = $res->get('redundant', array());
                $is_correct = $res->get('correct', array());

                if($res->get('got_one_correct',0)){
                    $data['got_one_correct'] = true;
                }

                if(!$is_correct){
                    $data['is_correct'] = 0;
                }

                $points = 0;
                if($res->get('points-for-options',false)){
                    /*
                     * each $redundant - option points
                     * each $correct and not $remained + option points
                     */
                    array_map(function($option) use (&$points, $remained, $redundant){
                        if(in_array($option->id, $redundant)){
                            $points -= $option->points;
                        }else{
                            if(!in_array($option->id, $remained) && $option->right){
                                $points += $option->points;
                            }
                        }
                    },$question->options);
                }

                if($question->partial){
                    if($res->get('got_one_correct',0)){
                        $percentage = (count($correct_answer)-count($missed))/($redundant?count($question->options):count($correct_answer));
                        $points += round(($percentage) * $question->points,2);
                        $total_points += $percentage;
                    }
                }else{
                    if(!$missed){
                        $points += $question->points;
                        $total_points += 1;
                    }
                }

                $question_result = new stdClass();
                $question_result->c_stu_quiz_id = $data['stu_quiz_id'];
                $question_result->c_question_id = $question->id;
                $question_result->c_score = $points;
                $question_result->c_attempts = 1;
                $question_result->is_correct = $is_correct;
                $db->insertObject('#__quiz_r_student_question', $question_result);
                $question_result->id = $db->insertid();
                array_map(function($option_id) use ($db,$question_result){
                    $question_choice = new stdClass();
                    $question_choice->c_sq_id = $question_result->id;
                    $question_choice->c_choice_id = $option_id;
                    $db->insertObject('#__quiz_r_choice',$question_choice);
                },$answer);
            }
        }

        $all_points = 0;
        $question_result = new stdClass();
        $question_result->c_stu_quiz_id = $data['stu_quiz_id'];
        $question_result->c_question_id = $data['quest_id'];
        $question_result->c_score = round($total_points/count($question_data) * $all_points,2);
        $question_result->c_attempts = 1;
        $question_result->is_correct = $data['is_correct'];
        $db->insertObject('#__quiz_r_student_question', $question_result);
        $session->set('quiz.'.$data['stu_quiz_id'].'.question.'.$data['quest_id'], null);
        $data['is_avail'] = 0;
        $data['is_no_attempts'] = 1;
        $data['score'] = $question_result->c_score;

		return true;
	}

    /**
     * Returns max total score for passed ids.
     * Called from front-end
     *
     * @param $data
     *
     * @return bool
     * @since 2.5
     */
    public function onTotalScore(&$data){

        $data['max_score'] = $this->getTotalScore($data['qch_ids']);

        return true;
    }

    /**
     * Returns max total score for passed ids.
     *
     * @param $ids
     *
     * @return bool
     * @since 3.9
     */
    public function getTotalScore($ids = array()){

        if(is_array($ids)){
            $ids = implode(',',$ids);
        }

        $max_score = 0;
        if($ids){
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('*')
                ->from($db->qn('#__quiz_t_question'))
                ->where($db->qn('c_type').' = '.$db->q(15))
                ->where($db->qn('c_id').' IN ('.$ids.')');
            $questions = $db->setQuery($query)->loadObjectList();
            $qids = array_map(function($quest){
                return $quest->c_id;
            },$questions);

            if($qids){
                $query->clear();
                $query->select('SUM(c_point)')
                    ->from($db->qn('#__quiz_t_question'))
                    ->where($db->qn('c_id').' IN ('.implode(',',$qids).')','OR')
                    ->where($db->qn('parent_id').' IN ('.implode(',',$qids).')');
                $max_score += $db->setQuery($query)->loadResult();

                $options_quesry = $db->getQuery(true);
                $query->clear('select')
                    ->select('c_id');
                $options_quesry->select('SUM(points)')
                    ->from($db->qn('#__quiz_options'))
                    ->where($db->qn('question').' IN ('.$query.')')
                    ->where($db->qn('right').' = '.$db->q(1));
                $max_score += $db->setQuery($options_quesry)->loadResult();
            }
        }

        return $max_score;
    }
}