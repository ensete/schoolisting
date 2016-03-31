<?php if($success = $this->session->flashdata("success")) : ?>
    <?php echo $this->lbplusbuilder->get_element('schoolisting/success', array('message' => LocalizedString($success))); ?>
<?php endif; ?>
<form method="post" action="<?php echo base_url("/admin/questionnaire/handle_manage_questionnaire") ?>" class="row">
	<legend>
		<?php echo $questionnaire_name ?>
		<div class="pull-right">
			<button type="reset" value="Reset" class="btn btn-xs btn-primary">Reset</button>
			<button type="submit" class="btn btn-xs btn-success">Save questionnaire</button>
		</div>
	</legend>
	<input type="hidden" name="questionnaire_id" value="<?php echo $questionnaire_id ?>" />
	<?php foreach ($categories as $key => $elements): ?>
	<div class="col-md-4">
		<div class="panel panel-default">
		    <div class="panel-heading">
		        <h3 class="panel-title"><?php echo $key ?></h3><!-- /panel-title -->
		    </div><!-- /.panel-heading -->
			<?php foreach ($elements as $element): ?>
			    <div class="todo-lists">
			        <div class="kit-todo">
			            <div class="nice-checkbox nice-checkbox-inline">
			                <input name="selected_element[]" type="checkbox" id="element<?php echo $element['element_id'] ?>" value="<?php echo $element['element_id'] ?>"
			                	<?php if (in_array($element['element_id'], $selected_questions)): ?> checked <?php endif; ?>
			                />
			                <label for="element<?php echo $element['element_id'] ?>"><?php echo $element['element_name'] ?></label>
			            </div>
			        </div><!-- /.kit-todo -->
			    </div><!-- /.todo-lists -->
			<?php endforeach; ?>
		</div><!-- /.panel -->
	</div>
	<?php endforeach; ?>
</form>

