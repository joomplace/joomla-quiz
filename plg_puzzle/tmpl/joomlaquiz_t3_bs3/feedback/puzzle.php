<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaquiz Deluxe class
 */
class JoomlaquizViewFeedbackPuzzle
{
    public static function getFeedbackContent($feedback_data, $data){

        $tag = JFactory::getLanguage()->getTag();
        $lang = JFactory::getLanguage();
        $lang->load('com_joomlaquiz', JPATH_SITE, $tag, true);

        $jq_tmpl_html = "<table align='left' class='jq_puzzle_fdb' width='100%'>" . "\n";
        $jq_tmpl_html .= "<tr><td><div class='jq_puzzle_img' style='width:100%;position:relative;'>";

        if(file_exists(JPATH_SITE.'/images/joomlaquiz/images/'.$data['qdata'][0]['q_data']->c_image)){
            $img_data = getimagesize(JPATH_SITE.'/images/joomlaquiz/images/'.$data['qdata'][0]['q_data']->c_image);
        }

        $scale = 1.5;
        $pieceWidth = round(($img_data[0] / $data['qdata'][0]['difficulty']) / $scale);
        $pieceHeight = round( ($img_data[1] / $data['qdata'][0]['difficulty']) / $scale);
        $puzzleWidth = $pieceWidth * $data['qdata'][0]['difficulty'];
        $puzzleHeight = $pieceHeight * $data['qdata'][0]['difficulty'];

        $xPos = 0;
        $yPos = 0;

        $jq_tmpl_html .= "<img class='feedback-puzzle__img' src='".JURI::root()."images/joomlaquiz/images/".$data['qdata'][0]['q_data']->c_image."' width='".$puzzleWidth."' height='".$puzzleHeight."' style='position:absolute;top:0px;left:0px;' data-difficulty='".$data['qdata'][0]['difficulty']."' >";
        $cc = 1;
        for($n = 0; $n < $data['qdata'][0]['difficulty']; $n++){
            for($m = 0; $m < $data['qdata'][0]['difficulty']; $m++){
                $bkg = (!in_array($cc, $feedback_data)) ? 'background: #eeeeee;filter:alpha(opacity=70);-moz-opacity:.7; opacity:.7;' : 'filter:alpha(opacity=0);-moz-opacity:0; opacity:0;';
                $jq_tmpl_html .= "<div class='feedback-puzzle__piece' style='".$bkg." width:".$pieceWidth."px; height:".$pieceHeight."px;float:left;'><!--x--></div>";
                $xPos += $pieceWidth;
                if($xPos >= $puzzleWidth){
                    $xPos = 0;
                    $yPos += $pieceHeight;
                }
                $cc++;
            }
            $jq_tmpl_html .= "<div style='clear:both;'><!--x--></div>";
        }

        $jq_tmpl_html .= "</div></td></tr>";

        $jq_tmpl_html .= "<tr><td><strong>".JText::_('COM_QUIZ_RES_MES_SCORE')."</strong>&nbsp;".$data['qdata'][0]['score']."&nbsp;<strong>".JText::_('COM_QUIZ_ELAPSED_TIME')."</strong>&nbsp;".$data['qdata'][0]['elapsed_time']." sec</td></tr>";

        $jq_tmpl_html .= "</table>" . "\n";
        return $jq_tmpl_html;
    }
}

?>