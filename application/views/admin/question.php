<div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
    Change language <i class="fa fa-angle-down"></i>
    </button>
    <ul class="dropdown-menu" role="menu">
    	<?php foreach ($languages as $key => $language): ?>
        	<li>
        		<a href="<?php echo base_url("/admin/questionnaire/show_question?id=" . $element_id . "&language=" . $key) ?>"><?php echo $language ?></a>
        	</li>
        <?php endforeach; ?>
    </ul>
</div>
<br /><br />
<form method="post" onsubmit="return save_question()" action="/admin/questionnaire/save_question">
	<input type="hidden" name="element_id" value="<?php echo $element_id ?>" />
	<input type="hidden" name="language_id" value="<?php echo $language_id ?>" />
	<textarea name="question" style="display: none"></textarea>
	<textarea name="answer" style="display: none"></textarea>
	<div id="summernote-panel" class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-pencil"></i> Question</h3>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
            <div id="summernote"><?php echo @$question ?></div>
        </div><!-- /.panel-body -->
		
    </div><!-- /.panel -->

	<div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Answer</h3>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
        	<div class="clearfix">
                <div class="pull-right">
                     <button onclick="add_answer()" type="button" class="btn btn-primary">Add answer</button>
                </div>
            </div>
            <hr>

            <div class="table-responsive">
                <table style="table-layout: fixed" id="answer" class="table table-bordered table-striped">
                    <tbody>
                    	<?php foreach ($answers as $ans): ?>
	                        <tr>         
	                            <td>
	                            	<a href="#" class="comments" data-type="textarea" data-pk="1" data-placeholder="Enter answer here..." data-title="Enter answer"><?php echo @$ans[0] ?></a>
	                            </td>
	                            <td>
	                            	<a href="#" class="comments" data-type="textarea" data-pk="1" data-placeholder="Enter answer here..." data-title="Enter answer"><?php echo @$ans[1] ?></a>
	                            </td>
	                            <td>
	                            	<a href="#" class="comments" data-type="textarea" data-pk="1" data-placeholder="Enter answer here..." data-title="Enter answer"><?php echo @$ans[2] ?></a>
	                            	<a href="#" class="delete_answer glyphicon glyphicon-remove" onclick="return delete_answer(this)"></a>
	                            </td>
	                        </tr>
	                	<?php endforeach ?>
                    </tbody>
                </table>
            </div><!-- /.table -->
        </div><!-- /.table-responsive -->
    </div><!-- /.panel -->
	
	<div id="summernote-panel" class="panel panel-default">

        <div class="panel-footer">
            <div class="pull-right">
                <div class="btn-groupsaveChange">
                    <button id="summernoteSave" class="btn btn-default">Save Change</button>
                </div>
            </div>
        </div><!-- /.panel-footer -->
    </div><!-- /.panel -->

</form>
