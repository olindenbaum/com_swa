<?php

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

class SwaModelOrgMemberQualifications extends SwaModelList {

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState( $ordering = null, $direction = null ) {
		// Initialise variables.
		$app = JFactory::getApplication();

		// Load the filter state.
		$search =
			$app->getUserStateFromRequest( $this->context . '.filter.search', 'filter_search' );
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

		// Select the required fields from the table.
		$query->select(
			$db->quoteName(
				array(
					'qualification.id',
					'member.id',
					'user.name',
					'qual_type.name',
					'qualification.expiry_date',
					'qualification.approved',
					'member_ability.safety_boat',
					'member_ability.instruct'
					),
				array(
					'id',
					'member_id',
					'member',
					'type',
					'expiry',
					'approved',
					'safety_boat',
					'instruct'
				)
			)
		);

		$query->from( $db->qn('#__swa_qualification', 'qualification') );

		$query->leftJoin( $db->qn('#__swa_qualification_type', 'qual_type') . ' ON qual_type.id = qualification.type_id' );
		$query->leftJoin( $db->qn('#__swa_member', 'member') . ' ON member.id = qualification.member_id' );
		$query->leftJoin( $db->qn('#__swa_member_ability', 'member_ability') . ' ON member_ability.member_id = member.id' );
		$query->leftJoin( $db->qn('#__users', 'user') . ' ON user.id = member.user_id' );
		$query->leftJoin( $db->qn('#__swa_university', 'uni') . ' ON uni.id = member.university_id' );

		// Add the list ordering clause.
		$orderCol = $this->state->get( 'list.ordering' );
		$orderDirn = $this->state->get( 'list.direction' );
		if ( $orderCol && $orderDirn ) {
			$query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );
		}

		return $query;
	}

	public function getMemberId() {
		$get = JFactory::getApplication()->input->get;
		$memberId = $get->getInt('member', $default=null);

		return $memberId;
	}

	public function getItems() {
		// NEVER limit this list
		$this->setState( 'list.limit', '0' );

		$parentItems = parent::getItems();
		$items = array();

		foreach($parentItems as $item) {

			$memberId = $item->member_id;

			if ( !isset($items[$memberId]) ) {
				$items[$memberId] = new stdClass();
				$items[$memberId]->id = $memberId;
				$items[$memberId]->name = $item->member;
				$items[$memberId]->safety_boat = $item->safety_boat;
				$items[$memberId]->instruct = $item->instruct;
			}

			unset($item->member_id);
			unset($item->member);
			unset($item->safety_boat);
			unset($item->instruct);

			$items[$memberId]->qualifications[] = $item;
		}

		return $items;
	}

}
