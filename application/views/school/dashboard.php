<div id="school-count-group" class="col-md-3">
    <div class="panel panel-default">
        <div class="panel-heading" style="height: 51px">
            <h3 class="panel-title" style="line-height: 30px;"><?php echo LocalizedString("Information") ?></h3>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
            <p>
                <i class="glyphicon glyphicon-thumbs-up"></i>
                <b class="school-details-title"> Likes: <?php echo $details['like_count'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-bookmark"></i>
                <b class="school-details-title"> Bookmarks: <?php echo $details['bookmark_count'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-share-alt"></i>
                <b class="school-details-title"> Recommendations: <?php echo $details['recommend_count'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-star-empty"></i>
                <b class="school-details-title"> <?php echo LocalizedString("Rates") ?>: <?php echo $details['rate_count'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-star"></i>
                <b class="school-details-title"> <?php echo LocalizedString("Parent Rating") ?>: <?php echo ($parent_rating) ? $parent_rating : 0 ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-star"></i>
                <b class="school-details-title"> <?php echo LocalizedString("School Rating") ?>: <?php echo ($school_rating) ? $school_rating : 0 ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-eye-open"></i>
                <b class="school-details-title"> <?php echo LocalizedString("Views") ?>: <?php echo $details['total_views'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-ok"></i>
                <b class="school-details-title"> <?php echo LocalizedString("Completed Plans") ?>: <?php echo $completed_plans ?></b>
            </p>
            <p>
                <i class="fa fa-pencil-square-o"></i>
                <a href="<?php echo base_url("school/profile/{$school['uuid']}/plan-actions") . "/" . refine_name($school['school_name']) ?>"><b class="school-details-title"> Active Plans: <?php echo $active_plans ?></b></a>
            </p>
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->

    <div class="panel panel-default">
        <div class="panel-heading" style="height: 51px">
            <h3 class="panel-title" style="line-height: 30px;"><?php echo LocalizedString("Share via Social Networks") ?></h3>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
            <?php
            $school_url = base_url("parents/school_details/{$school['uuid']}") . "/" . refine_name($school['school_name']);
            $share = array(
                'url' => $school_url,
                'email_image' => image_url("data/email-4096-black.png"),
                'email_share_modal' => $this->lbplusbuilder->get_element('schoolisting/modal-share', array('current_url' => $school_url))
            );
            echo $this->lbplusbuilder->get_element('schoolisting/share-alliance', $share);
            ?>
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->
    
    <div class="panel panel-default">
        <div class="panel-heading" style="height: 51px">
            <h3 class="panel-title" style="line-height: 30px;"><?php echo LocalizedString("Last report", "school/profile") ?></h3>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
            <form role="form" class="form-horizontal form-bordered" action="<?php echo base_url("welcome/print_report") ?>">

                <div class="input-group input-group-in">
                    <input id="datepicker2" class="form-control" name="time" required="">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div><!-- /input-group-in -->
                <p>
                	<?php echo LocalizedString("Export all statistic before this day", "school/profile") ?>
                </p>
                <br />
            	<button class="btn btn-primary" role="button"><?php echo LocalizedString("Print Report") ?></button>
            	<!-- <a target="_blank" href="<?php echo base_url("welcome/print_report") ?>" class="btn btn-primary" role="button" style="position: relative;bottom: 22px;left: 29px;">Print Report</a> -->
            </form>
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->

</div>

<div id="school-column-group" class="col-md-9">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title pull-left" style="line-height: 30px;"><span class="category_name"><?php echo LocalizedString($parent_current_category['name']) . "</span> - " . LocalizedString('Parent Rating')  ?></h3>

            <div class="btn-group pull-right">
                <a href="#" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-sitemap fa-fw"></i> <?php echo LocalizedString("Categories") ?>
                    <span class="fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu">
                    <?php foreach($parent_categories as $category) : ?>
                    <li>
                        <a href="#" class="load_statistics" data-target-type="0" data-category-name="<?php echo LocalizedString($category['name']) ?>" data-category-id="<?php echo $category['id'] ?>">
                    	<?php if ($parent_current_category['id'] == $category['id']): ?>
	                    		<i class="fa fa-check"></i>
                    	<?php else: ?>
                			<!--<a href="<?php /*echo base_url("school/profile/{$school['uuid']}/dashboard/{$category['id']}") . "/" . refine_name($school['school_name'])  */?>">-->
	                    		<i class="fa fa-caret-right"></i>
                    	<?php endif; ?>
                            <?php echo LocalizedString($category['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div style="clear: both"></div>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
            <div>
                <?php echo $pie_chart ?>
                <p style="display: inline-block;position: relative;top: 51px"><i style="position: relative;top: 2px" class="glyphicon glyphicon-chevron-left"></i><?php echo LocalizedString("Total Average Mark") ?></p>
            </div>
            <?php echo $column ?>
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title pull-left" style="line-height: 30px;"><span class="category_name"><?php echo LocalizedString($school_current_category['name']) . "</span> - " . LocalizedString('School Self-Rating') ?></h3>

            <div class="btn-group pull-right">
                <a href="#" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-sitemap fa-fw"></i> <?php echo LocalizedString("Categories") ?>
                    <span class="fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu">
                    <?php foreach($school_categories as $category) : ?>
                    <li>
                        <a href="#" class="load_statistics" data-target-type="1" data-category-name="<?php echo LocalizedString($category['name']) ?>" data-category-id="<?php echo $category['id'] ?>">
                    	<?php if ($school_current_category['id'] == $category['id']): ?>
	                    		<i class="fa fa-check"></i>
                    	<?php else: ?>
	                    		<i class="fa fa-caret-right"></i>
                    	<?php endif; ?>
                            <?php echo LocalizedString($category['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div style="clear: both"></div>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
            <div>
                <?php echo $pie_school ?>
                <p style="display: inline-block;position: relative;top: 51px"><i style="position: relative;top: 2px" class="glyphicon glyphicon-chevron-left"></i><?php echo LocalizedString("Total Average Mark") ?></p>
            </div>
            <?php echo $school_column ?>
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->

</div>
<div style="clear: both"></div>

<script>
   $(function() {
       $('.load_statistics').on('click', function(event) {
           event.preventDefault();
           var category_id = $(this).data('category-id');
           var category_name = $(this).data('category-name');
           var user_id = "<?php echo $school['user_id'] ?>";
           var target_type = $(this).data('target-type');

           var check = $(this).closest('.dropdown-menu').find('.fa-check');
           var container = $(this).closest(".panel");
           var header = $(this).closest('.panel-heading').find('.category_name');
           var h3 = $(this).closest('.panel-heading').find('.panel-title');
           var content = container.find('.panel-body');
           var url = "<?php echo base_url("school/load_statistics") ?>";

           $.ajax({
               url: url,
               type: "POST",
               context: this,
               data: { category_id: category_id, target_type: target_type, user_id: user_id },
               beforeSend: function () {
                   content.css('opacity', '.5');
                   h3.append("<i style='margin-left:10px' class='fa fa-spinner fa-spin'></i>");
                   check.parent().prepend("<i class='fa fa-caret-right'></i>");
                   check.remove();
               },
               success: function (result) {
                   content.css('opacity', '1');
                   h3.find('.fa-spinner').remove();
                   $(this).find('.fa-caret-right').remove();
                   $(this).prepend("<i class='fa fa-check'></i>");
                   header.html(category_name);
                   content.html(result);
               }
           });

       });
   });
</script>
