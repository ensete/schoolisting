<body data-spy="scroll" data-target=".navbar">

<div class="landing-wrapper" id="home">

    <?php
    $account_link = "";
    if(!$user) {
        $account_link .= '<a href="'.base_url("parents/register").'">'.LocalizedString("Create An Account").'</a><span class="nav-separate"> | </span><a href="'.base_url("parents/login").'">'.LocalizedString("Login").'</a>';
    } else {
        $account_link .= '<a href="'.base_url("").'">'.LocalizedString("Home").'</a><span class="nav-separate"> | </span><a href="'.base_url("parents/profile/{$user['uuid']}/{$user['username']}").'">'.LocalizedString("Profile").'</a><span class="nav-separate"> | </span><a href="'.base_url("parents/logout").'">'.LocalizedString("Logout").'</a>';
    }
    $head = array(
        'account_link' => $account_link,
        'school_link' => base_url("school"),
        'school_text' => LocalizedString("Schools, click here"),
        'find_link' => base_url(),
        'find_text' => LocalizedString("Find a school"),
        'review_link' => base_url("review/find_your_school"),
        'review_text' => LocalizedString("Review a school"),
        'tip_text' => LocalizedString("Rate a School"),
        'tip_link' => base_url("rate/find_a_school"),
        'button_language' => '<a href="'.base_url("welcome/switch_language").'" role="button" class="btn btn-nofill btn-silc">'.current_language().'</a>',
        'background' => image_url("dummy/landing1.jpg"),
        'search_text' => LocalizedString("Find your best school here and now!"),
        'search_action' => base_url("parents/search_results"),
        'search_placeholder' => LocalizedString("Enter district, city, address or school name"),
        'search_submit' => LocalizedString("Search now"),
    );
    echo $this->lbplusbuilder->get_element('schoolisting/parent-home-head', $head);;

    $about = array(
        'main_text' => LocalizedString("Why Schoolisting"),
        'sub_text' => LocalizedString("Over 1000 parents in Hanoi alone have already trusted us"),
        'icon1' => ' <i class="glyphicon glyphicon-search"></i>',
        'head_text1' => LocalizedString("Easy Search"),
        'sub_text1' => LocalizedString("Log in, and find the best school around you"),
        'icon2' => ' <i class="glyphicon glyphicon-heart"></i>',
        'head_text2' => LocalizedString("Rate - Review"),
        'sub_text2' => LocalizedString("Your voice matters, share it"),
        'icon3' => ' <i class="glyphicon glyphicon-user"></i>',
        'head_text3' => LocalizedString("Parenting Tips"),
        'sub_text3' => LocalizedString("From a panel of experts with combined 100 years of experience"),
        'icon4' => ' <i class="glyphicon glyphicon-fire"></i>',
        'head_text4' => LocalizedString("Hot Deals"),
        'sub_text4' => LocalizedString("Very often they are FREE"),
    );
    echo $this->lbplusbuilder->get_element('schoolisting/parent-home-about', $about);

    $snapshot = array(
        'title' => LocalizedString("Schoolisting Snapshot"),
        'number1' => get_setting("homepage_school"),
        'text1' => LocalizedString("SCHOOLS"),
        'number2' => get_setting("homepage_parent"),
        'text2' => LocalizedString("PARENTS"),
        'number3' => get_setting("homepage_review"),
        'text3' => LocalizedString("REVIEWS"),
        'number4' => get_setting("homepage_hotdeal"),
        'text4' => LocalizedString("HOT DEALS"),
    );
    echo $this->lbplusbuilder->get_element('schoolisting/parent-home-snapshot', $snapshot);

    $case_study = array(
        'background' => "",
        'title' => LocalizedString("What parents say?"),
        'word' => "<i class='fa fa-quote-left quote-left'></i>" . LocalizedString('parent-talk') . '<i class="fa fa-quote-right quote-right"></i>',
        'user' => "- ".LocalizedString("parent-talk-name")." -",
        'button_text' => LocalizedString("Start Searching Now!"),
        // 'foot' => LocalizedString("Show case your school's success")
        'button_link'=>'#'
    );
    echo $this->lbplusbuilder->get_element('schoolisting/parent-home-parentwords', $case_study);
    ?>

</div>