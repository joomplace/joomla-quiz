<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die();

class JoomlaquizModelUsercategories extends JModelList
{
    public $current_category_name = '';
    private $assignedUsersIds = array();

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        $current_category_id = $this->getUserStateFromRequest('usercategories.usercategories_category_id', 'usercategories_category_id', 0);
        $this->setState('usercategories.usercategories_category_id', $current_category_id);

        $limit = $this->getUserStateFromRequest('usercategories.limit', 'limit', 20);
        $this->setState('list.limit', $limit);
        $app->input->set('limit', $limit);

        $start = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $start);

        parent::populateState('u.name', 'asc');
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id	.= ':'.$this->getState('usercategories.usercategories_category_id');
        return parent::getStoreId($id);
    }

    public function getCategories()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->qn('id', 'value'))
            ->select($db->qn('title', 'text'))
            ->from($db->qn('#__categories'))
            ->where($db->qn('extension') .'='. $db->q('com_joomlaquiz'))
            ->where($db->qn('published') .'='. $db->q(1));
        $db->setQuery($query);
        $categories = array();

        $javascript = 'onchange="document.adminForm.submit();"';
        $categories[] = JHTML::_('select.option', '0', JText::_('COM_JOOMLAQUIZ_USERCATEGORIES_MODEL_SELECT_CATEGORY') );
        $categories = array_merge( $categories, $db->loadObjectList() );

        $current_category_id = $app->getUserStateFromRequest('usercategories.usercategories_category_id', 'usercategories_category_id', 0);
        if($current_category_id){
            foreach ($categories as $category){
                if((int)$category->value == (int)$current_category_id){
                    $this->current_category_name = $category->text;
                    break;
                }
            }
        }

        $categories = JHTML::_('select.genericlist', $categories,'usercategories_category_id', 'class="text_area" style="max-width: 300px;" size="1" '. $javascript, 'value', 'text', $current_category_id );
        return $categories;
    }

    public function getCatname()
    {
       return $this->current_category_name;
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
        $users = JHTML::_('select.genericlist', $users, 'usercategories_users_ids[]', 'class="text_area" style="max-width:300px;" multiple="multiple"', 'value', 'text', $this->assignedUsersIds );
        return $users;
    }

    public function getAssigned()
    {
        $app = JFactory::getApplication();
        $current_category_id = $app->getUserStateFromRequest('usercategories.usercategories_category_id', 'usercategories_category_id', 0);

        $limit = (int) $this->getState('list.limit', 20);
        $limitstart = (int) $this->getState('list.start', 0);

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->qn(array('quc.user_id', 'quc.category_id', 'quc.notified')))
            ->from($db->qn('#__quiz_usercategories', 'quc'))
            ->where($db->qn('quc.category_id') .'='. $db->q($current_category_id));
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
            ->leftJoin($db->qn('#__users', 'u') . ' ON ' . $db->qn('u.id') . '=' . $db->qn('quc.user_id'))
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

        foreach ($data['usercategories_users_ids'] as $i=>$user_id)
        {
            $query->clear();
            $query->select($db->qn('id'))
                ->from($db->qn('#__quiz_usercategories'))
                ->where($db->qn('user_id') .'='. $db->q($user_id))
                ->where($db->qn('category_id') .'='. $db->q((int)$data['usercategories_category_id']));
            $db->setQuery($query);
            if(!$db->loadResult())
            {
                $query->clear();
                $columns = array(
                    'user_id',
                    'category_id',
                    'notified'
                );
                $values = array(
                    $db->q((int)$user_id),
                    $db->q((int)$data['usercategories_category_id']),
                    $db->q(0)
                );
                $query->insert($db->qn('#__quiz_usercategories'))
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

        foreach ($data['usercategories_users_ids'] as $i=>$user_id)
        {
            $query->clear();
            $conditions = array(
                $db->qn('user_id') .'='. $db->q((int)$user_id),
                $db->qn('category_id') .'='. $db->q((int)$data['usercategories_category_id'])
            );
            $query->delete($db->qn('#__quiz_usercategories'))
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