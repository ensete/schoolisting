<body style="background: #f6f8fa">
<div class="panel panel-default" style="margin: 50px auto; width: 90%">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $form_title ?></h3>
    </div><!-- /panel-heading -->

    <?php if($success = $this->session->flashdata("success")) : ?>
        <div class="alert alert-success fade in" style="margin: 10px auto -5px;width: 50%;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <strong>Done!</strong> <?php echo $success ?>
        </div>
    <?php endif; ?>

    <?php if($error = $this->session->flashdata("error")) : ?>
        <div class="alert alert-danger fade in" style="margin: 10px auto -5px;width: 50%;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <strong>Oops!</strong> <?php echo $error ?>
        </div>
    <?php endif; ?>

    <div class="panel-body">
        <form role="form" class="form-horizontal form-bordered" action="<?php echo $form_action ?>" method="post">
            <input type="hidden" name="user_id" value="<?php echo $flashdata['user_id'] ?>">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="school_name">School Name <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-university"></i></span>
                        <input required="required" type="text" id="school_name" name="school_name" class="form-control" placeholder="School Name" value="<?php echo (isset($flashdata['school_name']) ? $flashdata['school_name'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="email">Email Address <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <input required="required" type="email" id="email" name="email" class="form-control" placeholder="Email Address" value="<?php echo (isset($flashdata['email']) ? $flashdata['email'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="username">Username <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></i></span>
                        <input required="required" type="text" id="username" name="username" class="form-control" placeholder="Username" value="<?php echo (isset($flashdata['username']) ? $flashdata['username'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="telephone"><?php echo LocalizedString("Telephone") ?> (+84) <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                        <input required="required" type="text" id="telephone" name="telephone" class="form-control" placeholder="Telephone" value="<?php echo (isset($flashdata['telephone']) ? $flashdata['telephone'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="address">Address <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-home"></i></span>
                        <input required="required" type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php echo (isset($flashdata['address']) ? $flashdata['address'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <?php echo $location; ?>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="establishment_year">Year of Establishment <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-home"></i></span>
                        <input required="required" type="number" id="establishment_year" name="establishment_year" class="form-control" placeholder="Year of Establishment" value="<?php echo (isset($flashdata['establishment_year']) ? $flashdata['establishment_year'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="web"><?php echo LocalizedString("Web") ?></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-globe"></i></span>
                        <input type="text" id="web" name="web" class="form-control" placeholder="http://www.yourwebsite.com" value="<?php echo (isset($flashdata['web']) ? $flashdata['web'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="facebook"><?php echo LocalizedString("Facebook") ?></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-facebook"></i></span>
                        <input type="text" id="facebook" name="facebook" class="form-control" placeholder="http://www.facebook.com/yourfanpage" value="<?php echo (isset($flashdata['facebook']) ? $flashdata['facebook'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <?php if(isset($edit)) : ?>

                <input type="hidden" name="school_id" value="<?php echo $flashdata['school_id'] ?>">

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="current_pass">Current Password<br>
                        <small><i style="color: red"><?php echo ($error = $this->session->flashdata('error_pass')) ? $error : "" ?></i></small>
                    </label>
                    <div class="col-sm-5">
                        <div class="input-group input-group-in">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" id="current_pass" name="current_pass" class="form-control" placeholder="Current Password">
                        </div><!-- /input-group-in -->
                    </div><!--/cols-->
                </div><!--/form-group-->
            <?php endif; ?>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="password">Password <?php echo (!isset($edit)) ? '<span class="required_field">*</span>' : '' ?><br><small><i style="color:red;"><?php echo ($error = $this->session->flashdata("error_pass_empty")) ? $error : "" ?></i></small></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input <?php echo (isset($edit)) ? "" : 'required="required"' ?> type="password" id="password" name="password" class="form-control" placeholder="Password">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="username">Confirm Password <?php echo (!isset($edit)) ? '<span class="required_field">*</span>' : '' ?><br><small><i style="color:red;"><?php echo ($error = $this->session->flashdata("error_confirm")) ? $error : "" ?></i></small></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input <?php echo (isset($edit)) ? "" : 'required="required"' ?> type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <?php
            echo $all;
            echo $appearance;
            ?>

            <div class="col-sm-3"></div>
            <div class="form-group col-sm-5" style="position: relative;left: 15px">
                <button type="submit" class="btn btn-success">Submit</button>
            </div>

        </form><!--/form-->
    </div><!-- /panel-body -->
</div>
</body>