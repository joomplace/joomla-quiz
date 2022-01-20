<?php
/**
* Joomlaquiz component for Joomla 4
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

$app = JFactory::getApplication();

$wa = JFactory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('joomlaquiz', JURI::root().'administrator/components/com_joomlaquiz/assets/css/joomlaquiz.css?v='.JoomlaquizHelper::getVersion());

?>
<nav class="navbar navbar-expand-lg navbar-light bg-light j4-jp-menu">
    <div class="container-fluid">
        <a class="navbar-brand" href="https://www.joomplace.com/" target="_blank" rel="noopener noreferrer">
            <img class="jp-panel-logo j4" src="<?php echo JURI::root(); ?>administrator/components/com_joomlaquiz/assets/images/joomplace-logo.png" />
            <?php echo JText::_('COM_JOOMLAQUIZ_JOOMPLACE')?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?option=com_joomlaquiz&view=dashboard"><?php echo JText::_('COM_JOOMLAQUIZ_CONTROL_PANEL')?></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="drop-quiz-management" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo JText::_('COM_JOOMLAQUIZ_MENU_QUIZZES_MANAGEMENT') ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="drop-quiz-management">
                        <li><a class="dropdown-item" href="index.php?option=com_categories&extension=com_joomlaquiz"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_CATEGORY');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=quizzes"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_QUIZ');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=quizzes&layout=import_quizzes"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_IMPORT_QUIZZES');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=lpaths"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_LPATH');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_categories&extension=com_joomlaquiz.lpath"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_CATEGORIES_LPATH');?></a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="drop-questions-manage" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo JText::_('COM_JOOMLAQUIZ_MENU_QUESTIONS_MANAGEMENT') ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="drop-questions-manage">
                        <li><a class="dropdown-item" href="index.php?option=com_categories&extension=com_joomlaquiz.questions"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_QUESTIONS_CATEGORY');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=questions"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_SETUP_QUEST');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=questions&quiz_id=0"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_QUESTIONS_POOL');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=questions&layout=uploadquestions"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_UPLOAD_QUEST');?></a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="drop-payments" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo JText::_('COM_JOOMLAQUIZ_MENU_PAYMENTS') ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="drop-payments">
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=payments"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_MANUAL_PAYMENTS');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=reactivates"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_REACTIVATE_ACCESS');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=products"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_QUIZ_PRODUCTS');?></a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="drop-settings" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo JText::_('COM_JOOMLAQUIZ_MENU_SETTINGS') ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="drop-settings">
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=templates"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_TEMPLATES');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=certificates"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_CERTIFICATES');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_config&view=component&component=com_joomlaquiz"><?php echo JText::_('COM_JOOMLAQUIZ_GLOBAL_SETTINGS');?></a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="drop-joomplace-reports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo JText::_('COM_JOOMLAQUIZ_MENU_REPORTS') ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="drop-joomplace-reports">
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=results"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_VIEW_REPORTS');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=statistic"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_VIEW_STATISTIC');?></a></li>
                        <li><a class="dropdown-item" href="index.php?option=com_joomlaquiz&view=dynamic"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_VIEW_DYNAMIC');?></a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="help" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo JText::_('COM_JOOMLAQUIZ_MENU_HELP') ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="help">
                        <li><a class="dropdown-item" href="https://www.joomplace.com/video-tutorials-and-documentation/joomla-quiz-deluxe-3.0/index.html" target="_blank" rel="noopener noreferrer"><?php echo JText::_('COM_JOOMLAQUIZ_SUBMENU_HELP'); ?></a></li>
                        <li><a class="dropdown-item" href="https://www.joomplace.com/support/helpdesk/" target="_blank" rel="noopener noreferrer"><?php echo JText::_('COM_JOOMLAQUIZ_ADMINISTRATION_SUPPORT_DESC');?></a></li>
                        <li><a class="dropdown-item" href="https://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create" target="_blank" rel="noopener noreferrer"><?php echo JText::_('COM_JOOMLAQUIZ_ADMINISTRATION_SUPPORT_REQUEST');?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>