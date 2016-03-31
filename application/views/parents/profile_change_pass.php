<body style="background: #f6f8fa">
<div class="panel panel-default" style="margin: 100px auto; width: 90%">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo LocalizedString("Change Password") ?> - <?php echo $user['full_name'] ?></h3>
    </div><!-- /panel-heading -->

    <div class="panel-body">
        <form role="form" class="form-horizontal form-bordered" action="" method="post">

            <div class="form-group">
                <label class="col-sm-3 control-label" for="current_pass"><?php echo LocalizedString("Current Password") ?><br>
                <small><i style="color: red"><?php echo ($error = $this->session->flashdata('error_pass')) ? $error : "" ?></i></small>
                </label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input required="required" type="password" id="current_pass" name="current_pass" class="form-control" placeholder="Current Password">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="password"><?php echo LocalizedString("New Password") ?><br>
                    <small><i style="color: red"><?php echo ($error = $this->session->flashdata('error_new')) ? $error : "" ?></i></small>
                </label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input required="required" type="password" id="password" name="password" class="form-control" placeholder="New Password">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="confirm_pass"><?php echo LocalizedString("Confirm Password") ?><br>
                    <small><i style="color: red"><?php echo ($error = $this->session->flashdata('error_match')) ? $error : "" ?></i></small>
                </label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input required="required" type="password" id="confirm_pass" name="confirm_pass" class="form-control" placeholder="Confirm Password">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="col-sm-3"></div>
            <div class="form-group col-sm-5" style="position: relative;left: 15px">
                <button type="submit" class="btn btn-success"><?php echo LocalizedString("Save Changes") ?></button>
            </div>

        </form><!--/form-->
    </div><!-- /panel-body -->
</div>
</body>