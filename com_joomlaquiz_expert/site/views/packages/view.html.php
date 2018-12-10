<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
 defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomlaquiz Deluxe Component
 */
class JoomlaquizViewPackages extends JViewLegacy
{
    public function display($tpl = null) 
    {
		$this->packages = $this->get('Packages');
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

        $load_vm_lang = false;
        foreach ($this->packages as $item){
            if(!empty($item->vm)){
                $load_vm_lang = true;
                break;
            }
        }
        if($load_vm_lang){
            JFactory::getLanguage()->load('com_virtuemart_orders', 'components/com_virtuemart');
        }


        parent::display($tpl);
    }
}
?>
