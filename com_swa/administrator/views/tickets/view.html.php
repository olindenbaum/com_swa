<?php

// No direct access
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * View class for a list of Swa.
 */
class SwaViewTickets extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	protected $eventtickets;
	protected $users;

	/**
	 * Display the view
	 */
	public function display( $tpl = null ) {
		$this->state = $this->get( 'State' );
		$this->items = $this->get( 'Items' );
		$this->pagination = $this->get( 'Pagination' );

		require_once JPATH_COMPONENT . '/helpers/tablegetters.php';
		$this->eventtickets = SwaHelperTableGetter::getEventTickets();
		$this->users = SwaHelperTableGetter::getUsers();

		// Check for errors.
		if ( count( $errors = $this->get( 'Errors' ) ) ) {
			throw new Exception( implode( "\n", $errors ) );
		}

		SwaHelper::addSubmenu( 'tickets' );

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display( $tpl );
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since    1.6
	 */
	protected function addToolbar() {
		require_once JPATH_COMPONENT . '/helpers/swa.php';

		$state = $this->get( 'State' );
		$canDo = SwaHelper::getActions( $state->get( 'filter.category_id' ) );

		JToolBarHelper::title( JText::_( 'COM_SWA_TITLE_TICKETS' ), 'tickets.png' );

		//Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/ticket';
		if ( file_exists( $formPath ) ) {

			if ( $canDo->get( 'core.create' ) ) {
				JToolBarHelper::addNew( 'ticket.add', 'JTOOLBAR_NEW' );
			}

			if ( $canDo->get( 'core.edit' ) && isset( $this->items[0] ) ) {
				JToolBarHelper::editList( 'ticket.edit', 'JTOOLBAR_EDIT' );
			}
		}

		if ( $canDo->get( 'core.edit.state' ) ) {

			if ( isset( $this->items[0]->state ) ) {
				JToolBarHelper::divider();
				JToolBarHelper::custom( 'tickets.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true );
				JToolBarHelper::custom( 'tickets.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true );
			} else if ( isset( $this->items[0] ) ) {
				//If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList( '', 'tickets.delete', 'JTOOLBAR_DELETE' );
			}

			if ( isset( $this->items[0]->state ) ) {
				JToolBarHelper::divider();
				JToolBarHelper::archiveList( 'tickets.archive', 'JTOOLBAR_ARCHIVE' );
			}
			if ( isset( $this->items[0]->checked_out ) ) {
				JToolBarHelper::custom( 'tickets.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true );
			}
		}

		//Show trash and delete for components that uses the state field
		if ( isset( $this->items[0]->state ) ) {
			if ( $state->get( 'filter.state' ) == -2 && $canDo->get( 'core.delete' ) ) {
				JToolBarHelper::deleteList( '', 'tickets.delete', 'JTOOLBAR_EMPTY_TRASH' );
				JToolBarHelper::divider();
			} else if ( $canDo->get( 'core.edit.state' ) ) {
				JToolBarHelper::trash( 'tickets.trash', 'JTOOLBAR_TRASH' );
				JToolBarHelper::divider();
			}
		}

		if ( $canDo->get( 'core.admin' ) ) {
			JToolBarHelper::preferences( 'com_swa' );
		}

		//Set sidebar action - New in 3.0
		JHtmlSidebar::setAction( 'index.php?option=com_swa&view=tickets' );

		$this->extra_sidebar = '';

		JHtmlSidebar::addFilter(

			JText::_( 'JOPTION_SELECT_PUBLISHED' ),

			'filter_published',

			JHtml::_( 'select.options', JHtml::_( 'jgrid.publishedOptions' ), "value", "text", $this->state->get( 'filter.state' ), true )

		);

	}

	protected function getSortFields() {
		return array(
			'a.id' => JText::_( 'JGRID_HEADING_ID' ),
			'a.ordering' => JText::_( 'JGRID_HEADING_ORDERING' ),
			'a.state' => JText::_( 'JSTATUS' ),
			'a.checked_out' => JText::_( 'COM_SWA_TICKETS_CHECKED_OUT' ),
			'a.checked_out_time' => JText::_( 'COM_SWA_TICKETS_CHECKED_OUT_TIME' ),
		);
	}

}
