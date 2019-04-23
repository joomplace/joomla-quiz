<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldOrderingQuestion extends JFormFieldList
{
    protected $type = 'Orderingquestion';

    public function getOptions()
    {
        $app = JFactory::getApplication();
        $filter_quiz_id = $app->getUserStateFromRequest('questions.filter.quiz_id', 'filter_quiz_id', '');

        if (empty($filter_quiz_id)) {
            $filter_quiz_id = 0;
            $c_id = $app->input->get('c_id', 0);
            if ($c_id) {
                $db = JFactory::getDbo();
                $db->setQuery("SELECT `c_quiz_id` FROM #__quiz_t_question WHERE `c_id` = '" . $c_id . "'");
                try{
                    $filter_quiz_id = $db->loadResult();
                }
                catch (RuntimeException $e){
                    $app->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('ordering') . ' as ' . $db->quoteName('value'));
        $query->select('concat(ordering,". ",c_question) as ' . $db->quoteName('text'));
        $query->from($db->quoteName('#__quiz_t_question'));
        $query->where($db->quoteName('c_quiz_id') . ' = ' . $db->quote($filter_quiz_id));

        $query->order($db->quoteName('ordering') . ' ASC');
        $db->setQuery($query);
        $rows = array();
        try{
            $rows = $db->loadObjectlist();
        }
        catch (RuntimeException $e){
            $app->enqueueMessage($e->getMessage(), 'error');
        }

        $options = array();
        if (!empty($rows))
        {
            foreach ($rows as $row) {
                $row->text = strip_tags($row->text);
                array_push($options,$row);
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}