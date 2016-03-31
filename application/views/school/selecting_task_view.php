<?php foreach ($tasks as $task): ?>

	<div class="todo-lists">
        <div class="kit-todo">
            <div class="nice-checkbox nice-checkbox-inline">
                <input name="selected_element[]" type="checkbox" id="task-<?php echo $task->id ?>" value="<?php echo $task->id ?>" checked />
                <label for="task-<?php echo $task->id ?>"><?php echo $task->name ?></label>
            </div>
        </div><!-- /.kit-todo -->
    </div><!-- /.todo-lists -->
    
<?php endforeach; ?>
