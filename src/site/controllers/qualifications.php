<?php

defined( '_JEXEC' ) or die;

require_once JPATH_COMPONENT . '/controller.php';

class SwaControllerQualifications extends SwaController {

	public function viewImage() {

		$input = JFactory::getApplication()->input;
		$get = $input->get;
		$data = $input->getArray();
		$qualificationId = $get->getInt('qualification', $default=null);

		$model = $this->getModel('qualifications');
		$member = $model->getMember();

		//TODO get qualification
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );

		$query->select( 'qualification.*' );
		$query->from( $db->qn('#__swa_qualification', 'qualification') );
		$query->where( 'id=' . $db->quote( $qualificationId ) );

		$db->setQuery( $query );
		if( !$db->execute() ) {
			die( 'something went wrong selecting the image' );
		}
		$qualification = $db->loadObject();

		if ( !$member->id ) {
			die( "Couldn't get member id to view qualification image");
		}
		if( $qualification->member_id != $member->id ) {
			die( "Trying to get qualification image for other member.." );
		}
		//output the file?
		header("Content-type: " . $qualification->file_type );
		print( $qualification->file );
		exit();
	}

	public function add() {
		// Check for request forgeries.
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		// get the input and the filtered post data
		$input = JFactory::getApplication()->input;
		$post = $input->post;
		$formData = new JInput($post->get('jform', '', 'array'));

		var_dump($post);
		var_dump($formData->getInt('type'));

		// Get info about the uploaded file;
		$fileData = new JInput($post->files->get('jform', '', 'array'));
		$fileData = new JInput($fileData->get('file_upload', '', 'array'));
		$fileError = $fileData->getInt('error', $default=null);

		// TODO: see why getPath doesn't work as expected
		$filePath = $fileData->getPath('tmp_name', $default=null);

		$filePath = $fileData->getString('tmp_name', $default=null);

		var_dump($filePath);

		if( $fileError !== 0) {
			die( 'Got an error while uploading file' );
		}
		if( !file_exists( $filePath ) ) {
			die( "Couldn't find uploaded file!" );
		}

		$qualificationTypeId = $formData->getInt('type', $default=null);
		var_dump($qualificationTypeId);
		if ( $qualificationTypeId === null ) {
			die( "Couldn't find qualification type in POST data." );
		}

		$model = $this->getModel('qualifications');
		$member = $model->getMember();

		if ( !$member->id ) {
			die( "Couldn't get member id to add qualification");
		}

		// Load the file to add to the db
		$fp = fopen($filePath, 'r');
		$file = fread($fp, filesize($filePath));
		fclose($fp);

		$db = JFactory::getDbo();
		$query = $db->getQuery( true );

		$columns = array( 'member_id', 'type_id', 'file', 'file_type' );
		$values = array(
			$db->quote( $member->id ),
			$db->quote( $qualificationTypeId ),
			$db->quote( $file ),
			$db->quote( $fileData->getString('type') ),
		);

		$query
			->insert( $db->quoteName( '#__swa_qualification' ) )
			->columns( $db->quoteName( $columns ) )
			->values( implode( ',', $values ) );

		$db->setQuery( $query );
		if ( !$db->execute() ) {

			JLog::add(
				__CLASS__ . ' failed to add qualification: Member:' . $member->id,
				JLog::INFO,
				'com_swa'
			);

		} else {
			$this->logAuditFrontend( 'added qualification: ' . $qualificationTypeId );
		}

		$this->setRedirect( JRoute::_('index.php?option=com_swa&view=qualifications', false) );
	}

}
