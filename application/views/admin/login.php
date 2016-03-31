<body style="background: #F6F8FA">
<div style="position: relative">
    <div class="panel panel-default" style="width: 350px;height: 260px;padding: 20px;position: absolute;top: 50%;left: 50%; margin: 150px 0 0 -175px; ">
        <div class="panel-body">
            <p class="text-center"><strong>SIGN IN</strong></p>
            <p style="color:red"><?php echo $this->session->flashdata('error') ?></p>
            <p class="clearfix"></p>
            <form action="<?php echo base_url("admin/oauth") ?>" method="post">
                <div class="form-group">
                    <div class="input-group input-group-in ui-no-corner no-border bordered-bottom bg-none">
                        <div class="input-group-addon"><i class="fa fa-user text-muted"></i></div>
                        <input class="form-control" placeholder="Username" name="username" value="<?php echo $this->session->flashdata("username") ?>">
                    </div>
                </div><!-- /.form-group -->
                <div class="form-group">
                    <div class="input-group input-group-in ui-no-corner no-border bordered-bottom bg-none">
                        <div class="input-group-addon"><i class="fa fa-lock text-muted"></i></div>
                        <input type="password" class="form-control" placeholder="Password" name="password">
                    </div>
                </div><!-- /.form-group -->
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                        </div><!-- /.cols -->
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-sm btn-block btn-info" style="margin-top:5px">SUBMIT</button>
                        </div><!-- /.cols -->
                    </div><!-- /.row -->
                </div><!-- /.form-group -->
            </form><!-- /form -->
        </div><!-- /.panel-body -->
    </div>
</div>
</body>