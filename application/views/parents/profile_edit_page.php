<body style="background: #f6f8fa">
<div class="panel panel-default" style="margin: 50px auto; width: 90%">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo LocalizedString("Edit Profile") . " - " . $user['full_name'] ?></h3>
    </div><!-- /panel-heading -->

    <div class="panel-body">
        <form role="form" class="form-horizontal form-bordered" action="" method="post">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="full_name"><?php echo LocalizedString("Full Name") ?> <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input required="required" type="text" id="full_name" name="full_name" class="form-control" placeholder="Full Name" value="<?php echo (isset($user['full_name']) ? $user['full_name'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="email"><?php echo LocalizedString("Email Address") ?> <span class="required_field">*</span><br><small><i style="color:red;"><?php echo ($error = $this->session->flashdata("error")) ? $error : "" ?></i></small></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <input required="required" type="email" id="email" name="email" class="form-control" placeholder="Email Address" value="<?php echo (isset($user['email']) ? $user['email'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="children_numbers"><?php echo LocalizedString("Number of children in school age") ?></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-users"></i></span>
                        <select class="form-control" id="children_numbers" name="children_numbers">
                            <option value="">- <?php echo LocalizedString("Select One") ?> -</option>
                            <?php if(isset($user['children_numbers'])) : ?>
                                <?php for($i=1;$i<6;$i++) : ?>
                                    <option <?php echo ($i == $user['children_numbers']) ? "selected" : "" ?> ><?php echo $i ?></option>
                                <?php endfor; ?>
                            <?php else: ?>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            <?php endif; ?>
                        </select>
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="telephone"><?php echo LocalizedString("Telephone Number") ?>(+84)</label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                        <input type="number" id="telephone" name="telephone" class="form-control" placeholder="<?php echo LocalizedString("Telephone Number") ?>" value="<?php echo (isset($user['telephone']) ? $user['telephone'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <?php echo $location ?>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="appearance"><?php echo LocalizedString("Status") ?><br><small><i><?php echo LocalizedString("Visible or not to others users") ?></i></small></label>
                <div class="col-sm-5">
                    <input type="checkbox" id="appearance" name="appearance" class="js-switch" data-color="#d14233" data-switchery="true" style="display: none;" value="1" <?php echo (isset($user['appearance']) && $user['appearance'] == 1) ? "checked" : "" ?>>
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