<?php

defined( '_JEXEC' ) or die;

JHtml::_( 'behavior.keepalive' );
JHtml::_( 'behavior.tooltip' );

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load( 'com_swa', JPATH_ADMINISTRATOR );

$get = JFactory::getApplication()->input->get;
$memberId = $get->getInt('member', $default=null);
$member = $this->items[$memberId];
$qualifications = $member->qualifications;

$baseUrl = "index.php?option=com_swa";
?>
<style>
    .form-inline .form-group {
        float:left;
        margin: 10px;
    }
    form, .table {
        margin-bottom: 0;
    }
</style>

<h1><?php echo $member->name ?>'s Qualifications</h1>

<div class="favth-panel favth-panel-default">
    <div class="favth-panel-heading">
        <form class="form-inline">
            <div class="form-group">
                <label for="safety-boat">Can Safety Boat?</label>
                <select id="safety-boat">
                    <option <?php echo $member->safety_boat ? "selected" : "" ?> value="1">Yes</option>
                    <option <?php echo $member->safety_boat ? "" : "selected" ?> value="0">No</option>
                </select>
            </div>
            <div class="form-group">
                <label for="instruct">Can Instruct?</label>
                <select class="form-control" id="instruct">
                    <option <?php echo $member->instruct ? "selected" : "" ?> value="1">Yes</option>
                    <option <?php echo $member->instruct ? "" : "selected" ?> value="0">No</option>
                </select>
            </div>
            <button type="submit" class="btn">Update</button>
        </form>
    </div>
    <div class="favth-panel-body">
        <form  method="POST">
            <?php echo JHtml::_( 'form.token' ) ?>
            <input type="hidden" name="option" value="com_swa" />
            <input type="hidden" name="task" value="orgmemberqualifications.submit" />
            <table class="table table-hover" id="qualificationsList">
                <thead>
                <tr>
                    <th class='left'>Qualification Type</th>
                    <th class='right'>Expires</th>
                    <th class='right'>Approved</th>
                    <th class="right">Image</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $qualifications as $qual ) :
                    $expired = $qual->expiry == null or new DateTime($qual->expiry) < new DateTime();
//                    $approved = $qual->approved_on != null and $qual->approved_by != null;

                    $imageUrl = "{$baseUrl}&task=orgmemberqualifications.viewImage&qualification={$qual->id}";
                    $imgSrc = JRoute::_( $imageUrl );
                    ?>
                    <tr>
                        <td>
                            <?php echo $qual->type; ?>
                        </td>
                        <td>
                            <?php echo JHtml::calendar(
                                $value = $qual->expiry,
                                $name = "qualifications[expiry][{$qual->id}]",
                                $id = "expires{$qual->id}",
                                $format = "%Y-%m-%d") ?>
                        </td>
                        <td>
                            <select <?php echo "id='approved{$qual->id}' name='qualifications[approved][{$qual->id}]'" ?>>
                                <option <?php echo $qual->approved ? "selected" : "" ?> value="1">Yes</option>
                                <option <?php echo $qual->approved ? "" : "selected" ?> value="0">No</option>
                            </select>
                        </td>
                        <td>
                            <a href="<?php echo $imgSrc ?>" target="_blank">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td><button type="submit" class="btn btn-default">Submit</button></td>
                </tr>
                </tfoot>
            </table>
        </form>
        </div>
    </div>
