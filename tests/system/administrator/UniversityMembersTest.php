<?php


class UniversityMembersTest extends SwaTestCase {

	public function testAddMultipleUniversityMembers() {
		$this->setUp();
		$this->gotoAdmin();
		$this->doAdminLogin();
		$this->clearAdminUniversityMembers();

		//Create joomla users
		$timestring = strval( time() );
		$users = array(
			'TestUser-' . $timestring . '-0',
			'TestUser-' . $timestring . '-1',
			'TestUser-' . $timestring . '-2',
		);
		foreach( $users as $username ) {
			$this->createAdminJoomlaUser( $username , 'somePass' );
		}

		//Add some universities
		$this->clearAdminUniversities();
		$this->addAdminUniversity( 'uni1', 'http://foo.com' );
		$this->addAdminUniversity( 'uni2', 'http://foo.com' );

		//Create some members
		$this->clearAdminMembers();
		$this->addAdminMember(
			$users[0], false, 'Male', '1993-02-25', '+441803111111', 'uni1', 'course', 2016, 'Race', 'Intermediate', 'L', 'nm', '11', 'No thanks'
		);
		$this->addAdminMember(
			$users[1], false, 'Male', '1993-02-25', '+441803111111', 'uni1', 'course', 2016, 'Race', 'Intermediate', 'L', 'nm', '11', 'No thanks'
		);
		$this->addAdminMember(
			$users[2], false, 'Male', '1993-02-25', '+441803111111', 'uni2', 'course', 2016, 'Race', 'Intermediate', 'L', 'nm', '11', 'No thanks'
		);

		$universityMembers = array(
			array( $users[0], 'uni1', 'None', false ),
			array( $users[1], 'uni1', 'President', true ),
			array( $users[2], 'uni2', 'Treasurer', false ),
		);

		foreach( $universityMembers as $data ) {
			list( $user, $uni, $committee, $graduated ) = $data;
			$this->addAdminUniversityMember( $user, $uni, $committee, $graduated );
		}

		$this->open( 'administrator/index.php?option=com_swa&view=universitymembers' );
		foreach( $universityMembers as $key => $data ) {
			list( $user, $uni, $committee, $graduated ) = $data;
			$tableRow = strval( $key + 1 );
			$this->assertTable( 'universitymemberList.' . $tableRow . '.2', $uni );
			$this->assertTable( 'universitymemberList.' . $tableRow . '.3', $user );
			$this->assertTable( 'universitymemberList.' . $tableRow . '.4', $committee );
			if( $graduated ) {
				$this->assertTable( 'universitymemberList.' . $tableRow . '.5', '1' );
			} else {
				$this->assertTable( 'universitymemberList.' . $tableRow . '.5', '0' );
			}
		}

		foreach( $universityMembers as $key => $data ) {
			list( $user, $uni, $committee, $graduated ) = $data;
			$this->open( 'administrator/index.php?option=com_swa&view=universitymembers' );
			$this->click( 'id=cb' . $key );
			$this->clickAndWait( 'css=#toolbar-edit > button.btn.btn-small' );
			$this->assertSelectedLabel( 'id=jform_member_id', $user );
			$this->assertSelectedLabel( 'id=jform_university_id', $uni );
			$this->assertSelectedLabel( 'id=jform_committee', $committee );
			if( $graduated ) {
				$this->assertValue( 'id=jform_graduated', 'on' );
			} else {
				$this->assertValue( 'id=jform_graduated', 'off' );
			}
		}

	}

	public function testChangeEntry() {
		$this->setUp();
		$this->gotoAdmin();
		$this->doAdminLogin();
		$this->clearAdminUniversityMembers();

		//Create a joomla user
		$user = 'TestUser-' . strval( time() ) . '-0';
		$this->createAdminJoomlaUser( $user , 'somePass' );

		//Add a university
		$this->clearAdminUniversities();
		$this->addAdminUniversity( 'uni1', 'http://foo.com' );
		$this->addAdminUniversity( 'uni2', 'http://foo.com' );

		//Create a member
		$this->clearAdminMembers();
		$this->addAdminMember(
			$user, false, 'Male', '1993-02-25', '+441803111111', 'uni1', 'course', 2016, 'Race', 'Intermediate', 'L', 'nm', '11', 'No thanks'
		);

		//Add the starting entry
		$this->addAdminUniversityMember( $user, 'uni1', 'None', false );
		$this->open( 'administrator/index.php?option=com_swa&view=universitymembers' );
		$this->click( 'id=cb0' );
		$this->clickAndWait( 'css=#toolbar-edit > button.btn.btn-small' );

		//graduate
		$this->click("id=jform_graduated");
		$this->clickAndWait( '//button[@onclick="Joomla.submitbutton(\'universitymember.apply\')"]' );
		$this->assertValue( 'id=jform_graduated', 'on' );
		//un-graduate
		$this->click("id=jform_graduated");
		$this->clickAndWait( '//button[@onclick="Joomla.submitbutton(\'universitymember.apply\')"]' );
		$this->assertValue( 'id=jform_graduated', 'off' );
		//committee VP
		$this->select( 'id=jform_committee', 'Vice President' );
		$this->clickAndWait( '//button[@onclick="Joomla.submitbutton(\'universitymember.apply\')"]' );
		$this->assertSelectedLabel( 'id=jform_committee', 'Vice President' );
		//un-committee OTHER
		$this->select( 'id=jform_committee', 'Other' );
		$this->clickAndWait( '//button[@onclick="Joomla.submitbutton(\'universitymember.apply\')"]' );
		$this->assertSelectedLabel( 'id=jform_committee', 'Other' );
		//un-committee None
		$this->select( 'id=jform_committee', 'None' );
		$this->clickAndWait( '//button[@onclick="Joomla.submitbutton(\'universitymember.apply\')"]' );
		$this->assertSelectedLabel( 'id=jform_committee', 'None' );
		//change uni
		$this->select( 'id=jform_university_id', 'uni2' );
		$this->clickAndWait( '//button[@onclick="Joomla.submitbutton(\'universitymember.apply\')"]' );
		$this->assertSelectedLabel( 'id=jform_university_id', 'uni2' );
		//TODO check changing user?

	}

} 