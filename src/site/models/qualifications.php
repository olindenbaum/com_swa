<?php

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

class SwaModelQualifications extends SwaModelList {

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState( $ordering = null, $direction = null ) {
		// Initialise variables.
		$app = JFactory::getApplication();

		// Load the filter state.
		$search = $app->getUserStateFromRequest( $this->context . '.filter.search', 'filter_search' );
		$this->setState( 'filter.search', $search );

		// Load the parameters.
		$params = JComponentHelper::getParams( 'com_swa' );
		$this->setState( 'params', $params );

		// List state information.
		parent::populateState( 'qualification.id', 'asc' );
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param    string $id A prefix for the store id.
	 *
	 * @return    string        A store id.
	 */
	protected function getStoreId( $id = '' ) {
		// Compile the store id.
		$id .= ':' . $this->getState( 'filter.search' );

		return parent::getStoreId( $id );
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery( true );

		$member = $this->getMember();

		// Select the required fields from the table.
		$query->select(
			$db->quoteName(
				array(
					'qualification.id',
					'user.name',
					'type.name',
					'qualification.expiry_date',
					'qualification.approved_on',
					'qualification.approved_by'
				),
				array('id', 'member', 'type', 'expires', 'approved_on', 'approved_by')
			)
		);

		$query->from( $db->qn('#__swa_qualification', 'qualification') );

		$query->leftJoin( $db->qn('#__swa_qualification_type', 'type') . " ON type.id = qualification.type_id" );
		$query->leftJoin( $db->qn('#__swa_member', 'member') . " ON member.id = qualification.member_id" );
		$query->leftJoin( $db->qn('#__users', 'user') . " ON user.id = member.user_id" );
		$query->leftJoin( $db->qn('#__swa_university', 'uni') . " ON uni.id = member.university_id" );

		$query->where( "qualification.member_id = {$member->id}" );

		// Add the list ordering clause.
		$orderCol = $this->state->get( 'list.ordering' );
		$orderDirn = $this->state->get( 'list.direction' );
		if ( $orderCol && $orderDirn ) {
			$query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );
		}

//		echo $query->dump();
//		die("model/qualifications");

		return $query;
	}

	public function getItems() {
		//NEVER limit this list
		$this->setState( 'list.limit', '0' );

		$items = parent::getItems();

		return $items;
	}

	public function getForm() {
		$form =
			$this->loadForm(
				'com_swa.qualification',
				'qualification',
				array( 'control' => 'jform' )
			);

		if ( empty( $form ) ) {
			return false;
		}

		return $form;
	}

}
