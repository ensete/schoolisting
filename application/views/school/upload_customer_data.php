<?php 
	$ci=& get_instance();
	$success = $ci->session->flashdata("success");
	if (!empty($success)): ?>
	<div class="alert alert-success fade in">
    	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><?php echo $success ?></h4>
	</div>
<?php endif; ?>
<?php 
	$error = $ci->session->flashdata("error");
	if (!empty($error)): ?>
	<div class="alert alert-danger fade in">
    	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><?php echo $error ?></h4>
	</div>
<?php endif; ?>
<form id="upload_form" role="form" class="form-horizontal form-bordered" method="post" action="/school/upload_customer_data">
	<p class="col-md-offset-3">
		<?php echo LocalizedString("Description for upload customer data", "school/profile") ?>
	</p>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="typeahead-local"><?php echo LocalizedString("Your customers' emails", 'school/profile') ?></label>
        <div class="col-sm-5">
            <div class="input-group input-group-in">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input required="" id="typeahead-local" class="form-control" placeholder="Your customers' emails" name="customers_emails" value="<?php echo $customers_emails; ?>">
            </div><!-- /input-group-in -->
            <p><?php echo LocalizedString("Separate different emails by comma.", "school/profile") ?></p>
        </div><!--/cols-->
    </div><!--/form-group-->
    
    <div class="form-group" style="text-align: center">
        <button type="submit" name="save_form" value="save_form" class="btn btn-default">Save Email</button>
    </div>
</form>
