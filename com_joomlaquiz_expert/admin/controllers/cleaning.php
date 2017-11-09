<?php
/**
 * Created by PhpStorm.
 * User: shvets_a
 * Date: 03.11.2017
 * Time: 18:32
 */
defined('_JEXEC') or die;

jimport( 'joomla.form.form' );
jimport( 'joomla.plugin.helper' );

class JoomlaquizControllerCleaning extends JControllerForm
{
    public function __construct($config = array()) {

        parent::__construct($config);
    }

    public function cleaning() {

        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $input = JFactory::getApplication()->input;
        $formData = $input->get('jform', '', 'array');
        $cleaning_from = $formData['cleaning_from'];
        $cleaning_to = $formData['cleaning_to'];

        if($cleaning_from > $cleaning_to) {
            $app->enqueueMessage(JText::_('COM_JOOMLAQUIZ_CLEANING_DATE_ERROR'), 'error');
        } else {
            $query = $db->getQuery(true);
            $query = "SELECT tquiz.c_id AS quiz_id, tquest.c_id AS quest_id, types.c_type FROM #__quiz_t_quiz AS tquiz 
                        INNER JOIN l9o6i_quiz_t_question AS tquest 
                          ON (tquiz.c_id = tquest.c_quiz_id)
                        INNER JOIN l9o6i_quiz_t_qtypes AS types
                          ON (tquest.c_type = types.c_id)
                        WHERE tquiz.c_created_time > '" . $cleaning_from . "' 
                          AND tquiz.c_created_time < '" . $cleaning_to . "'";
            $db->setQuery($query);
            $quests = $db->loadObjectList();

            $query = "DELETE FROM #__quiz_t_quiz  WHERE c_created_time > '" . $cleaning_from . "' AND c_created_time < '" . $cleaning_to . "'";
            $db->setQuery($query);
            $db->execute();
            $quiz_id = array();
            foreach ($quests as $quest) {
                $quiz_id[] = $quest->quiz_id;
            }

            if(count($quiz_id = array_unique($quiz_id))) {
                $query = "DELETE FROM #__quiz_t_question WHERE c_quiz_id IN ('" . implode("', '", $quiz_id) . "')";
                $db->setQuery($query);
                $db->execute();

                $query = "DELETE FROM #__quiz_r_student_quiz WHERE c_quiz_id IN ('" . implode("', '", $quiz_id) . "')";
                $db->setQuery($query);
                $db->execute();

                $query = "DELETE FROM #__quiz_r_student_question WHERE c_stu_quiz_id IN ('" . implode("', '", $quiz_id) . "')";
                $db->setQuery($query);
                $db->execute();

                $query = "DELETE FROM #__quiz_q_chain WHERE quiz_id IN ('" . implode("', '", $quiz_id) . "')";
                $db->setQuery($query);
                $db->execute();

                $query = "DELETE FROM #__quiz_t_pbreaks WHERE c_quiz_id IN ('" . implode("', '", $quiz_id) . "')";
                $db->setQuery($query);
                $db->execute();
            }

            foreach ($quests as $quest) {

                JPluginHelper::importPlugin('joomlaquiz', $quest->c_type);
                $className = 'plgJoomlaquiz' . ucfirst($quest->c_type);

                $data['quest_type'] = $quest->c_type;
                $data['quest_id'] = $quest->quest_id;
                $bla = method_exists($className, 'onCleaning');

                if (method_exists($className, 'onCleaning')) {
                    $return = $className::onCleaning($data);
                }
            }
            $app->enqueueMessage(JText::_('COM_JOOMLAQUIZ_CLEANING_SUCCESS'), 'message');
        }
        $this->setRedirect(JRoute::_('/administrator/index.php?option=com_joomlaquiz&view=cleaning'));
    }
}