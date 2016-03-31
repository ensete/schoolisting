<!-- 
	admin header
-->
<?php is_admin() ?>
<div class="navbar">
    <div class="container-fluid">
        <div class="navbar-header navbar-block">
            <button type="button" class="navbar-toggle navbar-toggle-alt" data-toggle="collapse" data-target="">
                <span class="fa fa-bars"></span>
            </button>

            <a class="navbar-brand" href="<?php echo base_url() ?>"><strong>Schoolisting</strong></a>

            <ul class="nav navbar-nav pull-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Bent Smith">
                        Schoolisting <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo base_url("admin/oauth/logout") ?>">Sign out</a></li>
                    </ul>
                </li>
            </ul><!-- /navbar-nav -->                            
        </div><!--/.navbar-header-->
    </div><!--/.container-fluid-->
</div><!--/.navbar-->
