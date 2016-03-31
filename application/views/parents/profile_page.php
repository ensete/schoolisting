<body>
<main id="wrapper" class="wrapkit-wrapper profile">
    <section class="content-wrapper profile">
        <div class="content-header">
            <h1 class="content-title"><?php echo $user['full_name'] . " " . LocalizedString("Profile") ?></h1>
        </div>
        <!-- /.content-header -->

        <div class="profile-header clearfix"
             style="background-image: url('<?php echo image_url("dummy/trianglify6.svg") ?>')">
            <div class="profile-avatar kit-avatar kit-avatar-128 border-transparent pull-left">
                <img class="profile-avatar-img" src="<?php echo image_render($user['avatar']) ?>" alt="bent smith">
                <?php if ($is_owner) : ?>
                    <div class="image_loader"><img
                            src="<?php echo base_url("assets/images/ajax-loader/ajax-loader.gif") ?>"></div>
                    <label for="change_avatar">
                        <div class="change_avatar"><?php echo LocalizedString("Edit Avatar") ?></div>
                    </label>
                    <form method="post" action="<?php echo base_url("parents/handle_change_avatar") ?>"
                          enctype="multipart/form-data" style="display: none">
                        <input type="file" name="avatar" id="change_avatar" accept="image/*">
                    </form>
                <?php endif; ?>
            </div>

            <p class="profile-status"><?php echo LocalizedString("Basic Information") ?></p>

            <div class="profile-data">
                <i class="fa fa-envelope fa-fw"></i><?php echo LocalizedString("Email") . ": " ?><?php echo $user['email'] ?><br>
                <?php if (isset($user['location'])) : ?>
                    <i class="fa fa-map-marker fa-fw"></i><?php echo LocalizedString("Address") . ": " ?><?php echo $user['location']['district_name'] . ' - ' . $user['location']['city_name'] ?><br>
                <?php endif; ?>
                <?php if (isset($user['telephone'])) : ?>
                    <i class="fa fa-phone fa-fw"></i><?php echo LocalizedString("Phone") . ": " ?><?php echo $user['telephone'] ?><br>
                <?php endif; ?>
                <?php if (isset($user['children_numbers'])) : ?>
                    <i class="fa fa-cog fa-fw"></i><?php echo LocalizedString("Status") . ": " ?><?php echo ($user['appearance'] == 1) ? LocalizedString("Public", "user_type") : LocalizedString("Private", "user_type") ?>
                    <br>
                <?php endif; ?>
            </div>
        </div>
        <!-- /.profile-header -->

        <div class="content-actions">
            <ul class="nav nav-tabs nav-tabs-alt hidden-sm hidden-xs pull-left">
                <?php if($is_newcomer) : ?>
                    <li class="active"><a data-toggle="tab" href="#welcome"><?php echo LocalizedString("Welcome") ?></a></li>
                <?php endif; ?>
                <li class="<?php echo (!$is_newcomer) ? "active" : "" ?>"><a data-toggle="tab" href="#bookmark"><?php echo LocalizedString("Bookmarks") ?></a></li>
                <li><a data-toggle="tab" href="#recommendation"><?php echo LocalizedString("Recommendations") ?></a></li>
                <li><a data-toggle="tab" href="#review"><?php echo LocalizedString("Reviews") ?></a></li>
                <li><a data-toggle="tab" href="#rate"><?php echo LocalizedString("Rates") ?></a></li>
                <li><a data-toggle="tab" href="#saved_answers"><?php echo LocalizedString("Saved Answers") ?></a></li>
            </ul>
            <!-- /.nav -->
            <div class="btn-group visible-sm visible-xs pull-left">
                <a href="#" class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                </a>
                <ul class="dropdown-menu pull-left" role="menu">
                    <li class="active"><a data-toggle="tab" href="#bookmark"><?php echo LocalizedString("About") ?></a></li>
                </ul>
            </div>
            <!-- /.btn-group -->

            <div class="pull-right">
                <div class="btn-group">
                    <?php if ($is_owner) : ?>
                    <a href="#" class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <?php if(!$user['google_id']) : ?>
                        <li><a href="<?php echo base_url("parents/change_password") ?>"><?php echo LocalizedString("Change Password") ?></a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo base_url("parents/logout") ?>"><?php echo LocalizedString("Logout") ?></a></li>
                        <li class="divider"></li>
                        <li><a id="delete_account" href="<?php echo base_url("parents/delete_account") ?>"><?php echo LocalizedString("Delete Account") ?></a></li>
                    </ul>
                    <?php endif; ?>
                </div>
                <!-- /.btn-group -->
                <?php if ($is_owner) : ?>
                    <a href="<?php echo base_url("parents/edit_profile") ?>" class="btn btn-sm btn-primary"><?php echo LocalizedString("Edit") ?></a>
                <?php endif; ?>
            </div>
            <!-- /.pull-right -->
        </div>
        <!-- /.content-actions -->

        <div class="content">

            <!-- PROFILE
            ================================================== -->
            <div class="tab-content">

                <div class="tab-pane" id="saved_answers">
                    <?php if($saved_answers) : ?>
                        <div class="tab-pane" id="posts-answer">
                            <div class="posts-wrapper" id="mediaObject-answer">
                                <?php foreach($saved_answers as $answer) : ?>
                                <div class="post-item" style="margin-right:15px;display:inline-block;width: 23%;text-align: center;vertical-align: top">
                                    <h4 class="post-title" style="margin: 5px 0 10px;">
                                        <a href="<?php echo base_url("parents/school_details/{$answer['uuid']}") ?>"><?php echo $answer['school_name'] ?></a>
                                    </h4>
                                    <span class="text-muted"><?php echo $answer['total'] . " " . LocalizedString("questions") ?></span>
                                    <hr>
                                    <div class="post-content">
                                        <div class="media" style="height: 160px">
                                            <a class="post-img" style="float: none;margin: 0;" href="<?php echo base_url("parents/school_details/{$answer['uuid']}") ?>">
                                                <img style="object-fit: cover;height: 115px;" src="<?php echo image_render($answer['avatar'], "school") ?>" alt="<?php $answer['school_name'] ?>">
                                            </a>
                                            <a style="margin-top:10px" href="<?php echo base_url("questionnaire/questionnaire_for_parent?name={$answer['school_name']}") ?>" class="btn btn-primary" role="button"><?php echo LocalizedString("Continue") ?></a>
                                        </div>
                                    </div><!-- /.post-content -->
                                    <hr>
                                    <div class="help-block">
                                        <div class="text-muted"><?php echo date("d-m-Y", strtotime($answer['created'])) ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div><!-- /.posts-wrapper -->
                        </div><!-- /.tab-pane #posts -->
                    <?php else : ?>
                        <div class="about-wrapper">
                            <blockquote>
                                <p class="lead"><?php echo LocalizedString("No ongoing rating is found") ?></p>
                            </blockquote>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- /.tab-pane #save_answers -->

                <div class="tab-pane <?php echo (!$is_newcomer) ? "active" : "" ?>" id="bookmark">
                    <?php if($bookmarked_schools) : ?>
                        <div class="tab-pane" id="posts-bookmark">
                            <div class="posts-wrapper" id="mediaObject-bookmark">
                                <?php echo $bookmarked_schools ?>
                            </div><!-- /.posts-wrapper -->
                        </div><!-- /.tab-pane #posts -->
                    <?php else : ?>
                        <div class="about-wrapper">
                            <blockquote>
                                <p class="lead"><?php echo LocalizedString("No bookmark of any school") ?></p>
                            </blockquote>
                        </div>
                    <?php endif; ?>
                    <?php if($bookmarked_total_groups > 1) : ?>
                    <div class="text-center">
                        <button type="button" id="more_bookmarks" class="btn btn-sm btn-default" data-position="<?php echo $bookmarked_position ?>">Load more...</button>
                    </div>
                    <?php endif; ?>
                </div>
                <!-- /.tab-pane #bookmark -->

                <div class="tab-pane" id="recommendation">
                    <?php if($recommended_schools) : ?>
                        <div class="tab-pane" id="posts-recommendation">
                            <div class="posts-wrapper" id="mediaObject-recommendation">
                                <?php echo $recommended_schools ?>
                            </div><!-- /.posts-wrapper -->
                        </div><!-- /.tab-pane #posts -->
                    <?php else : ?>
                        <div class="about-wrapper">
                            <blockquote>
                                <p class="lead"><?php echo LocalizedString("No recommendation for any school") ?></p>
                            </blockquote>
                        </div>
                    <?php endif; ?>
                    <?php if($recommendation_total_groups > 1) : ?>
                        <div class="text-center">
                            <button type="button" id="more_recommendations" class="btn btn-sm btn-default" data-position="<?php echo $recommendation_position ?>"><?php echo LocalizedString("Load more") ?>...</button>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- /.tab-pane #recommendation -->

                <div class="tab-pane" id="review">
                    <div class="posts-wrapper" id="review-main">
                        <?php if($review_html) : ?>
                            <?php echo $review_html; ?>
                        <?php else : ?>
                            <div class="about-wrapper">
                                <blockquote>
                                    <p class="lead"><?php echo LocalizedString("No review for any school") ?></p>
                                </blockquote>
                            </div>
                        <?php endif; ?>
                    </div>
                </div><!-- /.tab-pane #review -->

                <div class="tab-pane" id="rate">
                    <div class="posts-wrapper" id="rate-main">
                        <?php if($rate_html) : ?>
                            <?php echo $rate_html; ?>
                        <?php else : ?>
                            <div class="about-wrapper">
                                <blockquote>
                                    <p class="lead"><?php echo LocalizedString("No rating for any school") ?></p>
                                </blockquote>
                            </div>
                        <?php endif; ?>
                    </div>
                </div> <!-- /.tab-pane #review -->

                <?php if($is_newcomer) : ?>
                    <div class="tab-pane active" id="welcome">
                        <div class="posts-wrapper" id="welcome-main">
                            <h4><?php echo LocalizedString("From here you can") ?> <a style="margin-left:15px" href="<?php echo base_url() ?>" class="btn btn-primary btn-lg" role="button"><?php echo LocalizedString("Find a school") ?></a></h4>
                            <h4><?php echo LocalizedString("Or") ?> <a style="margin-left:15px" href="<?php echo base_url('review/find_your_school') ?>" class="btn btn-primary btn-lg" role="button"><?php echo LocalizedString("Review your school") ?></a></h4>
                            <h4><?php echo LocalizedString("Need for some useful tips?") ?> <a style="margin-left:15px" href="http://schoolisting.net/topics/parents/" class="btn btn-primary btn-lg" role="button"><?php echo LocalizedString("Parent Tips") ?></a></h4>
                            <?php
                            $share = array(
                                'url' => base_url(),
                                'email_image' => image_url("data/email-4096-black.png"),
                                'email_share_modal' => $this->lbplusbuilder->get_element('schoolisting/modal-share', array('current_url' => base_url()))
                            );
                            echo $this->lbplusbuilder->get_element('schoolisting/share-alliance', $share);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
            <!-- /.tab-content -->


        </div>
        <!-- /.content -->
    </section>
    <!--/.content-wrapper-->


</main>
<!--/#wrapper-->

<?php echo $this->lbplusbuilder->get_element('schoolisting/refer-modal', array('placeholder'=>LocalizedString("Enter your friend's email"),'submit'=>LocalizedString("Submit"),'close'=>LocalizedString("Close"),'title' => LocalizedString("Refer this school to your friend"))); ?>

<script>
    $(function () {
        $('#change_avatar').change(function () {
            var url = "<?php echo base_url("parents/avatar_upload/{$user['id']}") ?>";
            var formdata = new FormData();
            var file = $('#change_avatar')[0].files[0];
            if (file) {
                formdata.append('avatar', file);
                var ajax = new XMLHttpRequest();
                $('.image_loader').show();
                ajax.addEventListener("load", function (event) {
                    var img = event.target.responseText;
                    $('.image_loader').hide();
                    $('.profile-avatar-img').attr('src', img);
                });
                ajax.open("POST", url);
                ajax.send(formdata);
            }
        });

        $('#delete_account').on('click', function () {
            var r = confirm("Are you sure?");
            if (!r) {
                return false;
            }
        });
    });

    $(document).on('click', '#more_bookmarks', function() {
        $(this).attr('id','none');
        var url = "<?php echo base_url("parents/load_more_bookmarks") ?>";
        var user_id = <?php echo $user['id'] ?>;
        var current_id = <?php echo $current_id ?>;
        var position = $(this).data('position');
        var data = "user_id=" + user_id + "&current_id=" + current_id + "&position=" + position;
        $.ajax({
            url: url,
            type: "POST",
            context: this,
            data: data,
            dataType:"json",
            beforeSend: function () {
                $(this).html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
            },
            success: function (result) {
                $(this).remove();
                var object = $('#mediaObject-bookmark');
                if(result['schools']) {
                    object.append(result['schools']);
                    object.append('<div class="text-center"><button type="button" id="more_bookmarks" class="btn btn-sm btn-default" data-position="'+result['position']+'">Load more...</button></div>');
                } else {
                    object.append('<div class="text-center">End of Results</div>');
                }
            }
        });
    });

    $(document).on('click', '#more_recommendations', function() {
        $(this).attr('id','none');
        var url = "<?php echo base_url("parents/load_more_recommendations") ?>";
        var user_id = <?php echo $user['id'] ?>;
        var current_id = <?php echo $current_id ?>;
        var position = $(this).data('position');
        var data = "user_id=" + user_id + "&current_id=" + current_id + "&position=" + position;
        $.ajax({
            url: url,
            type: "POST",
            context: this,
            data: data,
            dataType:"json",
            beforeSend: function () {
                $(this).html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
            },
            success: function (result) {
                $(this).remove();
                var object = $('#mediaObject-recommendation');
                if(result['schools']) {
                    object.append(result['schools']);
                    object.append('<div class="text-center"><button type="button" id="more_recommendations" class="btn btn-sm btn-default" data-position="'+result['position']+'">Load more...</button></div>');
                } else {
                    object.append('<div class="text-center">End of Results</div>');
                }
            }
        });
    });

    $(document).on('click', '#more_reviews', function() {
        $(this).attr('id','none');
        var url = "<?php echo base_url("parents/load_more_reviews") ?>";
        var user_id = <?php echo $user['id'] ?>;
        var position = $(this).data('position');
        var data = "user_id=" + user_id + "&position=" + position;
        $.ajax({
            url: url,
            type: "POST",
            context: this,
            data: data,
            dataType:"json",
            beforeSend: function () {
                $(this).html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
            },
            success: function (result) {
                $(this).remove();
                var object = $('#review-main');
                if(result['reviews']) {
                    object.append(result['reviews']);
                    object.append('<div class="text-center"><button type="button" id="more_reviews" class="btn btn-sm btn-default" data-position="'+result['position']+'">Load more...</button></div>');
                } else {
                    object.append('<div class="text-center">End of Results</div>');
                }
            }
        });
    });

</script>
