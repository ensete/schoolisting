<form method="post" action="/planaction/submit_plan_task">
	<input type="hidden" name="plan_id" value="<?php echo $plan_id ?>" />
	<div class="panel panel-default">
	    <div class="todo-lists">
	    	<?php foreach ($tasks as $task): ?>
	    		<div class="kit-todo <?php if ($task->status == 1): ?>disabled<?php endif; ?>">
		            <div class="nice-checkbox nice-checkbox-inline">
		                <input type="checkbox" name="task[]" id="task-<?php echo $task->id ?>" value="<?php echo $task->id ?>" onchange="toggle_status(this)"
		                	<?php if ($task->status == 1): ?>checked<?php endif; ?> <?php if ($plan_status != 0): ?>disabled<?php endif; ?>>
		                <label for="task-<?php echo $task->id ?>"><?php echo $task->name ?></label>
		            </div>
		        </div><!-- /.kit-todo -->
	    	<?php endforeach; ?>
	    </div><!-- /.todo-lists -->
	</div><!-- /.panel -->
	<?php if ($plan_status == 0): ?>
		<div class="form-group">
            <button type="submit" class="btn btn-default"><?php echo LocalizedString("Submit", "plan/task") ?></button>
        </div>
	<?php endif; ?>
</form>