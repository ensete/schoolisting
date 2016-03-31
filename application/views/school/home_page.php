<body data-spy="scroll" data-target=".navbar">

<div class="landing-wrapper" id="home">

    <?php
    $account_link = "";
    $register_button = "";
    if(!$school) {
        $register_button .= '<a href="'.base_url('school/login').'" class="btn btn-primary btn-lg" role="button">'.LocalizedString("Create an account2").'</a>';
        $account_link .= '<a href="'.base_url("school/register").'">'.LocalizedString("Create An Account").'</a><span class="nav-separate"> | </span><a href="'.base_url("school/login").'">'.LocalizedString("Login").'</a>';
    } else {
        $account_link .= '<a href="'.base_url("school").'">'.LocalizedString("Home").'</a><span class="nav-separate"> | </span><a href="'.base_url("school/profile/{$school['uuid']}/dashboard") . "/" . refine_name($school['school_name']).'">'.LocalizedString("Profile").'</a><span class="nav-separate"> | </span><a href="'.base_url("school/logout").'">'.LocalizedString("Logout").'</a>';
    }
    $head = array(
        'account_link' => $account_link,
        'school_link' => base_url(),
        'school_text' => LocalizedString("Parent looking for a school, click here"),
        'button_language' => '<a href="'.base_url("welcome/switch_language").'" role="button" class="btn btn-nofill btn-silc">'.current_language().'</a>',
        'background' => image_url("dummy/landing1.jpg"),
        'header_text' => LocalizedString("How good is your school?"),
        'tip_text' => LocalizedString("School Tips"),
        'tip_link' => SCHOOL_BLOG,
        'link_button1' => base_url("questionnaire/questionnaire_for_school"),
        'text_button1' => LocalizedString("CHECK IT NOW"),
        'register_button' => $register_button
    );
    echo $this->lbplusbuilder->get_element('schoolisting/school-home-head', $head);

    $about = array(
        'main_text' => LocalizedString("Why Schoolisting"),
        'sub_text' => LocalizedString("Many schools have joined already. Make yours count!"),
        'link1' => '#',
        'icon1' => ' <i class="glyphicon glyphicon-ok"></i>',
        'head_text1' => LocalizedString("Track Quality"),
        'sub_text1' => LocalizedString("Check your quality against best practice, and track it"),
        'link2' => '#',
        'icon2' => ' <i class="glyphicon glyphicon-star"></i>',
        'head_text2' => LocalizedString("Become Better"),
        'sub_text2' => LocalizedString("Know where to put your effort and resources"),
        'link3' => '#',
        'icon3' => ' <i class="glyphicon glyphicon-comment"></i>',
        'head_text3' => LocalizedString("Get Rave Reviews"),
        'sub_text3' => LocalizedString("Get parents excited with your school’s improvements"),
        'link4' => '#',
        'icon4' => ' <i class="glyphicon glyphicon-flag"></i>',
        'head_text4' => LocalizedString("Stand Out"),
        'sub_text4' => LocalizedString("Stay on top of the list, thrive through competition"),
    );
    echo $this->lbplusbuilder->get_element('schoolisting/parent-home-about', $about);

    $offer = array(
        'text' => LocalizedString("Take a pre evaluation and get your annual consultancy package, Special offer 50% discount [6 months FREE]!"),
        'button_link' => base_url("questionnaire/questionnaire_for_school"),
        'button_text' => LocalizedString("START NOW!")
    );
    echo $this->lbplusbuilder->get_element('schoolisting/school-home-offer', $offer);

    $case_study = array(
        'background' => "",
        'main_text' => LocalizedString("Case Studies"),
        'word' => "<i class='fa fa-quote-left quote-left'></i>" . LocalizedString('school-talk') . '<i class="fa fa-quote-right quote-right"></i>',
        'user' => "- ".LocalizedString("school-talk-name")." -",
        'link1' => base_url("school/register"),
        'text1' => LocalizedString("START CHECKING NOW!"),
        'link2' => base_url("questionnaire/questionnaire_for_school"),
        'text2' => LocalizedString("SHOWCASE YOUR SUCCESS!")
    );
    echo $this->lbplusbuilder->get_element('schoolisting/school-home-casestudy', $case_study);
    ?>

</div>