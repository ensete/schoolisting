<body>
<main id="wrapper" class="wrapkit-wrapper profile">
    <section class="content-wrapper profile">
        <div class="content-header">
            <h1 class="content-title"><?php echo $school['school_name'] ?> <?php echo LocalizedString("Profile") ?></h1>
        </div>
        <!-- /.content-header -->

        <div class="profile-header clearfix"
             style="background-image: url('<?php echo image_url("dummy/trianglify6.svg") ?>')">
            <div class="profile-avatar kit-avatar kit-avatar-128 border-transparent pull-left">
                <img class="profile-avatar-img" src="<?php echo image_render($school['avatar']) ?>" alt="bent smith">
                <div class="image_loader"><img
                        src="<?php echo base_url("assets/images/ajax-loader/ajax-loader.gif") ?>"></div>
                <label for="change_avatar">
                    <div class="change_avatar"><?php echo LocalizedString("Edit Avatar") ?></div>
                </label>
                <form method="post" action="<?php echo base_url("parents/handle_change_avatar") ?>"
                      enctype="multipart/form-data" style="display: none">
                    <input type="file" name="avatar" id="change_avatar" accept="image/*">
                </form>
            </div>

            <p class="profile-status"><?php echo LocalizedString("Basic Information") ?></p>

            <div class="profile-data">
                <i class="fa fa-envelope fa-fw"></i><?php echo LocalizedString("Email") . ": " ?><?php echo $school['email'] ?><br>
            </div>
            <div class="profile-data">
                <i class="fa fa-map-marker fa-fw"></i><?php echo LocalizedString("Address") . ": " ?><?php echo $school['address'] ?><br>
            </div>
            <div class="profile-data">
                <i class="fa fa-phone fa-fw"></i><?php echo LocalizedString("Phone") . ": " ?><?php echo $school['telephone'] ?><br>
            </div>
            <div class="profile-data">
                <i class="fa fa-cog fa-fw"></i><?php echo LocalizedString("Status") . ": " ?><?php echo ($school['appearance'] == 1) ? LocalizedString("Public","user_type") : LocalizedString("Private","user_type") ?><br>
            </div>
        </div>
        <!-- /.profile-header -->

        <div class="content-actions">
            <ul class="nav nav-tabs nav-tabs-alt hidden-sm hidden-xs pull-left">
                <li <?php echo ($tab == "dashboard") ? 'class="active"' : '' ?>><a href="<?php echo base_url("school/profile/{$school['uuid']}/dashboard") . "/" . refine_name($school['school_name']) ?>"><?php echo LocalizedString("Dashboard") ?></a></li>
                <li <?php echo ($tab == "evaluation") ? 'class="active"' : '' ?>><a href="<?php echo base_url("questionnaire/questionnaire_for_school") ?>"><?php echo LocalizedString("Evaluation") ?></a></li>
                <li <?php echo ($tab == "plan-actions") ? 'class="active"' : '' ?>><a href="<?php echo base_url("school/profile/{$school['uuid']}/plan-actions") . "/" . refine_name($school['school_name']) ?>"><?php echo LocalizedString("Plan Actions") ?></a></li>
                <li <?php echo ($tab == "files") ? 'class="active"' : '' ?>><a href="<?php echo base_url("school/profile/{$school['uuid']}/files") . "/" . refine_name($school['school_name']) ?>"><?php echo LocalizedString("Files") ?></a></li>
                <li <?php echo ($tab == "reviews") ? 'class="active"' : '' ?>><a href="<?php echo base_url("school/profile/{$school['uuid']}/reviews") . "/" . refine_name($school['school_name']) ?>"><?php echo LocalizedString("Reviews") ?></a></li>
                <li <?php echo ($tab == "rates") ? 'class="active"' : '' ?>><a href="<?php echo base_url("school/profile/{$school['uuid']}/rates") . "/" . refine_name($school['school_name']) ?>"><?php echo LocalizedString("Rates") ?></a></li>
                <li <?php echo ($tab == "upload-customer-data") ? 'class="active"' : '' ?>><a href="<?php echo base_url("school/profile/{$school['uuid']}/upload-customer-data") . "/" . refine_name($school['school_name']) ?>"><?php echo LocalizedString("Upload Customers' Data") ?></a></li>
                <li <?php echo ($tab == "saved-answers") ? 'class="active"' : '' ?>><a href="<?php echo base_url("school/profile/{$school['uuid']}/saved-answers") . "/" . refine_name($school['school_name']) ?>"><?php echo LocalizedString("Saved Answers") ?></a></li>
            </ul>
            <!-- /.nav -->
            <div class="pull-right">
                <div class="btn-group">
                    <a href="#" class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="<?php echo base_url("school/change_password") ?>"><?php echo LocalizedString("Change Password") ?></a></li>
                        <li><a href="<?php echo base_url("school/logout") ?>"><?php echo LocalizedString("Logout") ?></a></li>
                        <li class="divider"></li>
                        <li><a id="delete_account" href="<?php echo base_url("school/delete_account") ?>"><?php echo LocalizedString("Delete Account") ?></a></li>
                        <li class="divider"></li>
                        <li><a href="mailto:<?php echo get_setting("email") ?>" target="_top"><?php echo LocalizedString("Contact to Schoolisting") ?></a></li>
                    </ul>
                </div>
                <!-- /.btn-group -->
                <a href="<?php echo base_url("school/edit_profile") ?>" class="btn btn-sm btn-primary"><?php echo LocalizedString("Edit") ?></a>
            </div>
        </div>
        <!-- /.content-actions -->

        <div class="content">
            <div class="tab-content">
                <?php echo $content; ?>
            </div>
        </div>

    </section>
    <!--/.content-wrapper-->
</main>
<!--/#wrapper-->

<!-- Modal Setups -->
<!-- Modal -->
<div class="modal fade" id="templateSetup" tabindex="-1" role="dialog" aria-labelledby="templateSetupLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<script>
    $(function () {
        $('#change_avatar').change(function () {
            var url = "<?php echo base_url("school/avatar_upload/{$school['id']}") ?>";
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

        $(document).on('click', '#more_reviews', function() {
            $(this).attr('id','none');
            var url = "<?php echo base_url("school/load_more_reviews") ?>";
            var school_id = <?php echo $school['school_id'] ?>;
            var position = $(this).data('position');
            var data = "school_id=" + school_id + "&position=" + position;
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
                    var object = $('#review');
                    if(result['reviews']) {
                        object.append(result['reviews']);
                        object.append('<div class="text-center"><button type="button" id="more_reviews" class="btn btn-sm btn-default" data-position="'+result['position']+'">Load more...</button></div>');
                    } else {
                        object.append('<div class="text-center">End of Results</div>');
                    }
                }
            });
        });

        $(document).on('click', '#more_rates', function() {
            $(this).attr('id','none');
            var url = "<?php echo base_url("school/load_more_rates") ?>";
            var school_user_id = <?php echo $school['id'] ?>;
            var position = $(this).data('position');
            var data = "school_user_id=" + school_user_id + "&position=" + position;
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
                    var object = $('#rate');
                    if(result['rates']) {
                        object.append(result['rates']);
                        object.append('<div class="text-center"><button type="button" id="more_rates" class="btn btn-sm btn-default" data-position="'+result['position']+'">Load more...</button></div>');
                    } else {
                        object.append('<div class="text-center">End of Results</div>');
                    }
                }
            });
        });

    });
</script>
