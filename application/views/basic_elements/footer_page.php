<?php
$controller = $this->router->class;
?>
<footer class="footer" id="footer">
    <div class="container" style="text-align:center">
        <div id="footer_infomation">
            <div class="col-info about col-md-6">
                <h3><?php echo LocalizedString("About Schoolisting") ?></h3>
                <p><?php echo LocalizedString("We promote accountability and transparency of schools across Vietnam") ?></p>
                <?php if((!$this->session->userdata("user_token") && $controller == "parents") || (!$this->session->userdata("school_token") && $controller == "school")) : ?>
                <form action="<?php echo base_url("welcome/add_contact/$controller") ?>" method="post">
                    <div class="input-group footer-input">
                        <span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
                        <input class="form-control" id="leftaddon" name="email" placeholder="<?php echo LocalizedString("Enter your Email") ?>" type="email" required="">
                    </div>
                    <input type="submit" value="<?php echo LocalizedString("Sign up") ?>" class="btn btn-primary btn-lg footer-btn" role="button">
                </form>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
            	<div class="col-info company col-md-4 col-sm-4">
	                <h4><?php echo LocalizedString("Company") ?></h4>
	                <p><a href="#"><?php echo LocalizedString("Our Mission") ?></a></p>
	                <p><a href="#"><?php echo LocalizedString("Our People") ?></a></p>
	                <p><a href="#"><?php echo LocalizedString("Join Our Team") ?></a></p>
	                <p><a href="#"><?php echo LocalizedString("Contact Us") ?></a></p>
	            </div>
	            <div class="col-info supporters col-md-4 col-sm-4">
	                <h4><?php echo  LocalizedString("Supporters") ?></h4>
	                <p><a href="#"><?php echo LocalizedString("Donors") ?></a></p>
	                <p><a href="#"><?php echo LocalizedString("Partners") ?></a></p>
	                <p><a href="#"><?php echo LocalizedString("Media") ?></a></p>
	            </div>
	            <div class="col-info policies col-md-4 col-sm-4">
	                <h4><?php echo LocalizedString("Policies") ?></h4>
	                <p><a href="#"><?php echo LocalizedString("How schools are rated") ?></a></p>
	                <p><a href="#"><?php echo LocalizedString("School review guidelines") ?></a></p>
	                <p><a href="#"><?php echo LocalizedString("Term of use") ?></a></p>
	                <p><a href="#"><?php echo LocalizedString("Privacy Policy") ?></a></p>
	            </div>
            </div>
        </div>
        <div style="display:inline-block;width: 400px;margin: 15px auto 20px"><?php echo LocalizedString("Project awarded by VACI 2014, programme co-organised by the Government Inspectorate and the World Bank") ?></div>
    </div><!-- /.container -->
</footer><!-- /.footer -->