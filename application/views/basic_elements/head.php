<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo (isset($head['title'])) ? $head['title'] : "" ?> - Schoolisting</title>
	
	<?php $opg_content = isset($opg_title) ? $opg_title : LocalizedString("Schoolisting", "opg") ?>
    <meta property="og:title" content="<?php echo $opg_content ?>" />
    <meta property="og:type" content="website" />
    <?php $opg_content = isset($opg_image) ? $opg_image : image_url("dummy/people5.jpg") ?>
    <meta property="og:image" content="<?php echo $opg_content ?>" />

    <?php if($this->router->class == "parents") : ?>
        <meta property="og:description" content="Schoolisting is an indispensable tool for parents who are struggling to find a best suited school for their children" />
    <?php else : ?>
        <meta property="og:description" content="Join Schoolisting to get the most benefits for your school" />
    <?php endif; ?>

    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">


    <!-- fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo ico_url("apple-touch-icon-144-precomposed.png") ?>">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo ico_url("apple-touch-icon-114-precomposed.png") ?>">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo ico_url("apple-touch-icon-72-precomposed.png") ?>">
    <link rel="apple-touch-icon-precomposed" href="<?php echo ico_url("apple-touch-icon-57-precomposed.png") ?>">
    <link rel="shortcut icon" href="<?php echo ico_url("favicon.png") ?>">
    <link rel="shortcut icon" href="<?php echo ico_url("favicon.ico") ?>">


    <!-- VENDOR -->
    <link rel="stylesheet" href="<?php echo css_url("jqueryui.min.css") ?>">
    <link rel="stylesheet" href="<?php echo css_url("bootstrap.min.css") ?>">
    <!-- END VENDOR -->

    <!-- WRAPKIT -->
    <link rel="stylesheet" href="<?php echo css_url("wrapkit.min.css") ?>">
    <link rel="stylesheet" href="<?php echo css_url("wrapkit-skins-all.min.css") ?>">
    <!-- END WRAPKIT -->

    <!-- !IMPORTANT DEPENDENCIES -->
    <link rel="stylesheet" href="<?php echo css_url("font-awesome.min.css") ?>">
    <link rel="stylesheet" href="<?php echo css_url("switchery.min.css") ?>">
    <link rel="stylesheet" href="<?php echo css_url("toastr.min.css") ?>">
    <link rel="stylesheet" href="<?php echo css_url("prettify.min.css") ?>">
    <!-- END !IMPORTANT DEPENDENCIES -->
    <!-- Page landing -->
	<link rel="stylesheet" href="<?php echo css_url("pages-landing.min.css") ?>">
    <?php
        if(isset($head['css'])) {
            foreach($head['css'] as $value) {
                echo '<link rel="stylesheet" href="'.css_url($value).'">';
            }
        }
    ?>

    <link rel="stylesheet" href="<?php echo css_url("devs/main.css") ?>">

    <script src="<?php echo js_url("jquery.min.js") ?>"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>