<?php

defined( '_JEXEC' ) or die( 'Unauthorized Access' );

?>

<?php

			$html = "";
			
			$html .= "<script type='text/javascript'>
						function myquizzes_initMenu() {
							jQuery('#myquizzes li table.myquizzes-table').hide();
							jQuery('#myquizzes li table.myquizzes-header').click(
								function() {								
									var checkElement = jQuery(this).next();								
									if((checkElement.is('table.myquizzes-table')) && (checkElement.is(':visible'))) { 
										checkElement.slideUp('slow');
										return false;
									}
									if((checkElement.is('table.myquizzes-table')) && (!checkElement.is(':visible'))) {
										jQuery('#myquizzes table.myquizzes-table:visible').slideUp('slow');
										checkElement.slideDown('slow');
										return false;
									}
								}
							);
						}
						jQuery(document).ready(function() {myquizzes_initMenu();});</script>";


			if(!empty($rows))
			{	
				$html .= '<ul id="myquizzes">';
				foreach($rows as $data)
				{			
					$html .= '<li>';	
					$text_limit = $params->get('limit', 50);				
					if(JString::strlen($data->introtext) > $text_limit)
					{
						$content = strip_tags(JString::substr($data->introtext, 0, $text_limit));
						$content .= " .....";
					}
					else
					{
						$content = $data->introtext;
					}		
							
					$data->text =& $content;

                    $date = Foundry::date($data->c_date_time)->toFormat();

					$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="myquizzes-header" style="margin: 0 0 5px;cursor: pointer;">';
					$html .= '	<tr style="border-bottom: solid 1px #ccc;">';
					$html .= '		<td height="20"><div class="myquizzes-title"><strong>'.$data->c_title.'</strong></div></td>';
					$html .= '		<td valign="top" width="200" style="text-align: right; font-weight: 700;" class="createdate">'.$date.'</td>';
					$html .= '	</tr>';
					
					$html .= '	<tr>';
					$html .= '		<td colspan="2">';
					if ( $introtext == 1 ) {
						$html .= '<div class="myquizzes-content">'.$content.'</div>';
					}
					$html .= '			<hr style="color: #ccc;" />';
					$html .= '		</td>';
					$html .= '	</tr>';
					$html .= '</table>';
					
					$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 0 0 5px;" class="myquizzes-table">';
					
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" width="150" class="mq_key">'.JText::_('PLG_MYQUIZZES_TEST_CATEGORY').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->c_category.'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_TEST_SCORE').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.JText::sprintf('PLG_MYQUIZZES_USER_SCORE', $data->c_total_score, $data->max_score, round($data->c_passing_score*$data->max_score/100), $data->max_score).'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_PERCENTITLE').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.(($data->total) ? round( (1 - $data->rank/$data->total)*100 ) : 0).' ('.JText::sprintf('PLG_MYQUIZZES_BETTER_THAN', (($data->total) ? round( (1 - $data->rank/$data->total)*100 ) : 0)).')</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_RANK').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->rank.' ('.JText::sprintf('PLG_MYQUIZZES_RANKED', $data->rank, $data->total).')</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_DURATION').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.str_pad(floor($data->c_total_time / 60), 2, "0", STR_PAD_LEFT).":".str_pad($data->c_total_time % 60, 2, "0", STR_PAD_LEFT).' ('.JText::sprintf('PLG_MYQUIZZES_ALLOWED_TIME', $data->c_time_limit).')</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_PLACE').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->rank.'</td>';
					$html .= '</tr>';
					
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" align="left" colspan="2"><h4>'.JText::_('PLG_MYQUIZZES_TEST_STATISTICS').'</h4></td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_NUMBER_OF_TEST').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->total_tries.'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::_('PLG_MYQUIZZES_NUMBER_PROVIDERS').':</td>';
					$html .= '	<td valign="top" class="mq_value">'.$data->total_passed.'</td>';
					$html .= '</tr>';
					$html .= '<tr class="sectiontableentry1">';
					$html .= '	<td valign="top" class="mq_key">'.JText::sprintf('PLG_MYQUIZZES_AVERAGE_SCORE', $data->total).':</td>';
					$html .= '	<td valign="top" class="mq_value">'.number_format($data->total_score_avg, 2, '.', ',').'</td>';
					$html .= '</tr>';

					
					$html .= '</table>';
					$html .= '</li>';
			
				}
				$html .= '</ul>';

                if($app === 1){
					jimport('joomla.html.pagination');
					$pagination	= new JPagination( $total , $limitstart , $limit );
					$html .= '
					<!-- Pagination -->
					<div class="row-fluid">
                      <div class="span6 offset4">
                        <div class="pagination">
                            '.$pagination->getPagesLinks().'
                        </div>
                       </div>
                    </div>
					<!-- End Pagination -->';
                    //$html .= "<div style='float:right;'><a href='".$showall."'>".JText::_('CC_SHOW_ALL')."</a></div>";
				}else{
					$apps_id = JFactory::getApplication()->input->get('id');
					$Itemid = JFactory::getApplication()->input->get('Itemid');

                    $showall = JRoute::_('index.php?option=com_easysocial&view=apps&userid='.$userid.'&id='.$apps_id.':myquizzez&layout=canvas&Itemid='.$Itemid.'&task=app');
                    $html .= '
                    <div class="row-fluid">
                       <div class="span2 offset5">
                        <div style="text-align: center;"><a href="'.$showall.'">'.JText::_("CC_SHOW_ALL").'</a></div>
                       </div>
                    </div>';
				}
			}else{
				$html .= "<div>".JText::_("PLG_MYQUIZZES_NO_QUIZZES")."</div>";
			}	
			
			$html .= "<div style='clear:both;'></div>";
	
	echo $html;
	
?>