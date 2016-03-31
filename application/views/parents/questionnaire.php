<?php 
	$active_category = 0;
?>
<style>
	.nice-radio>[type=radio]:checked+label:before {
  		content: '\f046';
  	}
  	.nice-radio:hover>[type=radio]:disabled:not(:checked)+label:before, .nice-radio>[type=radio]:not(:checked)+label:before {
	  	content: '\f0c8';
	}
</style>
<body>
<main id="wrapper" class="wrapkit-wrapper">
	<form id="questionnaire_form" action="/questionnaire/submit_questionnaire" method="post" onsubmit="return check_active_category_exist()">
		<input type="hidden" name="school_id" value="<?php echo $school_id ?>" />
		<input type="hidden" name="self_evaluate" value="<?php echo $self_evaluate ?>" />
		<input type="hidden" name="questionnaire_id" value="<?php echo $questionnaire ?>" />
		<input type="hidden" name="saved_answer_id" value="<?php echo $saved_answer_id ?>" />
	    <section class="content-wrapper" style="padding: 0 150px;">
			<div class="content">
				<div class="row">
                    <div class="col-md-12" style="padding:25px">
                        <?php if($user_type == "parents") : ?>
                            <p><?php echo LocalizedString("parent-rating-text1") ?></p>
                            <p><?php echo LocalizedString("parent-rating-text2") ?></p>
                        <?php else : ?>
                            <p><?php echo LocalizedString("school-rating-text1") ?></p>
                            <p><?php echo LocalizedString("school-rating-text2") ?></p>
                        <?php endif; ?>
                    </div>
					<?php foreach ($questions as $q_index => $question): ?>
						<?php 
							if ($active_category != $question->category_id || $active_category == 0)
							{
								// header
								$active_category = $question->category_id;
						?>
								<div class="col-md-12">
		                            <div class="panel panel-default">
		                                <div class="panel-heading">
		                                    <ul class="panel-actions panel-actions-alt">
		                                        <li class="dropdown" style="position:relative;bottom:4px;right:5px">
		                                            <?php echo LocalizedString("Applicable", "questionnaire") ?>:
		                                        </li>
                                                <li class="applicable-choice" style="position:relative;bottom:5px">
                                                    <div class="btn-group" data-toggle="buttons">
                                                        <label class="btn btn-silc active" style="padding: 5px">
                                                            <input onchange="toggle_required(this)" value="1" data-action="yes" type="radio" name="category-<?php echo $question->category_id ?>" checked="checked"> <?php echo LocalizedString("Yes", "questionnaire") ?>
                                                        </label>
                                                        <label class="btn btn-silc" style="padding: 5px;">
                                                            <input id="trigger-panel-<?php echo $question->category_id ?>" onchange="toggle_required(this)" value="0" data-action="no" type="radio" name="category-<?php echo $question->category_id ?>"> <?php echo LocalizedString("No", "questionnaire") ?>
                                                        </label>
                                                    </div>
                                                </li>
		                                    </ul>
		                                    <h3 class="panel-title" style="font-size: 18px;"><?php echo mb_strtoupper(LocalizedString($question->category_name)) ?></h3>
		                                </div><!-- /.panel-heading -->
		                                <div class="panel-body">
						<?php
							}
						?>
						<?php
							// rows
						?>
							<p><?php echo $question->question ?></p>
							<?php $list_answers = json_decode($question->answer) ?>
                            <?php shuffle($list_answers); ?>
			            	<?php foreach ($list_answers as $index => $answers): ?>
                        		<div class="row">
                        			<?php foreach ($answers as $key => $answer): ?>
                                        <?php
                                            $checked = "";
                                            if(isset($saved_answers[$question->question_id][$index])) {
                                                if($saved_answers[$question->question_id][$index] == $key) {
                                                    $checked = "checked";
                                                }
                                            }
                                        ?>
										<div class="col-md-3">
				                            <div class="panel panel-default">
				                                <div class="panel-heading">
				                                    <h3 class="panel-title nice-radio">
				                                    	<?php echo LocalizedString("Level", "questionnaire") ?> <?php echo 1*$key + 1; ?>
													    <div class="pull-right nice-radio">
													    	<?php $unique_id = "question-{$question->question_id}-{$index}-{$question->category_id}" ?>
														    <input class="category-<?php echo $question->category_id ?>"
														    	value="<?php echo $key ?>" type="radio" name="<?php echo $unique_id ?>"
														    	id="<?php echo $unique_id.$key ?>" required=""
                                                                <?php echo $checked ?> >
														    <label for="<?php echo $unique_id.$key ?>">
														    	&nbsp;
														    </label>
				                                        </div>
				                                    </h3>
				                                </div><!-- /.panel-heading -->
				                                <div class="panel-body">
				                                    <div class="nice-scroll" data-scroll-wrapper="#scrollWrapper3" data-cursorcolor="#D2E7F5" data-background="transparent" style="height:150px">
				                                        <div id="scrollWrapper3">
				                                            <?php echo str_replace("\n", "<br />", $answer) ?>
				                                        </div>
				                                    </div><!-- /.nice-scroll -->
				                                </div><!-- /.panel-body -->
				                            </div><!-- /.panel -->
				                        </div><!-- /.cols -->
				               		<?php endforeach; ?>
                                    <?php
                                    $checked = "";
                                    if(isset($saved_answers[$question->question_id][$index])) {
                                        if($saved_answers[$question->question_id][$index] == 3) {
                                            $checked = "checked";
                                        }
                                    }
                                    ?>
                                    <div class="col-md-3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h3 class="panel-title nice-radio">
                                                    <div class="pull-right nice-radio">
                                                        <?php $unique_id = "question-{$question->question_id}-{$index}-{$question->category_id}" ?>
                                                        <input class="category-<?php echo $question->category_id ?>"
                                                               value="3" type="radio" name="<?php echo $unique_id ?>"
                                                               id="<?php echo $unique_id . "3" ?>"
                                                            <?php echo $checked ?> >
                                                        <label for="<?php echo $unique_id . "3" ?>">
                                                            &nbsp;
                                                        </label>
                                                    </div>
                                                </h3>
                                            </div><!-- /.panel-heading -->
                                            <div class="panel-body">
                                                <div class="nice-scroll" data-scroll-wrapper="#scrollWrapper3" data-cursorcolor="#D2E7F5" data-background="transparent" style="height:150px">
                                                    <div id="scrollWrapper3">
                                                        <?php echo LocalizedString("I have no clue about this question. Skip it") ?>
                                                    </div>
                                                </div><!-- /.nice-scroll -->
                                            </div><!-- /.panel-body -->
                                        </div><!-- /.panel -->
                                    </div><!-- /.cols -->
				               	</div>
				        	<?php endforeach; ?>
						<?php		
							if (!isset($questions[$q_index+1]) || $active_category != $questions[$q_index+1]->category_id)
							{
								// footer
						?>
										</div><!-- /.panel-body -->
		                            </div><!-- /.panel -->
		                        </div><!-- /.cols -->
						<?php
							}
						?>
					<?php endforeach; ?>
					
				    <div class="col-md-12">
				        <!-- Lists -->
				        <div class="panel panel-minimalize">
					   		<div class="panel-body">
					   			<button type="submit" class="btn btn-primary"><?php echo LocalizedString("Submit Form") ?></button>
					   		</div>
				        </div><!-- /panel-lists -->
				    </div><!--/cols-->
				</div><!--/row-->
			</div>
		</section>
	</form>
    <div id="fixed_bar"></div>
    <button id="save_all" type="button" class="btn btn-success"><?php echo LocalizedString("Save") ?></button>
</main>
<script>
    $( function(){
        'use strict';
        toastr.options = {
            positionClass: 'toast-bottom-left',
            closeButton: true
        };

        $( '#save_all' ).on( 'click', function() {
            var url = "<?php echo base_url("questionnaire/save_answered_questions") ?>";
            var values = {};
            $.each($('#questionnaire_form').serializeArray(), function(i, field) {
                values[field.name] = field.value;
            });
            var myJsonString = JSON.stringify(values);
            var data = "values="+myJsonString;
            $.ajax({
                url: url,
                type: "POST",
                context: this,
                data: data,
                beforeSend: function () {
                    $(this).html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
                },
                success: function (result) {
                    if(result == 1) {
                        toastr.success( '<?php echo LocalizedString("Saved") ?>' );
                    }
                    $(this).html("<?php echo LocalizedString("Save") ?>");
                }
            });
        });

        var categories = <?php echo $categories ?>;
        $.each(categories, function(category_id, value) {
            if(value == 0) {
                $("#trigger-panel-"+category_id).click();
            }
        });

    });

	function toggle_required (input)
	{
        var action = $(input).data('action');
        if(action == "yes") {
            $(input).closest('.panel-heading').next().show();
        } else {
            $(input).closest('.panel-heading').next().hide();
        }

		var value = input.value;
		var class_domain = $(input).attr('name');
		if (value == 0)
		{
			$('input.' + class_domain).removeAttr("required");
		}
		else
		{
			console.log('here');
			$('input.' + class_domain).attr("required", "");
		}
	}
	
	function check_active_category_exist ()
	{
		var active_category_exist = false;
		$( ".applicable-choice input[type='radio']:checked" ).each(function( index ) {
		 	if ($(this).val() == 1)
		 	{
		 		active_category_exist = true;
		 		return false;
		 	}
		});
		if (active_category_exist)
		{
			return true;
		}
		alert("At least one category must be applicable!");
		return false;
	}
</script>