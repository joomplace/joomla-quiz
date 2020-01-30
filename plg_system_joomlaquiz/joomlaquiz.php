<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Joomlaquiz
 *
 * @copyright   Copyright (C) 2005 - 2020 JoomPlace, www.joomplace.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomlaquiz Plugin
 *
 * @since  3.7
 */
class PlgSystemJoomlaquiz extends JPlugin
{
    protected $app;

    /**
     * Saving additional custom quiz settings
     */
    public function onJoomlaquizAfterSave($context)
    {
        $doc = $this->app->getDocument();

        if (!$this->app->isClient('administrator') || $doc->getType() !== 'html') {
            return;
        }

        if($context != 'com_joomlaquiz.quiz') {
            return;
        }

        $input = $this->app->input;
        $jform = $input->get('jform', array(), 'ARRAY');

        $limit_by_ip = (int)$jform['limit_by_ip'];
        $attempts_from_IP = (int)$jform['attempts_from_IP'];
        $congratulations_email = (int)$jform['congratulations_email'];
        $email_congratulation = $jform['email_congratulation'];
        $email_participant = $jform['email_participant'];

        if($input->get('task', '') == 'save2copy'){ //ToDo ?
            return;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $quiz_new = false;
        $quiz_custom_settings_isNew = false;
        $quiz_id = $input->getInt('c_id', 0);

        if($quiz_id == 0) {
            $quiz_new = true;
            $query->clear();
            $query->select($db->qn('c_id'))
                ->from($db->qn('#__quiz_t_quiz'))
                ->order('c_id DESC')
                ->setLimit(1);
            $db->setQuery($query);
            $quiz_id = (int)$db->loadResult();
        }

        $query->clear();
        $query->select($db->qn('id'))
            ->from($db->qn('#__quiz_custom_quiz'))
            ->where($db->qn('quiz_id') .'='. $db->q($quiz_id));
        $db->setQuery($query);
        $quiz_custom_settings_ID = $db->loadResult();

        if(!$quiz_custom_settings_ID){
            $quiz_custom_settings_isNew = true;
        }

        $quiz_custom_quiz = new stdClass();
        $quiz_custom_quiz->quiz_id = $quiz_id;
        $quiz_custom_quiz->limit_by_ip = $limit_by_ip;
        $quiz_custom_quiz->attempts_from_IP = $attempts_from_IP;
        $quiz_custom_quiz->congratulations_email = $congratulations_email;
        $quiz_custom_quiz->email_congratulation = $email_congratulation;
        $quiz_custom_quiz->email_participant = $email_participant;

        if($quiz_new || $quiz_custom_settings_isNew){
            try {
                $db->insertObject('#__quiz_custom_quiz', $quiz_custom_quiz);
            } catch (RuntimeException $e) {
                throw new Exception($e->getMessage(), 500, $e);
            }
        } else {
            $quiz_custom_quiz->id = $quiz_custom_settings_ID;
            try {
                $db->updateObject('#__quiz_custom_quiz', $quiz_custom_quiz, 'id');
            } catch (RuntimeException $e) {
                throw new Exception($e->getMessage(), 500, $e);
            }
        }

        return;
    }

    /**
     * Get additional custom quiz settings for to populate the form
     */
    public function onJoomlaquizGetItem($context, $item)
    {
        $doc = $this->app->getDocument();

        if (!$this->app->isClient('administrator') || $doc->getType() !== 'html') {
            return;
        }

        if ($context != 'com_joomlaquiz.quiz') {
            return;
        }
        
        if((int)$item->c_id){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('*')
                ->from($db->qn('#__quiz_custom_quiz'))
                ->where($db->qn('quiz_id') .'='. $db->q((int)$item->c_id));
            $db->setQuery($query);
            $custom_quiz_settings = $db->loadAssoc();
            
            if(!empty($custom_quiz_settings)) {
                foreach ($custom_quiz_settings as $key=>$value) {
                    if(in_array($key, array('id', 'quiz_id'))) {
                        continue;
                    }
                    $item->$key =$value;
                }
            }
        }
        
        return $item;
    }


    public function onJoomlaquizStart($context, $quiz_id, &$is_available=true)
    {
        if ($context != 'com_joomlaquiz.quiz.start') {
            return;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn(array('limit_by_ip', 'attempts_from_IP')))
            ->from($db->qn('#__quiz_custom_quiz'))
            ->where($db->qn('quiz_id') .'='. $db->q((int)$quiz_id));
        $db->setQuery($query);
        $custom_quiz_settings = $db->loadObject();

        if(!empty($custom_quiz_settings) && $custom_quiz_settings->limit_by_ip == 1) {
            $query->clear();
            $query->select($db->qn('ip_qty_passed'))
                ->from($db->qn('#__quiz_custom_ip_statistics'))
                ->where($db->qn('quiz_id') .'='. $db->q((int)$quiz_id))
                ->where('`ip` = (SELECT INET_ATON(\''.$_SERVER["REMOTE_ADDR"].'\'))');
            $db->setQuery($query);
            $ip_qty_passed = (int)$db->loadResult();

            if($custom_quiz_settings->attempts_from_IP <= $ip_qty_passed) {
                $is_available = false;
            }
        }

        return;
    }

    public function onJQuizFinished($params)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        //IP statistics update
        $query->clear();
        $query->select($db->qn(array('id', 'ip_qty_passed')))
            ->from($db->qn('#__quiz_custom_ip_statistics'))
            ->where($db->qn('quiz_id') .'='. $db->q((int)$params['quiz_id']))
            ->where('`ip` = (SELECT INET_ATON(\''.$_SERVER["REMOTE_ADDR"].'\'))');
        $db->setQuery($query);
        $statistics = $db->loadObject();

        if(!empty($statistics->id)) {
            $new_ip_qty_passed = (int)$statistics->ip_qty_passed + 1;
            $query->clear();
            $fields = array(
                $db->qn('ip_qty_passed') . '=' . $db->q($new_ip_qty_passed)
            );
            $conditions = array(
                $db->qn('quiz_id') . '=' . $db->q((int)$params['quiz_id']),
                '`ip` = (SELECT INET_ATON(\''.$_SERVER["REMOTE_ADDR"].'\'))'
            );
            $query->update($db->qn('#__quiz_custom_ip_statistics'))
                ->set($fields)
                ->where($conditions);
            $db->setQuery($query)
                ->execute();
        } else {
            $query->clear();
            $columns = array(
                'ip',
                'quiz_id',
                'ip_qty_passed'
            );
            $values = array(
                '(SELECT INET_ATON(\''.$_SERVER["REMOTE_ADDR"].'\'))',
                $db->q((int)$params['quiz_id']),
                $db->q(1)
            );
            $query->insert($db->qn('#__quiz_custom_ip_statistics'))
                ->columns($db->qn($columns))
                ->values(implode(',', $values));
            $db->setQuery($query)
                ->execute();
            /*
            $new_statistics = new stdClass();
            $new_statistics->ip = ip2long($_SERVER['REMOTE_ADDR']);
            $new_statistics->quiz_id = (int)$params['quiz_id'];
            $new_statistics->ip_qty_passed = 1;
            $db->insertObject('#__quiz_custom_ip_statistics', $new_statistics);
            */
        }
        //end IP statistics update

        //Updating statistics of passing the quiz and sending emails
        $query->clear();
        $query->select('*')
            ->from($db->qn('#__quiz_custom_quiz'))
            ->where($db->qn('quiz_id') .'='. $db->q((int)$params['quiz_id']));
        $db->setQuery($query);
        $custom_quiz_settings = $db->loadObject();

        if(!empty((int)$custom_quiz_settings->congratulations_email)) {
            $new_passed = (int)$custom_quiz_settings->qty_passed + 1;

            $query->clear();
            $fields = array(
                $db->qn('qty_passed') . '=' . $db->q($new_passed)
            );
            $conditions = array(
                $db->qn('quiz_id') . '=' . $db->q((int)$params['quiz_id'])
            );
            $query->update($db->qn('#__quiz_custom_quiz'))
                ->set($fields)
                ->where($conditions);
            $db->setQuery($query)
                ->execute();

            if(JFactory::getUser()->id > 0) {
                if ((int)$custom_quiz_settings->congratulations_email >= $new_passed) {
                    $email_text = $custom_quiz_settings->email_congratulation;
                    $subject = JText::_('PLG_SYSTEM_JOOMLAQUIZ_EMAIL_SUBJECT_CONGRATULATION');
                } else {
                    $email_text = $custom_quiz_settings->email_participant;
                    $subject = JText::_('PLG_SYSTEM_JOOMLAQUIZ_EMAIL_SUBJECT_PARTICIPANT');
                }

                $body = '';
                $body .= '<table class="table table-bordered table-striped table-hover">';
                $body .= '<tr><td>' . $email_text . '.</td></tr>';
                $body .= '</table>';

                $app = $this->app;
                $mailfrom = $app->get('mailfrom');
                $fromname = $app->get('fromname');
                $sitename = $app->get('sitename');

                $mail = JFactory::getMailer();
                $mail->IsHTML(true);
                $mail->addRecipient(JFactory::getUser()->email);
                $mail->setSender(array($mailfrom, $fromname));
                $mail->setSubject($sitename . ': ' . $subject);
                $mail->setBody($body);
                $mail->Send();
            }

        }

        return;
    }

}
