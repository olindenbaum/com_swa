<?php

defined( '_JEXEC' ) or die;

JHtml::_( 'behavior.keepalive' );
JHtml::_( 'behavior.tooltip' );
JHtml::_( 'behavior.formvalidation' );
JHtml::_( 'formbehavior.chosen', 'select' );

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load( 'com_swa', JPATH_ADMINISTRATOR );
$doc = JFactory::getDocument();
$doc->addScript( JUri::base() . '/components/com_swa/assets/js/form.js' );

?>

<!-- TODO this doesn't seem to do anything!-->
<script type="text/javascript">
	getScript( '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', function () {
		jQuery( document ).ready( function () {
			jQuery( '#form-member' ).submit( function ( event ) {
			} );
		} );
	} );
</script>

<h1>Org Members with Qualifications</h1>

<p>Below is a list of all members with Qualifications.</p>

	<table class="table table-hover" id="qualificationsList">
		<thead>
			<tr>
				<th class='left'>Member</th>
				<th class='right'>Can Safety Boat</th>
				<th class='tight'>Can Instruct</th>
				<th class='right'>Expired Qualifications</th>
				<th class='right'>Unapproved Qualifications</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $this->items as $item ) :
//				$expired = $item->expiry == null or new DateTime($item->expiry) < new DateTime();
//				$approved = $item->approved_on != null and $item->approved_by != null;
//
//				$baseUrl = "index.php?option=com_swa";
//				$approvedUrl = JRoute::_( $baseUrl . '&task=orgmemberqualifications.approve' );
//				$unapprovedUrl = JRoute::_( $baseUrl . '&task=orgmemberqualifications.unapprove' );
//
//				$imgSrc = JRoute::_( $baseUrl . '&task=orgmemberqualifications.viewImage&qualification=5');

				$expired = 0;
				$unapproved = 0;
				foreach ( $item->qualifications as $qualification ) {
					if ( $qualification->expiry == null or new DateTime($qualification->expiry) < new DateTime() ) {
						$expired++;
					}

					if ( $qualification->approved_on == null and $qualification->approved_by == null ) {
						$unapproved++;
					}
				}
				?>
				<tr onclick="getElementById('edit-1').click()" style="cursor: pointer">
					<td>
						<a href="<?php echo JRoute::_("index.php?option=com_swa&view=orgmemberqualifications&layout=member&member={$item->id}") ?>" id="edit-1"></a>
							<?php echo $item->name; ?>
					</td>
					<td <?php echo $item->safety_boat ? "bgcolor='#FF6666'" : "" ?>>
						<?php echo $item->safety_boat ? "Yes" : "No" ?>
					</td>
					<td <?php echo $item->instruct ? "bgcolor='#FF6666'" : "" ?>>
						<?php echo $item->instruct ? "Yes" : "No" ?>
					</td>
					<td <?php echo $expired > 0 ? "bgcolor='#FF6666'" : ""?>>
						<?php echo $expired ?>
					</td>
					<td <?php echo $unapproved > 0 ? "bgcolor='#FF6666'" : ""?>>
						<?php echo $unapproved ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
