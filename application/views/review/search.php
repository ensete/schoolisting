<body data-spy="scroll" data-target=".navbar">
<div class="landing-wrapper review-find-school" id="home" style="height: 730px;overflow: hidden">
    <?php
        $account_link = "";
        if(!$user) {
            $account_link .= '<a href="'.base_url("parents/register").'">'.LocalizedString("Create An Account").'</a><span class="nav-separate"> | </span><a href="'.base_url("parents/login").'">'.LocalizedString("Login").'</a>';
        } else {
            $account_link .= '<a href="'.base_url("").'">'.LocalizedString("Home").'</a><span class="nav-separate"> | </span><a href="'.base_url("parents/profile/{$user['uuid']}/{$user['username']}").'">'.LocalizedString("Profile").'</a><span class="nav-separate"> | </span><a href="'.base_url("parents/logout").'">'.LocalizedString("Logout").'</a>';
        }
        if($lang = $this->session->userdata('site_language')) {
            $language_text = "English";
        } else {
            $language_text = ($lang != "vi") ? "Tiếng Việt" : "English";
        }
        $head = array(
            'account_link' => $account_link,
            'school_link' => base_url("school"),
            'school_text' => LocalizedString("Schools, click here"),
            'find_link' => base_url(),
            'find_text' => LocalizedString("Find a school"),
            'review_link' => base_url("review/find_your_school"),
            'review_text' => LocalizedString("Review a School"),
            'tip_text' => LocalizedString("Rate a School"),
            'tip_link' => base_url("rate/find_a_school"),
            'button_language' => '<a href="'.base_url("welcome/switch_language").'" role="button" class="btn btn-nofill btn-silc">'.$language_text.'</a>',
            'background' => image_url("dummy/landing1.jpg"),
            'search_text' => $search_text,
            'search_placeholder' => LocalizedString("Enter your School Name"),
            'school_return' => "",
        );
        echo $this->lbplusbuilder->get_element('schoolisting/review-home-head', $head);
    ?>
</div>
<script>
    $(document).on('keyup', '#search-bar', function() {
        var key = $(this).val();
        if(key != "") {
            $('#suggest-school').show();
            var base_url = window.location.origin + "/";
            var url = base_url + "<?php echo $search_url ?>";
            var ajax_loader = "<img src='"+base_url + "assets/images/ajax-loader/bar.gif'>";
            $.ajax({
                url: url,
                type: "POST",
                context: this,
                data: "key=" + key,
                beforeSend: function () {
                    $('#suggest-school').html(ajax_loader);
                },
                success: function (result) {
                    if(result) {
                        $('#suggest-school').html(result);
                    } else {
                        $('#suggest-school').hide();
                    }
                }
            });
        } else {
            $('#suggest-school').hide();
        }
    });
</script>