<body style="background: #f6f8fa">
<div id="review-school-info">
    <a href="<?php echo base_url("parents/school_details/{$school['uuid']}") . "/" . refine_name($school['school_name']) ?>"><div id="review-school-avatar" style="background-image: url(<?php echo image_render($school['avatar']) ?>)"></div></a>
    <div id="review-school-data">
        <a href="<?php echo base_url("parents/school_details/{$school['uuid']}") . "/" . refine_name($school['school_name']) ?>"><h3><?php echo $school['school_name'] ?></h3></a>
        <p><?php echo LocalizedString("Type") ?>: <?php echo LocalizedString($school['type']) ?></p>
        <p><?php echo LocalizedString("Grade") ?>: <?php echo LocalizedString($school['grade']) ?></p>
    </div>
</div>

<div class="panel panel-default" style="width: 50%;margin: 30px auto 50px">
    <div class="panel-heading" style="height:45px">
        <h3 class="panel-title"><?php echo LocalizedString("Write a review about ") . " " . $school['school_name'] ?></h3>
    </div><!-- /panel-heading -->

    <div class="panel-body">
        <form id="review_form" role="form" class="form-bordered" action="<?php echo base_url("review/review_submit") ?>" method="post">
            <input type="hidden" name="user_id" value="<?php echo $parent['id'] ?>">
            <input type="hidden" name="school_id" value="<?php echo $school['school_id'] ?>">
            <div class="form-group">
                <label for="review"><?php echo LocalizedString("Review") ?></label>
                <textarea required="required" style="height: 300px" rows="3" class="form-control" id="review" name="review"></textarea>
            </div><!-- /form-group -->
            <div class="form-group">
                <button type="submit" class="btn btn-default"><?php echo LocalizedString("Submit Review") ?></button>
            </div>
        </form><!-- /form -->
    </div><!-- /panel-body -->
</div>
<script>
    $(function() {
        $("#review_form").submit(function(e) {
            var review = $("#review").val();
            var message = "<?php echo LocalizedString("Your review should not be that short (at least 10 characters)") ?>";
            if(review.length < 10) {
                alert(message);
                e.preventDefault();
            }
        });
    });
</script>