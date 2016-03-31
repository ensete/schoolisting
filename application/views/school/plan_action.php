<div class="row">
	<div class="col-sm-4">
		<a class="btn btn-lg btn-greentur" href="/planaction/add">
            <i class="fa fa-list-alt fa-3x pull-left"></i> <?php echo LocalizedString("Add new", "school/planaction") ?> <br /> <?php echo LocalizedString("Plan", "school/planaction") ?>
        </a>
	</div>
</div>
<br />
<?php 
	$ci=& get_instance();
	$error = $ci->session->flashdata("error");
	if (!empty($error)): ?>
	<div class="alert alert-danger fade in">
    	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><?php echo $error ?></h4>
	</div>
<?php endif; ?>
<div class="row">
	<?php foreach ($plans as $plan): ?>
		<div class="col-sm-4">
	        <div class="panel panel-default">
	            <div class="panel-body">
	                <ul class="panel-actions">
	                	<div class="btn-group">
		                    <a href="#" class="btn btn-link dropdown-toggle" data-toggle="dropdown">
		                        <i class="fa fa-bars"></i>
		                    </a>
		                    <ul class="dropdown-menu pull-right">
		                    	<?php if ($plan->status == 0): ?>
			                        <li>
			                        	<a href="<?php echo base_url("planaction/close_plan/{$plan->id}") ?>" onclick="return confirm('<?php echo LocalizedString("Are you sure?", "school/planaction") ?>')"><?php echo LocalizedString("Close") ?></a>
			                        </li>
			                    <?php endif; ?>
		                        <?php if ($plan->status == 1): ?>
			                        <li>
			                        	<a href="<?php echo base_url("planaction/copy_plan/{$plan->id}") ?>"><?php echo LocalizedString("Copy") ?></a>
			                        </li>
			                    <?php endif; ?>
		                        <li>
		                        	<?php 
		                        		echo highslide_anchor("planaction/check_list/{$plan->id}", LocalizedString("Go to checklist"))
		                        	?>
		                        </li>
		                    </ul>
		                </div>
	                </ul>
	                <p class="text-muted" style="font-size: 20px">
	                	<?php echo convert_plan_status_as_string($plan->status) ?>
	                </p>
	                <h5><small>Category: </small><?php echo $plan->category_name ?></h5>
	                <h4><small>Completed tasks: </small><?php echo $plan->completed_task ?>/<?php echo $plan->qty_tasks ?></h4>
	                
	                <?php $percent_completed = round(100*$plan->completed_task/$plan->qty_tasks); ?>
	                <div class="progress progress-xs">
	                    <div class="progress-bar 
	                    <?php if ($plan->status == 1): ?>
	                    	progress-bar-danger
	                    <?php elseif ($plan->status == 2): ?>
	                    	progress-bar-success
	                   	<?php else: ?>
	                   		progress-bar-info
	                   	<?php endif; ?>
	                    " role="progressbar" 
	                    	aria-valuenow="<?php echo $percent_completed ?>" aria-valuemin="0" aria-valuemax="100" 
	                    	style="width: <?php echo $percent_completed ?>%">
	                        <div class="sr-only"><?php echo $percent_completed ?>%</div>
	                    </div><!-- /.progress-bar -->
	                </div>
	                <p><small><i class="fa fa-angle-up fa-fw text-success"></i> Starttime <span class="text-muted">
	                	<?php echo $plan->starttime ?>
	                </span></small></p>
	                <p><small><i class="fa fa-angle-up fa-fw text-success"></i> Endtime <span class="text-muted">
	                	<?php echo $plan->endtime ?>
	                </span></small></p>
	            </div><!-- /.panel-body -->
	        </div><!-- /.panel -->
	    </div><!-- /.cols -->
    <?php endforeach; ?>
</div>
<script>
	$(document).ready(function(){
		$('body').on('hidden.bs.modal', '.modal', function () {
		    $(this).removeData('bs.modal');
		});
	});
	
	function toggle_status (input) 
	{
		console.log(input);
		var plan_task_id = $(input).val();
		var status = +$(input).is(":checked"); 
		
	  	if (status == 1)
	  	{
	  		$(input).parent().parent().addClass('disabled');
	  	}
	  	else
	  	{
	  		$(input).parent().parent().removeClass('disabled');
	  	}
	  	// $.post("/planaction/toggle_status_plantask", {
  			// 'plan_task_id': plan_task_id,
  			// 'status': status
  		// }, function(r){
  			// console.log(r);
  		// });
	}
</script>