<?php

defined( '_JEXEC' ) or die;

require_once JPATH_COMPONENT . '/controller.php';

class SwaControllerOrgMemberQualifications extends SwaController {

	public function viewImage() {
		/** @var SwaModelOrgMemberQualifications $model */
		$model = $this->getModel( 'OrgMemberQualifications' );

		$member = $model->getMember();
		if ( !is_object( $member ) ) {
			throw new Exception( 'You must be a member to view this page.' );
		}
		if ( !$member->swa_committee ) {
			throw new Exception( 'You must be an SWA committee member to view this page.' );
		}

		$input = JFactory::getApplication()->input;
		$data = $input->getArray();
		$qualificationId = $data['qualification'];

		$db = JFactory::getDbo();
		$query = $db->getQuery( true );

		$query->select( 'file, file_type' );
		$query->from( '#__swa_qualification' );
		$query->where( 'id=' . $db->quote( $qualificationId ) );

		$db->setQuery( $query );
		if( !$db->execute() ) {
			die( 'something went wrong selecting the image' );
		}
		$qualification = $db->loadObject();

		// output the file
		header("Content-type: " . $qualification->file_type );
		print( $qualification->file );
		exit();
	}

	public function update() {
		echo "Update";
		var_dump($this->input);
	}

	public function submit() {
		// Check for request forgeries.
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		// get the input and the filtered post data
		$input = JFactory::getApplication()->input;
		$memberId = $input->get->getInt('member', null);
		$post = $input->post;
		$qualifications = $post->get('qualifications', null, 'array');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);



		$sql = "UPDATE {$db->qn('#__swa_qualification')}\n";
		$sql .= "SET {$db->qn('expiry_date')} = CASE {$db->qn('id')}\n";

		foreach ( $qualifications['expiry'] AS $id => $expiry ) {
			$expiry = (int)$expiry ? "DATE({$db->q($expiry)})" : "NULL";
			$sql .= "WHEN {$id} THEN {$expiry}\n";
		}

		$sql .= "END,\n";
		$sql .= "{$db->qn('approved')} = CASE {$db->qn('id')}\n";

		foreach ( $qualifications['approved'] AS $id => $approved ) {
			$approved = (int)$approved;
			$sql .= "WHEN {$id} THEN {$approved}\n";
		}

		$sql .= "END\n";
		$sql .= "WHERE {$db->qn('id')} IN ";

		$ids = "(";
		foreach (array_keys($qualifications['expiry']) AS $id) {
			if ( !is_int($id) )
				continue;

			$ids .= "{$id}, ";
		}

		$ids = rtrim($ids, ", ");
		$ids .= ")";
		$sql .= $ids;

		$query->setQuery( $sql );
//		echo $query->dump(); die;
		$db->setQuery( $query );


		$e = $db->execute();
		if ( !$e ) {
			JLog::add( __CLASS__ . ' failed to update qualifications: ' . $ids, JLog::INFO, 'com_swa' );

		} else {
			$this->logAuditFrontend( 'updated qualifications: ' . $ids );
		}

		$this->setRedirect(
			JRoute::_(
				'index.php?option=com_swa&view=orgmemberqualifications&layout=member&member=' . $memberId,
				false
			)
		);
	}

	public function approve() {
		// Check for request forgeries.
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		/** @var SwaModelOrgMemberQualifications $model */
		$model = $this->getModel( 'OrgMemberQualifications' );
		$member = $model->getMember();

		if ( !is_object( $member ) ) {
			throw new Exception( 'You must be a member to view this page.' );
		}
		if ( !$member->swa_committee ) {
			throw new Exception( 'You must be an SWA committee member to view this page.' );
		}

		$input = JFactory::getApplication()->input;
		$data = $input->getArray();
		$qualificationId = $data['qualification'];

		$db = JFactory::getDbo();
		$query = $db->getQuery( true );

		$query
			->update( $db->qn( '#__swa_qualification' ) )
			->where( $db->qn('id') . '=' . $db->quote( $qualificationId ) )
			->set(
				array(
					$db->qn( 'approved_on' ) . '=' . $db->quote( date('d-m-Y') ),
					$db->qn( 'approved_by' ) . '=' . $db->quote( $member->id )
				)
			);

		$db->setQuery( $query );

		if ( !$db->execute() ) {
			JLog::add(
				__CLASS__ . ' failed to approve qualification: ' . $qualificationId,
				JLog::INFO,
				'com_swa'
			);
		} else {
			$this->logAuditFrontend( 'approved qualification ' . $qualificationId );
		}

		$this->setRedirect(
			JRoute::_( 'index.php?option=com_swa&view=orgmemberqualifications&layout=default', false )
		);
	}

	public function unapprove() {
		// Check for request forgeries.
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		/** @var SwaModelOrgMemberQualifications $model */
		$model = $this->getModel( 'OrgMemberQualifications' );

		$member = $model->getMember();
		if ( !is_object( $member ) ) {
			throw new Exception( 'You must be a member to view this page.' );
		}
		if ( !$member->swa_committee ) {
			throw new Exception( 'You must be an SWA committee member to view this page.' );
		}

		$input = JFactory::getApplication()->input;
		$data = $input->getArray();
		$qualificationId = $data['qualification'];

		$db = JFactory::getDbo();
		$query = $db->getQuery( true );

		$query
			->update( $db->quoteName( '#__swa_qualification' ) )
			->where( 'id = ' . $db->quote( $qualificationId ) )
			->set(
				array(
					$db->qn('approved_on') . '=NULL',
					$db->qn('approved_by') . '=NULL'
				)
			);

		$db->setQuery( $query );

		if ( !$db->execute() ) {
			JLog::add(
				__CLASS__ . ' failed to unapprove qualification: ' . $qualificationId,
				JLog::INFO,
				'com_swa'
			);
		} else {
			$this->logAuditFrontend( 'unapproved qualification ' . $qualificationId );
		}

		$this->setRedirect(
			JRoute::_( 'index.php?option=com_swa&view=orgmemberqualifications&layout=default', false )
		);
	}

}
