<?php
if($this->session->userdata('school_token') && $this->uri->segment(2) != "school_details") {
    $controller = "school";
    $user = get_school_by_token();
} else {
    $controller = "parents";
    $user = get_user_by_token();
}
?>
<div class="landing-wrapper">
	<div class="navbar-wrapper affix" data-spy="affix" data-offset-top="0">
	    <div class="navbar navbar-default" role="navigation">
	        <div class="container">
	            <div class="navbar-header">
	                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	                    <span class="sr-only">Toggle navigation</span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                </button>
	                <div class="navbar-brand">
	                	<?php if(!$user) : ?>
				            <a href="<?php echo base_url("$controller/register") ?>"><?php echo LocalizedString("Create An Account") ?></a>
				            <span class="nav-separate"> | </span>
				            <a href="<?php echo base_url("$controller/login") ?>"><?php echo LocalizedString("Login") ?></a>
				        <?php else: ?>
				            <a href="<?php echo base_url($controller) ?>"><?php echo LocalizedString("Home") ?></a>
				            <span class="nav-separate"> | </span>
				            <?php if($controller == "school") : ?>
				                <a href="<?php echo base_url("school/profile/{$user['uuid']}/dashboard") . "/" . refine_name($user['school_name']) ?>"> <?php echo LocalizedString("Profile") ?></a>
				            <?php else : ?>
				                <a href="<?php echo base_url("$controller/profile/{$user['uuid']}/{$user['username']}") ?>"><?php echo LocalizedString("Profile") ?></a>
				            <?php endif; ?>
				
				            <span class="nav-separate"> | </span>
				            <a href="<?php echo base_url("$controller/logout") ?>"><?php echo LocalizedString("Logout") ?></a>
				        <?php endif; ?>
	                </div>
	            </div><!-- /.navbar-header -->
	
	            <div class="navbar-collapse collapse">
	            	<ul class="nav navbar-nav navbar-right">
	            		<?php if($controller == "parents") : ?>
                        	<li><a class="header-link" href="<?php echo base_url("school") ?>"><?php echo LocalizedString("Schools, click here") ?></a></li>
                        <?php elseif($controller == "school") : ?>
                        	<li><a class="header-link" href="<?php echo base_url("parents") ?>"><?php echo LocalizedString("Parent looking for a school, click here") ?></a></li>
                        <?php endif; ?>
                    </ul><!-- /.nav -->
                    <?php if($controller == "parents") : ?>
                    	<form class="navbar-form navbar-right">
	                     	<a href="<?php echo base_url() ?>" role="button" class="btn btn-nofill btn-silc"><?php echo LocalizedString("Find a school") ?></a>
	                    </form>
			            <form class="navbar-form navbar-right">
	                     	<a href="<?php echo base_url("review/find_your_school") ?>" role="button" class="btn btn-nofill btn-silc"><?php echo LocalizedString("Review a School") ?></a>
	                    </form>
	                    <form class="navbar-form navbar-right">
	                     	<a href="<?php echo base_url("rate/find_a_school") ?>" role="button" class="btn btn-nofill btn-silc"><?php echo LocalizedString("Rate a School") ?></a>
	                    </form>
			        <?php elseif($controller == "school") : ?>
			        	<form class="navbar-form navbar-right">
	                     	<a target="_blank" href="http://schoolisting.net/topics/schools/" class="btn btn-nofill btn-silc" role="button"><?php echo LocalizedString("School Tips") ?></a>
	                    </form>
			        <?php endif; ?>
			        <form class="navbar-form navbar-right">
                     	<a href="<?php echo base_url("welcome/switch_language") ?>" role="button" class="btn btn-nofill btn-silc"><?php echo current_language() ?></a>
                    </form>
	            </div><!-- /.navbar-collapse -->
	        </div><!-- /.container -->
	    </div><!-- /.navbar -->
	</div>
</div>
<div style="height: 52px;">
</div>
<!-- <div id="sticky_header">
    <div style="display:inline-block">
        
    </div>
    <div class="sticky_right">
        <?php if($controller == "parents") : ?>
            <a href="<?php echo base_url() ?>" role="button" class="btn btn-nofill btn-silc"><?php echo LocalizedString("Find a school") ?></a>
            <a href="<?php echo base_url("review/find_your_school") ?>" role="button" class="btn btn-nofill btn-silc"><?php echo LocalizedString("Review your school") ?></a>
            <a target="_blank" href="<?php echo PARENT_BLOG ?>" role="button" class="btn btn-nofill btn-silc"><?php echo LocalizedString("Parent Tips") ?></a>
            <a href="<?php echo base_url("welcome/switch_language") ?>" role="button" class="btn btn-nofill btn-silc"><?php echo current_language() ?></a>
            
        <?php elseif($controller == "school") : ?>
            <a target="_blank" href="<?php echo SCHOOL_BLOG ?>" class="btn btn-nofill btn-silc" role="button"><?php echo LocalizedString("School Tips") ?></a>
            <a href="<?php echo base_url("welcome/switch_language") ?>" role="button" class="btn btn-nofill btn-silc"><?php echo current_language() ?></a>
            <a class="header-link" href="<?php echo base_url("parents") ?>"><?php echo LocalizedString("Parent looking for a school, click here") ?></a>
        <?php endif; ?>
    </div>
</div> -->