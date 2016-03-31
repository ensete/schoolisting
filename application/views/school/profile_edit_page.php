<body style="background: #f6f8fa">
<div class="panel panel-default" style="margin: 50px auto; width: 90%">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo LocalizedString("Edit Profile") ?> - <?php echo $school['school_name'] ?></h3>
    </div><!-- /panel-heading -->

    <div class="panel-body">
        <form role="form" class="form-horizontal form-bordered" action="" method="post">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="school_name"><?php echo LocalizedString("School Name") ?> <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input required="required" type="text" id="school_name" name="school_name" class="form-control" placeholder="School Name" value="<?php echo (isset($school['school_name']) ? $school['school_name'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="email"><?php echo LocalizedString("Email Address") ?> <span class="required_field">*</span><br><small><i style="color:red;"><?php echo ($error = $this->session->flashdata("error_email")) ? $error : "" ?></i></small></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <input required="required" type="email" id="email" name="email" class="form-control" placeholder="Email Address" value="<?php echo (isset($school['email']) ? $school['email'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="telephone"><?php echo LocalizedString("Telephone") ?> (+84) <span class="required_field">*</span><br><small><i style="color:red;"><?php echo ($error = $this->session->flashdata("error_phone")) ? $error : "" ?></i></small></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                        <input required="required" type="text" id="telephone" name="telephone" class="form-control" placeholder="Telephone" value="<?php echo (isset($school['telephone']) ? $school['telephone'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="address"><?php echo LocalizedString("Address") ?> <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-home"></i></span>
                        <input required="required" type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php echo (isset($school['address']) ? $school['address'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <?php echo $location; ?>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="web"><?php echo LocalizedString("Web") ?><br><small><i style="color:red;"><?php echo ($error = $this->session->flashdata("error_web")) ? $error : "" ?></i></small></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-globe"></i></span>
                        <input type="text" id="web" name="web" class="form-control" placeholder="http://www.yourwebsite.com" value="<?php echo (isset($school['web']) ? $school['web'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="facebook"><?php echo LocalizedString("Facebook") ?><br><small><i style="color:red;"><?php echo ($error = $this->session->flashdata("error_fb")) ? $error : "" ?></i></small></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-facebook"></i></span>
                        <input type="text" id="facebook" name="facebook" class="form-control" placeholder="http://www.facebook.com/yourfanpage" value="<?php echo (isset($school['facebook']) ? $school['facebook'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <?php
            echo $all;
            echo $appearance;
            ?>

            <div class="col-sm-3"></div>
            <div class="form-group col-sm-5" style="position: relative;left: 15px">
                <button type="submit" class="btn btn-success"><?php echo LocalizedString("Save Changes") ?></button>
            </div>

        </form><!--/form-->
    </div><!-- /panel-body -->
</div>
</body>