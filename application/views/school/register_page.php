<?php $flash_data = $this->session->flashdata("data"); ?>
<main class="signin-wrapper" style="width: 550px">
    <div class="tab-pane" id="signup">
        <a href="<?php echo base_url("school") ?>"><h2 class="signin-brand animated-hue"><i class="fa fa-rocket"></i> Schoolisting</h2></a>
        <form id="signupForm" role="form" method="post" action="<?php echo base_url("school/handle_register") ?>">
            <p class="lead"><?php echo LocalizedString("Create a new school") ?></p>

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

            <p class="text-muted"><strong><?php echo LocalizedString("Enter your school data") ?></strong></p>
            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-font"></i></span>
                    <input required name="school_name" id="school_name" class="form-control" placeholder="<?php echo LocalizedString("School Name") ?>"
                           value="<?php echo (isset($flash_data['school_name'])) ? $flash_data['school_name'] : "" ?>">
                    <span class="form-control-feedback"></span>
                </div>
                <!-- /.form-group -->
            </div>

            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    <input required type="email" name="email" id="email" class="form-control" placeholder="<?php echo LocalizedString("Email Address") ?>"
                           value="<?php echo (isset($flash_data['email'])) ? $flash_data['email'] : $this->session->flashdata("email") ?>">
                    <span class="form-control-feedback"></span>
                </div>
            </div>
            <!-- /.form-group -->

            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input required name="username" id="username" class="form-control" placeholder="<?php echo LocalizedString("Username") ?>"
                           value="<?php echo (isset($flash_data['username'])) ? $flash_data['username'] : "" ?>">
                    <span class="form-control-feedback"></span>
                </div>
            </div>
            <!-- /.form-group -->

            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                    <input required="required" type="text" id="telephone" name="telephone" class="form-control" placeholder="<?php echo LocalizedString("Telephone") ?>" value="<?php echo (isset($flash_data['telephone']) ? $flash_data['telephone'] : "")?>">
                </div><!-- /input-group-in -->
            </div><!--/form-group-->

            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-home"></i></span>
                    <input required type="text" name="address" id="address" class="form-control" placeholder="<?php echo LocalizedString("Address") ?>"
                           value="<?php echo (isset($flash_data['address'])) ? $flash_data['address'] : "" ?>">
                    <span class="form-control-feedback"></span>
                </div>
            </div>
            <!-- /.form-group -->

            <?php echo $location; ?>

            <div class="form-group has-feedback">
                <div class="input-group input-group-in">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="number" required="required" name="establishment_year" id="establishment_year" class="form-control" placeholder="<?php echo LocalizedString("Year of Establishment") ?>"
                           value="<?php echo (isset($flash_data['establishment_year'])) ? $flash_data['establishment_year'] : "" ?>">
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
                           placeholder="<?php echo LocalizedString("Enter Password Again") ?>">
                    <span class="form-control-feedback"></span>
                </div>
            </div>
            <!-- /.form-group -->

            <hr>
            <p class="text-muted"><strong><?php echo LocalizedString("Enter your data in details") ?></strong></p>

            <?php
            echo $all;
            ?>

            <div class="form-group animated-hue clearfix">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary"><?php echo LocalizedString("Create account") ?> <i
                            class="fa fa-chevron-circle-right"></i></button>
                </div>
                <div class="pull-left">
                    <a href="<?php echo base_url("school/login") ?>" class="btn btn-default"><i
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