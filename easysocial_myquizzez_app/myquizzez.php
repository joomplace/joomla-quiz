<?php
/**
* @package		%PACKAGE%
* @subpackge	%SUBPACKAGE%
* @copyright	Copyright (C) 2010 - 2012 %COMPANY_NAME%. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*
* %PACKAGE% is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// We want to import our app library
Foundry::import( 'admin:/includes/apps/apps' );

/**
 * Some application for EasySocial. Take note that all classes must be derived from the `SocialAppItem` class
 *
 * Remember to rename the Textbook to your own element.
 * @since	1.0
 * @author	Author Name <author@email.com>
 */
class SocialUserAppMyQuizzez extends SocialAppItem
{
	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();
		JPlugin::loadLanguage('plg_app_user_myquizzez', JPATH_ADMINISTRATOR);
	}

	/**
	 * Triggers the preparation of stream.
	 *
	 * If you need to manipulate the stream object, you may do so in this trigger.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream object.
	 * @param	bool				Determines if we should respect the privacy
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		// You should be testing for app context
		if( $item->context !== 'appcontext' )
		{
			return;
		}
	}

	/**
	 * Triggers the preparation of activity logs which appears in the user's activity log.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream object.
	 * @param	bool				Determines if we should respect the privacy
	 */
	public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
	{
	}

	/**
	 * Triggers after a like is saved.
	 *
	 * This trigger is useful when you want to manipulate the likes process.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableLikes	The likes object.
	 *
	 * @return	none
	 */
	public function onAfterLikeSave( &$likes )
	{
	}

	/**
	 * Triggered when a comment save occurs.
	 *
	 * This trigger is useful when you want to manipulate comments.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableComments	The comment object
	 * @return
	 */
	public function onAfterCommentSave( &$comment )
	{
	}

	/**
	 * Renders the notification item that is loaded for the user.
	 *
	 * This trigger is useful when you want to manipulate the notification item that appears
	 * in the notification drop down.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad( $item )
	{
	}
	
}
