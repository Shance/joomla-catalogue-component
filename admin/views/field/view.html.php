<?php


defined('_JEXEC') or die;

JLoader::register('CatalogueHelper', JPATH_COMPONENT.'/helpers/catalogue.php');

class CatalogueViewField extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		// Since we don't track these assets at the item level, use the category id.
		$canDo		= CatalogueHelper::getActions($this->item->id, 0);

		JToolbarHelper::title($isNew ? JText::_('COM_CATALOGUE_MANAGER_FIELD_NEW') : JText::_('COM_CATALOGUE_MANAGER_FIELD_EDIT'));

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit'))){
			JToolbarHelper::apply('field.apply');
			JToolbarHelper::save('field.save');

			if ($canDo->get('core.create')) {
				JToolbarHelper::save2new('field.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolbarHelper::save2copy('field.save2copy');
		}

		if (empty($this->item->id))  {
			JToolbarHelper::cancel('field.cancel');
		}
		else {
			JToolbarHelper::cancel('field.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
	}
}
