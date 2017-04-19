<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

JPluginHelper::importPlugin('joomlaquiz');

/**
 * Joomlaquiz Deluxe component helper.
 */
abstract class plgJoomlaquizQuestion extends JPlugin
{
	var $_type		= 'joomlaquiz';

	abstract public function onScoreByCategory(&$data);

    protected function onGetResultPdf($question, $result, &$pdf, $score, $total, $pdf_doc, $i){
        /** @var TCPDF $pdf */
        $fontFamily = $pdf->getFontFamily();
        $pdf->setFont($fontFamily, 'B');
        $pdf->writeHTML(($i + 1) . ".[" . number_format($score,1) . '/' . number_format($total,1) . "]", true);
    }

    protected function onPrintResult($question, $result, $score, $total, $i){
        echo ($i + 1) . ".[" . number_format($score,1) . '/' . number_format($total,1) . "]";
        echo '<br/>';
    }
		
}