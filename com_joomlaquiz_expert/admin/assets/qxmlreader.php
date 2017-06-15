<?php defined('_JEXEC') or die;


/**
 * @license http://www.gnu.org/copyleft/lesser.html LGPL License
 **/

class QuizXMLReader
{

	var $reader;
	var $tag;
	var $canRun = false;

	function QuizXMLReader(  ){

		$this->canRun = class_exists('XMLReader');
		$this->reader = new XMLReader();
	}

	function setContents( $contents )
	{
		return $this->reader->XML( $contents );
	}

	function ignore_whitespace()
	{
		while ( $this->reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE )
			$this->reader->read();
	}

	// if $ignoreDepth == 1 then will parse just first level, else parse 2th level too
	/*
	 * Example:
	 * 			parseBlock('quiz_question'):
	 *
	 * 				<quiz_question>
						<question_text><![CDATA[]]></question_text>
						<question_image><![CDATA[]]></question_image>
						<question_info>
							<question_date></question_date>
							<question_rating></question_rating>
						</question_info>
					</quiz_question>

				$ignoreDepth = 1
				return
					array(
							[question_text]
							[question_image]
					)

				$ignoreDepth = 0
					return
						array(
							[question_text]
							[question_image]
							[question_info] = array (
												[question_date]
												[question_rating]
											)
						)
	 */
	function parseBlock( $name, $ignoreDepth = 1, $converToObject = false )
	{
		if ( $this->reader->name == $name && $this->reader->nodeType == XMLReader::ELEMENT )
		{
			$result = array();
			while ( !($this->reader->name == $name && $this->reader->nodeType == XMLReader::END_ELEMENT) )
			{
				//echo $this->reader->name. ' - '.$this->reader->nodeType." - ".$this->reader->depth."\n";
				switch($this->reader->nodeType){
					case 1:
						if ( $this->reader->depth > 3 && !$ignoreDepth )
						{
							$result[ $nodeName ] = ( isset($result[ $nodeName ]) ? $result[ $nodeName ] : array() );
							while ( !($this->reader->name == $nodeName && $this->reader->nodeType == XMLReader::END_ELEMENT) )
							{
								$resultSubBlock = $this->parseBlock($this->reader->name, 1);

								if ( !empty($resultSubBlock) )
									$result[ $nodeName ][] = $resultSubBlock;

								unset($resultSubBlock);
								$this->reader->read();
							}
						}
						$nodeName = $this->reader->name;
						if($this->reader->hasAttributes){
							$attributeCount = $this->reader->attributeCount;

							for($i = 0; $i < $attributeCount; $i++){
								$this->reader->moveToAttributeNo($i);
								$result[$this->reader->name] = $this->reader->value;
							}
							$this->reader->moveToElement();
						}
					break;

					case 3: case 4:
						$result[$nodeName] = $this->reader->value;
						$this->reader->read();
						break;
				}

				$this->reader->read();
			}

			if ( !$ignoreDepth && $converToObject )
			{
				foreach ( $result as $k => $v )
					if ( is_array($v) )
					{
						foreach ( $result[$k] as $kk => $vv )
							if ( is_array($vv) )
								$result[$k][$kk] = (object)$vv;
					}
			}

			return $result;
		}
	}

	// move cursor to tag
	function move_to_tag($tagName)
	{
		while( $this->reader->read() ) {
			if ( $this->reader->name == $tagName && $this->reader->nodeType == XMLReader::ELEMENT )
			{
				break;
			}
		}
	}

	/**
	 * get items from tag
	 * @param string $ListTag <p>parent tag</p>
	 * @param string $ElementTag <p>elements tag</p>
	 * @param string $ignoreDepth [optional] <p>true - parse 1 level, false - parse 2 levels</p>
	 * @param string $oneByOne [optional] <p>true - return one element, false - return all elements</p>
	 * @return (array) if $oneByOne is false , else (object)
	 */
	function quiz_getlist( $ListTag, $ElementTag, $ignoreDepth = 1 )
	{
		$list = array();
		while( $this->reader->read() ) {
			if ( $this->reader->name == $ListTag )
			{
				// while not found end tag read blocks
				while ( !($this->reader->name == $ListTag && $this->reader->nodeType == XMLReader::END_ELEMENT) )
				{
						$element = $this->parseBlock($ElementTag, $ignoreDepth);
						if ( !empty($element) )
							$list[] = (object)$element;

						unset($element);
						$this->reader->read();
				}
				break;
			}
		}

		return $list;
	}

	function quizess_pool()
	{
		$list = new stdClass();
		$list->quizzes_question_pool = array();
		$list->choice_data = array();
		$list->match_data = array();
		$list->blank_data = array();
		$list->blank_distr_data = array();
		$list->hotspot_data = array();

		while( $this->reader->read() ) {

			if ( $this->reader->name == 'quizess_pool' )
			{
				while ( $this->reader->name != 'quizess_poolos' )
					$this->reader->read();

				while ( !($this->reader->name == 'quizess_poolos' && $this->reader->nodeType == XMLReader::END_ELEMENT) )
				{
					$this->reader->read();
					$this->ignore_whitespace();

					if ( $this->reader->name == 'quizzes_question_pool' )
					{
						while ( !($this->reader->name == 'quizzes_question_pool' && $this->reader->nodeType == XMLReader::END_ELEMENT) )
						{
							$quiz_question = $this->parseBlock('quiz_question');

							if ( !empty($quiz_question) )
								$list->quizzes_question_pool[] = (object)$quiz_question;

							unset($quiz_question);
							$this->reader->read();
						}
					}

					if ( $this->reader->name == 'choice_data' )
					{
						while ( !($this->reader->name == 'choice_data' && $this->reader->nodeType == XMLReader::END_ELEMENT) )
						{
							$quest_choice = $this->parseBlock('quest_choice');
							if ( !empty($quest_choice) )
								$list->choice_data[] = (object)$quest_choice;

							unset($quest_choice);
							$this->reader->read();
						}
					}

					if ( $this->reader->name == 'match_data' )
					{
						while ( !($this->reader->name == 'match_data' && $this->reader->nodeType == XMLReader::END_ELEMENT) )
						{
							$quest_choice = $this->parseBlock('quest_match');
							if ( !empty($quest_choice) )
								$list->match_data[] = (object)$quest_choice;

							unset($quest_choice);
							$this->reader->read();
						}
					}

					if ( $this->reader->name == 'blank_data' )
					{
						while ( !($this->reader->name == 'blank_data' && $this->reader->nodeType == XMLReader::END_ELEMENT) )
						{
							$quest_choice = $this->parseBlock('quest_blank');
							if ( !empty($quest_choice) )
								$list->blank_data[] = (object)$quest_choice;

							unset($quest_choice);
							$this->reader->read();
						}
					}

					if ( $this->reader->name == 'blank_distr_data' )
					{
						while ( !($this->reader->name == 'blank_distr_data' && $this->reader->nodeType == XMLReader::END_ELEMENT) )
						{
							$quest_choice = $this->parseBlock('quest_blank_distr');
							if ( !empty($quest_choice) )
								$list->blank_distr_data[] = (object)$quest_choice;

							unset($quest_choice);
							$this->reader->read();
						}
					}

					if ( $this->reader->name == 'hotspot_data' )
					{
						while ( !($this->reader->name == 'hotspot_data' && $this->reader->nodeType == XMLReader::END_ELEMENT) )
						{
							$quest_choice = $this->parseBlock('quest_hotspot');
							if ( !empty($quest_choice) )
								$list->blank_distr_data[] = (object)$quest_choice;

							unset($quest_choice);
							$this->reader->read();
						}
					}


					if ( $this->reader->name == 'blank_distr_data' )
					{
						while ( !($this->reader->name == 'blank_distr_data' && $this->reader->nodeType == XMLReader::END_ELEMENT) )
						{
							$quest_choice = $this->parseBlock('quest_blank_distr');
							if ( !empty($quest_choice) )
								$list->blank_distr_data[] = (object)$quest_choice;

							unset($quest_choice);
							$this->reader->read();
						}
					}

				}

				$this->reader->read();
				$this->ignore_whitespace();
				$this->reader->read();

				break;
			}
		}

		return $list;
	}

}