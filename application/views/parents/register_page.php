<main class="signin-wrapper">
    <div class="tab-pane" id="signup">
        <a href="<?php echo base_url() ?>"><h2 class="signin-brand animated-hue"><i class="fa fa-rocket"></i> Schoolisting</h2></a>
        <form id="signupForm" role="form" method="post" action="<?php echo base_url("parents/handle_register") ?>">
            <p class="lead"><?php echo LocalizedString("Create a new account") ?></p>

            <?php if($success = $this->session->flashdata("success")) : ?>
                <div class="alert alert-success fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo LocalizedString($success) ?>
                </div>
            <?php endif; ?>

            <?php if($error = $this->session->flashdata("error")) : ?>
                <div class="alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo LocalizedString($error) ?>
                </div>
            <?php endif; ?>
            <p class="text-muted"><strong><?php echo LocalizedString("Enter your data in details") ?></strong></p>
            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-font"></i></span>
                    <input required name="full_name" id="full_name" class="form-control" placeholder="<?php echo LocalizedString("Full Name") ?>"
                           value="<?php echo ($full_name = $this->session->flashdata("full_name")) ? $full_name : "" ?>">
                    <span class="form-control-feedback"></span>
                </div>
                <!-- /.form-group -->
            </div>

            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    <input required type="email" name="email" id="email" class="form-control" placeholder="Email"
                           value="<?php echo ($email = $this->session->flashdata("email")) ? $email : "" ?>">
                    <span class="form-control-feedback"></span>
                </div>
            </div>
            <!-- /.form-group -->
            <hr>

            <p class="text-muted"><strong><?php echo LocalizedString("Enter your account data") ?></strong></p>
            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input required name="username" id="username" class="form-control" placeholder="<?php echo LocalizedString("Username") ?>"
                           value="<?php echo ($username = $this->session->flashdata("username")) ? $username : "" ?>">
                    <span class="form-control-feedback"></span>
                </div>
            </div>
            <!-- /.form-group -->
            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                    <input required type="password" name="password" id="password" class="form-control" placeholder="<?php echo LocalizedString("Password") ?>">
                    <span class="form-control-feedback"></span>
                </div>
            </div>
            <!-- /.form-group -->
            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-check"></i></span>
                    <input required type="password" name="confirm_password" id="confirm_password" class="form-control"
                           placeholder="<?php echo LocalizedString("Enter password again") ?>">
                    <span class="form-control-feedback"></span>
                </div>
            </div>
            <!-- /.form-group -->

            <?php echo $methods ?>

            <div class="form-group animated-hue clearfix">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary"><?php echo LocalizedString("Create an account") ?> <i
                            class="fa fa-chevron-circle-right"></i></button>
                </div>
                <div class="pull-left">
                    <a href="<?php echo base_url("parents/login") ?>" class="btn btn-default"><i
                            class="fa fa-chevron-circle-left fa-fw"></i> <?php echo LocalizedString("Signin") ?></a>
                </div>
            </div>
            <!-- /.form-group -->
        </form>
        <!-- /#signupForm -->
        <hr>
    </div>
    <!-- /.tab-pane -->
</main>