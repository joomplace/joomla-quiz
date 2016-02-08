<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted Access');

$tag = JFactory::getLanguage()->getTag();
$lang = JFactory::getLanguage();
$lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);

$article = $article_data->article;
$params = $article_data->params;
$user = $article_data->user;
$print = $article_data->print;
$access = $article_data->access;

$document 	= JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_joomlaquiz/views/templates/tmpl/joomlaquiz_standard/css/jq_template.css');
?>	

<?php if ($params->get('show_page_heading', 1)) : ?>
<h1>
<?php echo $params->get('page_heading'); ?>
</h1>
<?php endif; ?>

<?php if ($params->get('show_title')) : ?>
<h2>
	<?php if ($params->get('link_titles') && !empty($article->readmore_link)) : ?>
	<a href="<?php echo $article->readmore_link; ?>">
			<?php echo $article->title; ?></a>
	<?php else : ?>
			<?php echo $article->title; ?>
	<?php endif; ?>
</h2>
<?php endif; ?>

<?php if ($params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
<ul class="actions">
<?php if (!$print) : ?>
	<?php if ($params->get('show_print_icon')) : ?>
	<li class="print-icon">
			<?php echo JHtml::_('icon.print_popup',  $article, $params); ?>
	</li>
	<?php endif; ?>

	<?php if ($params->get('show_email_icon')) : ?>
	<li class="email-icon">
			<?php echo JHtml::_('icon.email',  $article, $params); ?>
	</li>
	<?php endif; ?>
<?php else : ?>
	<li>
			<?php echo JHtml::_('icon.print_screen',  $article, $params); ?>
	</li>
<?php endif; ?>
</ul>
<?php endif; ?>

<?php  if (!$params->get('show_intro')) :
	echo $article->event->afterDisplayTitle;
endif; ?>

<?php echo $article->event->beforeDisplayContent; ?>

<?php $useDefList = (($params->get('show_author')) OR ($params->get('show_category')) OR ($params->get('show_parent_category'))
OR ($params->get('show_create_date')) OR ($params->get('show_modify_date')) OR ($params->get('show_publish_date'))
OR ($params->get('show_hits'))); ?>

<?php if ($useDefList) : ?>
<dl class="article-info">
<dt class="article-info-term"><?php  echo JText::_('COM_CONTENT_ARTICLE_INFO'); ?></dt>
<?php endif; ?>
<?php if ($params->get('show_parent_category') && $article->parent_slug != '1:root') : ?>
<dd class="parent-category-name">
<?php	$title = $article->parent_title;
		$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($article->parent_slug)).'">'.$title.'</a>';?>
<?php if ($params->get('link_parent_category') AND $article->parent_slug) : ?>
	<?php echo JText::sprintf('COM_CONTENT_PARENT', $url); ?>
	<?php else : ?>
	<?php echo JText::sprintf('COM_CONTENT_PARENT', $title); ?>
<?php endif; ?>
</dd>
<?php endif; ?>
<?php if ($params->get('show_category')) : ?>
<dd class="category-name">
<?php 	$title = $article->category_title;
		$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($article->catslug)).'">'.$title.'</a>';?>
<?php if ($params->get('link_category') AND $article->catslug) : ?>
	<?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
	<?php else : ?>
	<?php echo JText::sprintf('COM_CONTENT_CATEGORY', $title); ?>
<?php endif; ?>
</dd>
<?php endif; ?>
<?php if ($params->get('show_create_date')) : ?>
<dd class="create">
<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHTML::_('date',$article->created, JText::_('DATE_FORMAT_LC2'))); ?>
</dd>
<?php endif; ?>
<?php if ($params->get('show_modify_date')) : ?>
<dd class="modified">
<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHTML::_('date',$article->modified, JText::_('DATE_FORMAT_LC2'))); ?>
</dd>
<?php endif; ?>
<?php if ($params->get('show_publish_date')) : ?>
<dd class="published">
<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE', JHTML::_('date',$article->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
</dd>
<?php endif; ?>
<?php if ($params->get('show_author') && !empty($article->author )) : ?>
<dd class="createdby"> 
<?php $author =  $article->author; ?>
<?php $author = ($article->created_by_alias ? $article->created_by_alias : $author);?>

<?php if (!empty($article->contactid ) &&  $params->get('link_author') == true):?>
	<?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' , 
	 JHTML::_('link',JRoute::_('index.php?option=com_contact&view=contact&id='.$article->contactid),$author)); ?>

<?php else :?>
	<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
<?php endif; ?>
</dd>
<?php endif; ?>	
<?php if ($params->get('show_hits')) : ?>
<dd class="hits">
<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $article->hits); ?>
</dd>
<?php endif; ?>
<?php if ($useDefList) : ?>
</dl>
<?php endif; ?>

<?php if (isset ($article->toc)) : ?>
	<?php echo $article->toc; ?>
<?php endif; ?>

<?php echo $article->text; ?>

<?php echo $article->event->afterDisplayContent; ?>


<table class="jq_footer_container" cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>	
	<td class="sectiontableheader">
	<div class='jq_footer_link jq_lpath'><a href="<?php echo JRoute::_("index.php?option=com_joomlaquiz&view=lpath&".($article->rel_id? "package_id=".$article->package_id."&rel_id=".$article->rel_id: "lpath_id=".$article->lid).JoomlaquizHelper::JQ_GetItemId() ); ?>"><?php echo JText::_('COM_LPATH_QUIZZES_LIST'); ?></a></div>
	<?php if($article->next) {?>
	<div class='jq_footer_link jq_nquiz'><a href="<?php echo JRoute::_("index.php?option=com_joomlaquiz&view=quiz&".($article->rel_id? "package_id=".$article->package_id."&rel_id=".$article->rel_id: "lid=".$article->lid) . $article->next.JoomlaquizHelper::JQ_GetItemId()); ?>"><?php echo JText::_('COM_LPATH_NEXT_QUIZ'); ?></a></div>
	<?php } ?>
	</td>
</tr>
</table>