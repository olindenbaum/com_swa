<?php

// No direct access
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * View class for a list of Swa.
 */
class SwaViewTeamresults extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	protected $universities;

	/**
	 * Display the view
	 */
	public function display( $tpl = null ) {
		$this->state = $this->get( 'State' );
		$this->items = $this->get( 'Items' );
		$this->pagination = $this->get( 'Pagination' );

		require_once JPATH_COMPONENT . '/helpers/tablegetters.php';
		$this->universities = SwaHelperTableGetter::getUniversities();

		// Check for errors.
		if ( count( $errors = $this->get( 'Errors' ) ) ) {
			throw new Exception( implode( "\n", $errors ) );
		}

		SwaHelper::addSubmenu( 'teamresults' );

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

		JToolBarHelper::title( JText::_( 'COM_SWA_TITLE_TEAMRESULTS' ), 'teamresults.png' );

		//Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/teamresult';
		if ( file_exists( $formPath ) ) {

			if ( $canDo->get( 'core.create' ) ) {
				JToolBarHelper::addNew( 'teamresult.add', 'JTOOLBAR_NEW' );
			}

			if ( $canDo->get( 'core.edit' ) && isset( $this->items[0] ) ) {
				JToolBarHelper::editList( 'teamresult.edit', 'JTOOLBAR_EDIT' );
			}
		}

		if ( $canDo->get( 'core.edit.state' ) ) {

			if ( isset( $this->items[0]->state ) ) {
				JToolBarHelper::divider();
				JToolBarHelper::custom( 'teamresults.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true );
				JToolBarHelper::custom( 'teamresults.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true );
			} else if ( isset( $this->items[0] ) ) {
				//If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList( '', 'teamresults.delete', 'JTOOLBAR_DELETE' );
			}

			if ( isset( $this->items[0]->state ) ) {
				JToolBarHelper::divider();
				JToolBarHelper::archiveList( 'teamresults.archive', 'JTOOLBAR_ARCHIVE' );
			}
			if ( isset( $this->items[0]->checked_out ) ) {
				JToolBarHelper::custom( 'teamresults.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true );
			}
		}

		//Show trash and delete for components that uses the state field
		if ( isset( $this->items[0]->state ) ) {
			if ( $state->get( 'filter.state' ) == -2 && $canDo->get( 'core.delete' ) ) {
				JToolBarHelper::deleteList( '', 'teamresults.delete', 'JTOOLBAR_EMPTY_TRASH' );
				JToolBarHelper::divider();
			} else if ( $canDo->get( 'core.edit.state' ) ) {
				JToolBarHelper::trash( 'teamresults.trash', 'JTOOLBAR_TRASH' );
				JToolBarHelper::divider();
			}
		}

		if ( $canDo->get( 'core.admin' ) ) {
			JToolBarHelper::preferences( 'com_swa' );
		}

		//Set sidebar action - New in 3.0
		JHtmlSidebar::setAction( 'index.php?option=com_swa&view=teamresults' );

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
			'a.checked_out' => JText::_( 'COM_SWA_TEAMRESULTS_CHECKED_OUT' ),
			'a.checked_out_time' => JText::_( 'COM_SWA_TEAMRESULTS_CHECKED_OUT_TIME' ),
			'a.race_id' => JText::_( 'COM_SWA_TEAMRESULTS_RACE_ID' ),
			'a.university_id' => JText::_( 'COM_SWA_TEAMRESULTS_UNIVERSITY_ID' ),
			'a.team_number' => JText::_( 'COM_SWA_TEAMRESULTS_TEAM_NUMBER' ),
			'a.result' => JText::_( 'COM_SWA_TEAMRESULTS_RESULT' ),
		);
	}

}