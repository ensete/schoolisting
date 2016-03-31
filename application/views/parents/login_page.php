<body>
<main class="signin-wrapper">
    <div class="tab-content">
        <div class="tab-pane active" id="signin">
            <a href="<?php echo base_url() ?>"><h2 class="signin-brand animated-hue"><i class="fa fa-rocket"></i> Schoolisting</h2></a>
            <form name="signinForm" id="signinForm" action="<?php echo base_url("parents/handle_login") ?>" method="post" role="form">
                <p class="lead"><?php echo LocalizedString("Login to your Parent Account") ?></p>
                <?php if($success = $this->session->flashdata("success")) : ?>
                    <div class="alert alert-success fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <strong>Done!</strong> <?php echo LocalizedString($success) ?>
                    </div>
                <?php endif; ?>

                <?php if($error = $this->session->flashdata("error")) : ?>
                    <div class="alert alert-danger fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <strong>Oops!</strong> <?php echo LocalizedString($error) ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <input required name="username" id="username" class="form-control" placeholder="<?php echo LocalizedString("Username") ?>" value="<?php echo ($username = $this->session->flashdata("username")) ? $username : ""?>">
                    </div>
                </div><!-- /.form-group -->
                <div class="form-group">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo LocalizedString("Password") ?>">
                    </div>
                </div><!-- /.form-group -->
                <div class="form-group clearfix">
                    <div class="animated-hue pull-right">
                        <button id="btnSignin" type="submit" class="btn btn-primary" ><?php echo LocalizedString("Signin") ?> <i class="fa fa-chevron-circle-right"></i></button>
                    </div>
                    <div class="nice-checkbox nice-checkbox-inline">
                        <input type="checkbox" name="keepSignin" id="keepSignin">
                        <label for="keepSignin"><?php echo LocalizedString("Keep me sign in") ?></label>
                    </div>
                </div><!-- /.form-group -->

                <hr>

                <p><a id="recover_trigger" href="#recoverAccount" data-toggle="modal"><?php echo LocalizedString("Can't Access your Account?") ?></a></p>
                <p class="lead"><?php echo LocalizedString("Signin with another account"); ?></p>
                <div class="signin-alt">
                    <a href="<?php echo base_url("parents/login_by_google") ?>" class="btn btn-sm btn-danger"><i class="fa fa-google-plus"></i></a>
                </div>
                <?php if($this->session->flashdata("reset_code")) : ?>
                    <p><a id="reset_code_trigger" href="#reset_code_modal" data-toggle="modal"><?php echo LocalizedString("Enter your Reset Password Code") ?></a></p>
                    <script>
                        $(function() {
                            $('#reset_code_trigger').click();
                        });
                    </script>
                <?php elseif($this->session->flashdata("recover_code")) : ?>
                    <script>
                        $(function() {
                            $('#recover_trigger').click();
                        });
                    </script>
                <?php endif; ?>
                <hr>

                <p><?php echo LocalizedString("Don't have an account?") ?> <a href="<?php echo base_url("parents/register") ?>" ><?php echo LocalizedString("Create an Account") ?></a></p>
            </form><!-- /#signinForm -->
        </div><!-- /.tab-pane -->

    </div><!-- /.tab-content -->
</main><!--/#wrapper-->


<?php echo $this->lbplusbuilder->get_element('schoolisting/recover-modal', array('error' => $this->session->flashdata('recover_error'), 'form-action' => base_url('parents/forgot_password'))); ?>
<?php echo $this->lbplusbuilder->get_element('schoolisting/resetpass-modal', array('error' => $this->session->flashdata('reset_error'), 'form-action' => base_url('welcome/check_reset_code'))); ?>
<script>
    $(function() {
        $('#reset_close').on('click', function() {
            $('#reset_error').html("");
        });
        $('#recover_close').on('click', function() {
            $('#recover_error').html("");
        });
    })
</script>