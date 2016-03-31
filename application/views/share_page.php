<body style="background: #f6f8fa">
<div class="panel panel-default" style="width: 50%;margin: 30px auto 50px">
    <div class="panel-heading" style="height:45px">
        <h3 class="panel-title">
            <?php echo LocalizedString("Share your Review") ?> -
            <a href="<?php echo (@$school_profile_url) ? $school_profile_url : $school_url ?>"><?php echo $school['school_name'] ?></a></h3>
    </div><!-- /panel-heading -->

    <div class="panel-body">
        <a style="bottom: 21px;;position: relative;" target="_window" onclick="return !window.open(this.href, 'Share on Google+', 'width=640, height=536')" href="https://plus.google.com/share?url=<?php echo $school_url ?>&display=popup&ref=plugin""><i class="fa fa-google-plus-square fa-3x" style="color:#D24836"></i></a>
        <a style="bottom: 21px;left: 6px;position: relative;" target="_window" onclick="return !window.open(this.href, 'Share on Facebook', 'width=500, height=536')" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $school_url ?>&display=popup&ref=plugin"><i class="fa fa-facebook-square fa-3x" style="color: #405D9B"></i></a>
        <a href="#modelShare" data-toggle="modal"><div style="background: url(<?php echo image_url("data/email-4096-black.png") ?>);width: 70px;height: 70px;display: inline-block"></div></a>

        <div style="margin-top: -10px">
            <?php echo $review_html ?>
        </div>

        <div>
            <?php if($user_type == "parents") : ?>
                <p><?php echo LocalizedString("thank-parent") ?></p>
            <?php elseif($user_type == "school") : ?>
                <p><?php echo LocalizedString("thank-school") ?></p>
            <?php endif; ?>
        </div>

        <hr>
        <h3 style="text-align: center"><?php echo LocalizedString("Start a Survey") ?></h3>
        <form id="survey_form" action="<?php echo base_url("review/submit_survey") ?>" method="post">
            <input type="hidden" name="url" value="<?php echo $school_url ?>">
            <input type="hidden" name="school_id" value="<?php echo $school['school_id'] ?>">
            <input type="hidden" name="user_id" value="<?php echo @$user['id'] ?>">
            <div class="form-group">
                <label>- <b><?php echo LocalizedString("Rate Schoolisting") ?></b></label>
                <div class="nice-radio">
                    <input type="radio" name="rate" id="niceCheck1" value="2" checked="checked">
                    <label for="niceCheck1"><?php echo LocalizedString("Schoolisting proved to be very helpful to me") ?></label>
                </div><!--/nice-checkbox-->
                <div class="nice-radio">
                    <input type="radio" name="rate" id="niceCheck2" value="1">
                    <label for="niceCheck2"><?php echo LocalizedString("Schoolisting is a good tool and does help me in some certain aspects") ?></label>
                </div><!--/nice-checkbox-->
            </div>

            <div class="form-group">
                <label>- <b><?php echo LocalizedString("How do you say about Schoolisting") ?></b></label>
                <textarea id="survey_content" required="required" name="content" class="form-control autogrow" placeholder="<?php echo LocalizedString("Type in any word from your thought") ?>" style="overflow: hidden; min-height: 8em; height: 52px;"></textarea><div class="autogrow-textarea-mirror" style="display: none; word-wrap: break-word; white-space: normal; padding: 6px 12px; width: 448px; font-family: Lato, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 19.99997901916504px;">.<br>.</div>
            </div>
            <hr>
            <div class="form-group">
                <button class="btn btn-default"><?php echo LocalizedString("Submit") ?></button>
            </div>
        </form>
    </div><!-- /panel-body -->
</div>
<?php
    if(!isset($customers_emails)) {
        $customers_emails = "";
    }
    echo $this->lbplusbuilder->get_element('schoolisting/school-share-modal', array('emails'=> $customers_emails,'content'=>$content,'current_url' => $school_url));
?>
<script>
    $(function() {
        $('#survey_form').submit(function(e) {
            var review = $('#survey_content').val();
            var message = "<?php echo LocalizedString("Your review should not be that short (at least 10 characters)") ?>";
            if(review.length < 10) {
                alert(message);
                e.preventDefault();
            } else {
                alert("<?php echo LocalizedString("Thank you for raising your voice to help us improve schoolisting") ?>");
            }
        });
    });
</script>