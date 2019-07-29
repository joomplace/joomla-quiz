<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die();

class JoomlaquizModelUserquiz extends JModelList
{
    public $current_quiz_name = '';
    private $assignedUsersIds = array();

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        $current_quiz_id = $this->getUserStateFromRequest('userquiz.userquiz_quiz_id', 'userquiz_quiz_id', 0);
        $this->setState('userquiz.userquiz_quiz_id', $current_quiz_id);

        $limit = $this->getUserStateFromRequest('userquiz.limit', 'limit', 20);
        $this->setState('list.limit', $limit);
        $app->input->set('limit', $limit);

        $start = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $start);

        parent::populateState('u.name', 'asc');
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id	.= ':'.$this->getState('userquiz.userquiz_quiz_id');
        return parent::getStoreId($id);
    }

    public function getQuizzes()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->qn('c_id', 'value'))
            ->select($db->qn('c_title', 'text'))
            ->from($db->qn('#__quiz_t_quiz'))
            ->where($db->qn('published') .'='. $db->q(1));
        $db->setQuery($query);
        $quizzes = array();

        $javascript = 'onchange="document.adminForm.submit();"';
        $quizzes[] = JHTML::_('select.option', '0', JText::_('COM_JOOMLAQUIZ_USERQUIZ_MODEL_SELECT_QUIZ') );
        $quizzes = array_merge( $quizzes, $db->loadObjectList() );

        $current_quiz_id = $app->getUserStateFromRequest('userquiz.userquiz_quiz_id', 'userquiz_quiz_id', 0);
        if($current_quiz_id){
            foreach ($quizzes as $quiz){
                if((int)$quiz->value == (int)$current_quiz_id){
                    $this->current_quiz_name = $quiz->text;
                    break;
                }
            }
        }

        $quizzes = JHTML::_('select.genericlist', $quizzes,'userquiz_quiz_id', 'class="text_area" style="max-width: 300px;" size="1" '. $javascript, 'value', 'text', $current_quiz_id );
        return $quizzes;
    }

    public function getQuizname()
    {
       return $this->current_quiz_name;
    }

    public function getUsers()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->qn('id', 'value'))
            ->select($db->qn('name', 'text'))
            ->from($db->qn('#__users'))
            ->where($db->qn('block') .'='. $db->q(0));
        $db->setQuery($query);
        $users = $db->loadObjectList();
        $users = JHTML::_('select.genericlist', $users, 'userquiz_users_ids[]', 'class="text_area" style="max-width:300px;" multiple="multiple"', 'value', 'text', $this->assignedUsersIds );
        return $users;
    }

    public function getAssigned()
    {
        $app = JFactory::getApplication();
        $current_quiz_id = $app->getUserStateFromRequest('userquiz.userquiz_quiz_id', 'userquiz_quiz_id', 0);

        $limit = (int) $this->getState('list.limit', 20);
        $limitstart = (int) $this->getState('list.start', 0);

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->qn(array('quq.user_id', 'quq.quiz_id', 'quq.notified')))
            ->from($db->qn('#__quiz_userquiz', 'quq'))
            ->where($db->qn('quq.quiz_id') .'='. $db->q($current_quiz_id));
        $db->setQuery($query);
        $allAssignedUsers = $db->loadObjectList();

        if($allAssignedUsers)
        {
            $this->assignedUsersIds = array();
            foreach ($allAssignedUsers as $user){
                $this->assignedUsersIds[] = $user->user_id;
            }
        }

        $query->select($db->qn('u.name'))
            ->leftJoin($db->qn('#__users', 'u') . ' ON ' . $db->qn('u.id') . '=' . $db->qn('quq.user_id'))
            ->where($db->qn('u.block') .'='. $db->q(0))
            ->setLimit($limit, $limitstart);
        $db->setQuery($query);
        $assignedUsers = $db->loadObjectList();

        return $assignedUsers;
    }

    public function getPagination()
    {
        $limit = (int) $this->getState('list.limit', 20);
        $limitstart = (int) $this->getState('list.start', 0);
        return new \JPagination(count($this->assignedUsersIds), $limitstart, $limit);
    }

    public function assign($data=array())
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        foreach ($data['userquiz_users_ids'] as $i=>$user_id)
        {
            $query->clear();
            $query->select($db->qn('id'))
                ->from($db->qn('#__quiz_userquiz'))
                ->where($db->qn('user_id') .'='. $db->q((int)$user_id))
                ->where($db->qn('quiz_id') .'='. $db->q((int)$data['userquiz_quiz_id']));
            $db->setQuery($query);
            if(!$db->loadResult())
            {
                $query->clear();
                $columns = array(
                    'user_id',
                    'quiz_id',
                    'notified'
                );
                $values = array(
                    $db->q((int)$user_id),
                    $db->q((int)$data['userquiz_quiz_id']),
                    $db->q(0)
                );
                $query->insert($db->qn('#__quiz_userquiz'))
                    ->columns($db->qn($columns))
                    ->values(implode(',', $values));
                $db->setQuery($query)
                    ->execute();
            }
        }

        return true;
    }

    public function unassign($data=array())
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        foreach ($data['userquiz_users_ids'] as $i=>$user_id)
        {
            $query->clear();
            $conditions = array(
                $db->qn('user_id') .'='. $db->q((int)$user_id),
                $db->qn('quiz_id') .'='. $db->q((int)$data['userquiz_quiz_id'])
            );
            $query->delete($db->qn('#__quiz_userquiz'))
                ->where($conditions);
            $db->setQuery($query)
                ->execute();
        }

        return true;
    }

    public function notify($data=array())
    {
        //ToDo
        return true;
    }
}