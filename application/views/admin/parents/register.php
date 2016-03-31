<body style="background: #f6f8fa">
<div class="panel panel-default" style="margin: 50px auto; width: 90%">
    <div class="panel-heading">
        <h3 class="panel-title">Insert Parent</h3>
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
        <form role="form" class="form-horizontal form-bordered" action="<?php echo base_url("admin/parents/handle_register") ?>" method="post">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="full_name">Full Name <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input required="required" type="text" id="full_name" name="full_name" class="form-control" placeholder="Full Name" value="<?php echo (isset($flashdata) ? $flashdata['full_name'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="username">Username <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input required="required" type="text" id="username" name="username" class="form-control" placeholder="Username" value="<?php echo (isset($flashdata) ? $flashdata['username'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="email">Email Address <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <input required="required" type="email" id="email" name="email" class="form-control" placeholder="Email Address" value="<?php echo (isset($flashdata) ? $flashdata['email'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="children_numbers">Children in School Age</label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-users"></i></span>
                        <select class="form-control" id="children_numbers" name="children_numbers">
                            <option value="">- Select One -</option>
                            <?php if(isset($flashdata)) : ?>
                                <?php for($i=1;$i<6;$i++) : ?>
                                    <option <?php echo ($i == $flashdata['children_numbers']) ? "selected" : "" ?> ><?php echo $i ?></option>
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
                <label class="col-sm-3 control-label" for="telephone">Telephone Number(+84)</label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                        <input type="number" id="telephone" name="telephone" class="form-control" placeholder="Telephone Number" value="<?php echo (isset($flashdata) ? $flashdata['telephone'] : "")?>">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <?php echo $location ?>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="password">Password <span class="required_field">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input required="required" type="password" id="password" name="password" class="form-control" placeholder="Password">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="username">Confirm Password <span class="required_field">*</span><br><small><i style="color:red;"><?php echo ($error = $this->session->flashdata("error_confirm")) ? $error : "" ?></i></small></label>
                <div class="col-sm-5">
                    <div class="input-group input-group-in">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input required="required" type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                    </div><!-- /input-group-in -->
                </div><!--/cols-->
            </div><!--/form-group-->

            <?php echo $appearance ?>

            <div class="col-sm-3"></div>
            <div class="form-group col-sm-5" style="position: relative;left: 15px">
                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>

        </form><!--/form-->
    </div><!-- /panel-body -->
</div>
</body>