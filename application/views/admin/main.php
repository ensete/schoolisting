<!--
	Admin - main element - mapping guide
	|
	|   			$admin_header
	|____________________________________________________
	$admin_sidebar  | $admin_title    $admin_breadcrumb
					|____________________________________
					|			$admin_content:
					|
-->
<body>
	<main id="wrapper" class="wrapkit-wrapper">
	    <header class="header">
	    	<?php echo @$admin_header ?> 
	   	</header><!--/.header-->
		<aside class="sidebar" data-control="wrapkit-sidebar">
	   		<nav class="sidebar-nav">
	   			<?php echo @$admin_sidebar ?>
	       	</nav><!--/.sidebar-nav-->
	    </aside><!--/.sidebar-->
		<section class="content-wrapper">
	        <div class="content-header">
	            <div class="pull-right">
	                <ol class="breadcrumb">
	                    <?php echo @$admin_breadcrumb ?>
	                </ol>
	            </div>
	            <h1 class="content-title"><?php echo @$admin_title; ?></h1>
	        </div><!-- /.content-header -->
			<div class="content">
				<?php echo @$admin_content; ?>
	        </div><!-- /.content -->
	    </section><!--/.content-wrapper-->
	
	    <!-- <footer class="footer-wrapper">
	        <div class="footer">
	            <p>Schoolisting</p>
	        </div>
	    </footer> --><!--/.footer-->
	
	</main><!--/#wrapper-->
