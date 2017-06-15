<?php defined('_JEXEC') or die;
/**
* JoomlaQuiz component for Joomla
* @version $Id: manual_faq.php 2009-11-16 17:30:15
* @package JoomlaQuiz
* @subpackage manual_faq.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html	
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div style="text-align:left; padding:5px; font-family: verdana, arial, sans-serif; font-size: 9pt;">

<h3><b>Below are the answers to frequently asked questions:</b></h3>

<div style="padding-left:10px ">
<h3><b>How can I create audio/video questions?</b></h3>
<div style="padding-left:10px ">
<p>
First of all you need to upload and install some kind of video/audio plugin (for example "JPlayer" <a href="http://extensions.joomla.org/extensions/multimedia/video-players-a-gallery/11572">http://extensions.joomla.org/extensions/multimedia/video-players-a-gallery/11572</a>)
Then read carefully about this plugin's options. 
After doing that you can insert an audio question (for instance {mp3}filename{/mp3}) or a video question (for example {mpg}filename{/mpg}) into the content (question title).
To make working with this mambot using the WYSIWYG editor more convenient you may use the JCE plugin:  http://www.joomplace.com/allvideo-jce-plugin/allvideo-jce-plugin.html
</p>
</div>
</div>

<div style="padding-left:10px ">
<h3><b>Solutions how to solve possible difficulties that can be met while creating '<em>HotSpot</em>' question.</b></h3>
<div style="padding-left:10px ">
<p>
&nbsp;-&nbsp;&nbsp;In old versions of Joomla! you can not open the '<em>Upload image</em>' popup-window, because '<em>You are not authorized</em>' error  occurs. <strong>Solution</strong>: Update your Joomla.<br />
&nbsp;-&nbsp;&nbsp;In Front-end HotSpot mark doesn't display correctly (with some offset). <strong>Notice</strong>: The problem occurs because of complex Joomla! template structure. No solution yet.<br />
&nbsp;-&nbsp;&nbsp;The popup Window for creating HotSpot Area in Back-end opens with an error or with a wrong image. <strong>Solution</strong>: After selecting an image it is NECESSARY to click '<em>Apply</em>' button (to re-save the question) and then go and create the Hotspot Area.
</p>
</div>
</div>



<div style="padding-left:10px ">
<h3><b>How do I create a new quiz?</b></h3>
<div style="padding-left:10px ">
<p>At first create a new category for the quiz. To do this choose '<em>JoomlaQuiz->Quiz Categories</em>' menu item, press the '<em>New</em>' button, input the category's name and description (optional). Press the '<em>Save</em>' button. After that, click on the new category's name to view quizzes assigned to that category. Then press the '<em>New</em>' button to create a new quiz and do the following:<br>
&nbsp;-&nbsp;&nbsp;Input the title, the author and the description of the newly created quiz.<br>
&nbsp;-&nbsp;&nbsp;Choose a category.<br>
&nbsp;-&nbsp;&nbsp;Choose a template and a language file.<br>
&nbsp;-&nbsp;&nbsp;Enter the time limit (the maximum time available to pass the quiz).<br>
&nbsp;-&nbsp;&nbsp;Enter the time limit stating when a user can do the same quiz again (time period a user should wait before he can start the same quiz).<br>
&nbsp;-&nbsp;&nbsp;Enter the passing score (percent).<br>
&nbsp;-&nbsp;&nbsp;Enable/disable additional options.<br>
&nbsp;-&nbsp;&nbsp;Redefine some text messages as necessary.<br>
</p>
</div>
</div>
<div style="padding-left:10px ">
<h3><b>How do I create a menu item for the quiz?</b></h3>
<div style="padding-left:10px ">
<p>To add an item to your menu use normal Joomla Menu Manager:<br>
&nbsp;-&nbsp;&nbsp;Click '<em>New</em>'.<br>
&nbsp;-&nbsp;&nbsp;Select 'Component' from the '<em>Components</em>' section.<br>
&nbsp;-&nbsp;&nbsp;Choose JoomlaQuiz component.<br>
&nbsp;-&nbsp;&nbsp;Click '<em>Save</em>'.<br>
After that, go to '<em>JoomlaQuiz->Menu manager</em>' and assign the new menu item for the correct quiz (by doing that you add the parameters to the menu item automatically).<br>
To add parameters to menu item manually go to the quizzes' page, navigate the mouse on the quiz, the quiz ID will be indicated, remember it and go to the menu manager to edit your menu item. In the parameters section for menu item type in '<em>quiz_id=X</em>', change '<em>X</em>' to quiz ID that you will see in a floating window for the quiz.
</p>
</div>
</div>
<div style="padding-left:10px ">
<h3><b>How do I set user access rights for the quiz?</b></h3>
<div style="padding-left:10px ">
<p>By default only registered users have access to the quiz. You can enable option '<em>guest access</em>' for the quiz in order to allow public users (guests) to take it.
</p>
</div>
</div>
<div style="padding-left:10px ">
<h3><b>In the previous version of the component I could upload a picture for the question. How can I assign a picture to the question in the new version of the component?</b></h3>
<div style="padding-left:10px ">
<p>In the new version the question text is entered using WISIWYG editor. Using that editor you can add pictures as well.</p>
</div>
</div>
<div style="padding-left:10px ">
<h3><b>How do I create a link for the quiz without the menu item?</b></h3>
<div style="padding-left:10px ">
<p>The link for quiz must look like this: '<em>index.php?option=com_joomlaquiz&amp;quiz_id=X</em>' (you can learn what <em>quiz_id</em> is from the previous topic).</p>
</div>
</div>
<br>
<div style="padding-left:10px ">
<h3><b>How do I install a new update for the JoomlaQuiz component?</b></h3>
<div style="padding-left:10px ">
<p>Firstly, uninstall your version of the component, all data will be kept in your database.<br />
Then install the new version of component. The new component will upgrade your database structure if necessary, and all your data will be correctly moved to the new version. 
</p>
</div>
</div>

</div>
