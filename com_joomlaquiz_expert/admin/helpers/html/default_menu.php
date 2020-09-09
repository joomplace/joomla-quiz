<?php
/**
* Joomlaquiz component for Joomla 3.0
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'administrator/components/com_joomlaquiz/assets/css/joomlaquiz.css');

?>
<div id="jp-navbar" class="navbar navbar-static navbar-inverse">
    <div class="navbar-inner">
        <div class="container" style="width: auto;">
            <a class="brand" href="<?php JRoute::_('index.php?option=com_joomlaquiz') ?>"><img class="jp-panel-logo" src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/joomplace-logo.png" /> <?php echo JText::_('COM_JOOMLAQUIZ_JOOMPLACE')?></a>
            <ul class="nav" role="navigation">
                <li class="dropdown">
                    <a id="control-panel" href="index.php?option=com_joomlaquiz&view=dashboard" role="button" class="dropdown-toggle"><?php echo JText::_('COM_JOOMLAQUIZ_CONTROL_PANEL')?></a>
                </li>
            </ul>
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse-joomlaquiz">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="nav-collapse-joomlaquiz nav-collapse collapse">
                <ul class="nav" role="navigation">
                <li class="dropdown">
                    <a href="#" id="drop-quiz-management" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_JOOMLAQUIZ_MENU_QUIZZES_MANAGEMENT') ?><b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="drop-quiz-management">
                        <li class="dropdown"><a href="index.php?option=com_categories&extension=com_joomlaquiz" role="button" class="dropdown-toggle"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_CATEGORY');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=quizzes"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_QUIZ');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=quizzes&layout=import_quizzes"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_IMPORT_QUIZZES');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=lpaths"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_LPATH');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_categories&extension=com_joomlaquiz.lpath"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_CATEGORIES_LPATH');?></a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" id="drop-questions-manage" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo  JText::_('COM_JOOMLAQUIZ_MENU_QUESTIONS_MANAGEMENT') ?><b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="drop-questions-manage">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_categories&extension=com_joomlaquiz.questions"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_QUESTIONS_CATEGORY');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=questions"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_QUEST');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=questions&quiz_id=0"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_QUESTIONS_POOL');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=questions&layout=uploadquestions"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_UPLOAD_QUEST');?></a></li>
                    </ul>
                </li>
				<li class="dropdown">
					<a href="#" id="drop-payments" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo  JText::_('COM_JOOMLAQUIZ_MENU_PAYMENTS') ?><b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="drop-payments">
						<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=payments"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_MANUAL_PAYMENTS');?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=reactivates"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_REACTIVATE_ACCESS');?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=products"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_QUIZ_PRODUCTS');?></a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" id="drop-settings" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo  JText::_('COM_JOOMLAQUIZ_MENU_SETTINGS') ?><b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="drop-settings">
						<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=templates"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_TEMPLATES');?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=certificates"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_CERTIFICATES');?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_config&view=component&component=com_joomlaquiz"><?php echo JText::_('COM_JOOMLAQUIZ_GLOBAL_SETTINGS');?></a></li>
						<!--<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=addons"></a></li>-->
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" id="drop-joomplace-reports" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo  JText::_('COM_JOOMLAQUIZ_MENU_REPORTS') ?><b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="drop-joomplace-reports">
						<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=results"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_VIEW_REPORTS');?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=statistic"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_VIEW_STATISTIC');?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_joomlaquiz&view=dynamic"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_VIEW_DYNAMIC');?></a></li>
					</ul>
				</li>
            </ul>
            <ul class="nav pull-right">
                <li id="fat-menu" class="dropdown">
                    <a href="#" id="help" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_JOOMLAQUIZ_MENU_HELP') ?><b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="help">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="https://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe-3.0/index.html" target="_blank"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_HELP') ?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://www.joomplace.com/support/helpdesk/" target="_blank"><?php echo JText::_('COM_JOOMLAQUIZ_ADMINISTRATION_SUPPORT_DESC') ?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create" target="_blank"><?php echo JText::_('COM_JOOMLAQUIZ_ADMINISTRATION_SUPPORT_REQUEST') ?></a></li>
                    </ul>
                </li>
            </ul>
          </div>
       </div>
    </div>
</div>