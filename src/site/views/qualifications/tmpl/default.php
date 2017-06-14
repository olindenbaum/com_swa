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

<!-- TODO: Is this needed? Looks like it doesn't do anything -->
<script type="text/javascript">
	getScript( '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', function () {
		jQuery( document ).ready( function () {
			jQuery( '#form-member' ).submit( function ( event ) {
			} );
		} );
	} );
</script>

<h1>Qualifications</h1>

<p>Below you are able to manage your current qualifications.</p>

<?php
if( empty( $this->items ) ) {
	echo "<p>You do not have any qualifications registered with us!</p>";
} else {
	?>

	<table class="table table-hover">
		<thead>
			<tr>
				<th>Id</th>
				<th>Type</th>
				<th>Expires</th>
				<th>Approved</th>
				<th>File</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $this->items as $item ) {
				$approved = $item->approved_on != null;
				$expires = $item->expires == null ? "-" : date('d-m-Y', strtotime($item->expires));
				$imgSrc =
//					"http://localhost/swa/images/banners/windsurf-mag-subscribe.jpg";
					JRoute::_(
						'index.php?option=com_swa&task=qualifications.viewImage&qualification=' .
						$item->id
					);

				print "<tr>";
				print "<td>{$item->id}</td>";
				print "<td>{$item->type}</td>";
				if ( new DateTime( $item->expires ) < new DateTime() ) {
					echo "<td bgcolor='#FF6666'>";
				} else {
					echo "<td>";
				}
				echo $expires;
				echo "</td>";
				if ( !$approved ) {
					echo "<td bgcolor='#FF6666'>";
				} else {
					echo "<td>";
				}
				echo $approved ? "True" : "False";
				echo "</td>";
				echo "<td><a href='$imgSrc' target='_blank'><img src='$imgSrc' height='50' width='50'/></a></td>";
				print "</tr>";
			}
			?>
		</tbody>
	</table>

<?php
}
?>

<h2>Add new qualification</h2>

<div class="qualification front-end-edit">
	<form id="form-qualification" method="post"
		  action="<?php echo JRoute::_( 'index.php?option=com_swa&task=qualifications' ); ?>"
		  class="form-validate form-horizontal" enctype="multipart/form-data">
		<table class="table">
			<thead></thead>
			<tbody>
				<tr>
					<td><?php echo $this->form->getLabel( 'type' ); ?></td>
					<td><?php echo $this->form->getInput( 'type' ); ?></td>
				</tr>
				<tr>
					<td><?php echo $this->form->getLabel( 'file_upload' ); ?></td>
					<td><?php echo $this->form->getInput( 'file_upload' ); ?></td>
				</tr>

				<tr>
					<td>
						<div class="control-group">
							<div class="controls">
								<button type="submit" class="validate btn btn-primary">
									<?php echo JText::_('JSUBMIT'); ?>
								</button>
								<a class="btn"
								   href="<?php echo JRoute::_('index.php?option=com_swa&task=qualifications.cancel'); ?>"
								   title="<?php echo JText::_( 'JCANCEL' ); ?>">
									<?php echo JText::_('JCANCEL'); ?>
								</a>
							</div>
						</div>
					</td>
					<!--Empty td tags to ensure row divider line continues across whole width of table-->
					<td></td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_swa"/>
		<input type="hidden" name="task" value="qualifications.add"/>
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>
</div>