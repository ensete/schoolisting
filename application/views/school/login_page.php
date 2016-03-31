<body>

<main class="signin-wrapper">
    <div class="tab-content">
        <div class="tab-pane active" id="signin">
            <a href="<?php echo base_url("school") ?>"><h2 class="signin-brand animated-hue"><i class="fa fa-rocket"></i> Schoolisting</h2></a>
            <form name="signinForm" id="signinForm" action="<?php echo base_url("school/handle_login") ?>" method="post" role="form">
                <p class="lead"><?php echo LocalizedString("Login to your School Account") ?></p>
                <?php if($success = $this->session->flashdata("success")) : ?>
                    <?php echo $this->lbplusbuilder->get_element('schoolisting/success', array('message' => LocalizedString($success))); ?>
                <?php endif; ?>

                <?php if($error = $this->session->flashdata("error")) : ?>
                    <?php echo $this->lbplusbuilder->get_element('schoolisting/error', array('message' => LocalizedString($error))); ?>
                <?php endif; ?>

                <?php echo $this->lbplusbuilder->get_element('schoolisting/login-form', array('placeholder-pass'=>LocalizedString("Password"),'placeholder-username'=>LocalizedString("Username"),'remember'=>LocalizedString("Keep me sign in"),'username' => $this->session->flashdata("username"))); ?>

                <p><a id="recover_trigger" href="#recoverAccount" data-toggle="modal"><?php echo LocalizedString("Can't Access your Account?") ?></a></p>
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
                <p><?php echo LocalizedString("Don't have an account?") ?> <a href="<?php echo base_url("school/register") ?>" ><?php echo LocalizedString("Create an Account") ?></a></p>
            </form><!-- /#signinForm -->
        </div><!-- /.tab-pane -->

    </div><!-- /.tab-content -->
</main><!--/#wrapper-->

<?php echo $this->lbplusbuilder->get_element('schoolisting/recover-modal', array('error' => $this->session->flashdata('recover_error'), 'form-action' => base_url('school/forgot_password'))); ?>
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