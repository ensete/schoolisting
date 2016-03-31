<?php if($saved_answers) : ?>
    <div class="posts-wrapper" id="mediaObject-answer">
        <?php foreach($saved_answers as $answer) : ?>
            <div class="post-item" style="margin:0 auto;width: 50%;text-align: center;">
                <h4 class="post-title" style="margin: 5px 0 10px;">
                    <a href="<?php echo base_url("questionnaire/questionnaire_for_school?name={$answer['school_name']}") ?>"><?php echo $answer['school_name'] ?></a>
                </h4>
                <span class="text-muted"><?php echo $answer['total'] . " " . LocalizedString("questions") ?></span>
                <hr>
                <div class="post-content">
                    <div class="media">
                        <a class="post-img" style="float: none;margin: 0;" href="<?php echo base_url("questionnaire/questionnaire_for_school?name={$answer['school_name']}") ?>">
                            <img src="<?php echo image_render($answer['avatar']) ?>" alt="<?php $answer['school_name'] ?>">
                        </a>
                    </div>
                </div><!-- /.post-content -->
                <hr>
                <div class="help-block">
                    <div class="text-muted"><?php echo date("d-m-Y", strtotime($answer['created'])) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div><!-- /.posts-wrapper -->
<?php else : ?>
    <div class="about-wrapper">
        <blockquote>
            <p class="lead"><?php echo LocalizedString("No ongoing rating is found") ?></p>
        </blockquote>
    </div>
<?php endif; ?>