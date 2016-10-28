<?php
/**
 * Finder Plugin for Joomla! - Song
 *
 * @author     Jisse Reitsma <jisse@yireo.com>
 * @copyright  Copyright 2014 Jisse Reitsma
 * @license    GNU Public License version 3 or later
 * @link       http://www.yireo.com/books/
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

use Joomla\Utilities\ArrayHelper;
/**
 * Class PlgFinderQuiz
 */
class PlgFinderJoomlaquiz extends FinderIndexerAdapter
{
	/**
	 * @var string
	 */
	protected $context = 'com_joomlaquiz';

	/**
	 * @var string
	 */
	protected $extension = 'com_joomlaquiz';

	/**
	 * @var string
	 */
	protected $type_title = 'Joomlaquiz';

	/**
	 * @var string
	 */
	protected $table = '#__quiz_t_quiz';

	/**
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	protected $state_field = 'c_published';

	//protected $type_id = 'c_id';

	/**
	 * Override method to index a certain result
	 *
	 * @param   FinderIndexerResult  $item    Finder item
	 * @param   string               $format  Formatting (html or text)
	 *
	 * @return  null
	 */
	protected function index(FinderIndexerResult $item, $format = 'html')
	{
		/*
		if (JComponentHelper::isEnabled($this->extension) == false)
		{
		    return;
		}
		*/

		// Prepare the item
		$item->access = 1;

		// Define these items as songs
		$item->addTaxonomy('Type', 'Joomlaquiz');

		// Add artist information
		//$item->addInstruction(FinderIndexer::META_CONTEXT, 'artist');
		//$item->addTaxonomy('Artist', $item->artist);

		// Set language
		//$item->setLanguage();
		//$item->addTaxonomy('Language', $item->language);

		//$item->id = $item->c_id;
		//unset($item->c_id);
		//$item->title = $item->c_title;
		//unset($item->c_title);
		///$item->description = $item->c_description;
		//unset($item->c_description);

		// Set URLs
		$item->route = 'index.php?option=com_joomlaquiz&view=quiz&quiz_id=' . $item->id;
		$item->url = $item->route;
		$item->path = FinderIndexerHelper::getContentPath($item->route);
		$item->state = $item->published;

		// Allow others to hook into our $item as well
		FinderIndexerHelper::getContentExtras($item);

		$this->indexer->index($item);
	}

	/**
	 * Override method to setup the entire plugin process
	 *
	 * @return bool
	 */
	protected function setup()
	{
		//require_once JPATH_SITE.'/components/com_music/helpers/route.php';

		return true;
	}

	/**
	 * Override method to return the list query
	 *
	 * @param   mixed  $query  JDatabaseQuery object or null
	 *
	 * @return null
	 */
	protected function getListQuery($query = null)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('a.c_id as id, a.c_title as title, a.c_description as description, a.published');
		//$query->select('a.c_id, a.c_title, a.c_description, a.published');
		//$query->select('p.name AS artist');
		$query->from($db->quoteName($this->table, 'a'));
		//$query->innerJoin($db->quoteName('#__music_artists', 'p')
		//	. ' ON (' . $db->quoteName('a.artist_id') . '=' . $db->quoteName('p.id') . ')');

		//$debugQuery = str_replace('#__', $db->getPrefix(), trim($query));
		//echo "[SONGS]\n" . $debugQuery . "\n[/SONGS]\n";

		return $query;
	}

	/**
	 * Override method to return the state query
	 *
	 * @return mixed
	 */
	protected function getStateQuery()
	{
		$query = $this->db->getQuery(true);
		$query->select('a.c_id');
		$query->select('a.' . $this->state_field . ' AS state');
		$query->from($this->table . ' AS a');

		jimport('joomla.log.log');
		JLog::add($query, JLog::WARNING, 'jerror');

		return $query;
	}


	/**
	 * Event method to run when the item state changes
	 *
	 * @param   text   $context  String describing the current context
	 * @param   array  $pks      List of primary keys
	 * @param   mixed  $value    State value (likely either 0 or 1)
	 *
	 * @return null
	 */
	public function onFinderChangeState($context, $pks, $value)
	{

		

		if ($context == 'com_joomlaquiz.quiz')
		{
			$this->itemStateChange($pks, $value);
		}

		if ($context == 'com_plugins.plugin' && $value === 0)
		{
			$this->pluginDisable($pks);
		}
	}

	/**
	 * Event method run when the category state changes
	 *
	 * @param   string  $extension  String pointing to the component
	 * @param   array   $pks        Array of primary keys
	 * @param   mixed   $value      State value
	 *
	 * @return null
	 */
	public function onFinderCategoryChangeState($extension, $pks, $value)
	{
		if ($extension == 'com_joomlaquiz')
		{
			$this->categoryStateChange($pks, $value);
		}
	}

	/**
	 * Event method run when the item is deleted
	 *
	 * @param   string  $context  String describing the current context
	 * @param   JTable  $table    JTable instance of the content item
	 *
	 * @return bool
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ($context == 'com_joomlaquiz.quiz')
		{
			$id = $table->id;
		}
		else
		{
			return true;
		}

		return $this->remove($id);
	}

	/**
	 * Event method run after the item is saved
	 *
	 * @param   string  $context  String describing the current context
	 * @param   object  $item     Content item that is being saved
	 * @param   bool    $isNew    Flag determining whether this item is new or not
	 *
	 * @return null
	 */
	public function onFinderAfterSave($context, $item, $isNew)
	{

		if ($context == 'com_joomlaquiz.quiz')
		{
			if (!$isNew && $this->old_access != $item->access)
			{
				$this->itemAccessChange($item);
			}
			
			$this->reindex($item->c_id);
		}

		if ($context == 'com_categories.category')
		{
			if (!$isNew && $this->old_cataccess != $item->access)
			{
				$this->categoryAccessChange($item);
			}
		}
	}

	/**
	 * Event method run before the item is saved
	 *
	 * @param   string  $context  String describing the current context
	 * @param   object  $item     Content item that is being saved
	 * @param   bool    $isNew    Flag determining whether this item is new or not
	 *
	 * @return null
	 */
	public function onFinderBeforeSave($context, $item, $isNew)
	{
		if ($context == 'com_joomlaquiz.quiz' && $isNew == false)
		{
			$this->checkItemAccess($item);
		}

		if ($context == 'com_categories.category' && $isNew == false)
		{
			$this->checkCategoryAccess($item);
		}
	}

	/**
	 * Method to get a content item to index.
	 *
	 * @param   integer  $id  The id of the content item.
	 *
	 * @return  FinderIndexerResult  A FinderIndexerResult object.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function getItem($id)
	{
		// Get the list query and add the extra WHERE clause.
		$query = $this->getListQuery();
		$query->where('a.c_id = ' . (int) $id);

		// Get the item to index.
		$this->db->setQuery($query);
		$row = $this->db->loadAssoc();

		// Convert the item to a result object.
		$item = ArrayHelper::toObject((array) $row, 'FinderIndexerResult');

		// Set the item type.
		$item->type_id = $this->type_id;

		// Set the item layout.
		$item->layout = $this->layout;

		return $item;
	}
}
