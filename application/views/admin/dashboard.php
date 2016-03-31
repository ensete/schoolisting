<div id="dashboard-total" class="col-md-6" style="padding: 0 7px 0 0">
    <div class="panel panel-default">
        <div class="panel-heading">
            <ul class="panel-actions">
                <li><a href="#" data-toggle="panel-collapse"><i class="fa fa-angle-down"></i></a></li>
                <li><a href="#" data-toggle="panel-close"><i class="fa fa-times"></i></a></li>
            </ul><!-- /panel-actions -->
            <h3 class="panel-title">Statistics</h3>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
            <p><i class="glyphicon glyphicon-user" style="width:15px"></i> <b class="school-details-title"><?php echo LocalizedString("New Parents") ?>: </b><?php echo $new_parents ?></p>
            <p><i class="fa fa-university" style="width:15px"></i> <b class="school-details-title"><?php echo LocalizedString("New Schools") ?>: </b><?php echo $new_schools ?></p>
            <p><i class="fa fa-pencil-square-o" style="width:15px"></i> <b class="school-details-title"><?php echo LocalizedString("Active Plans") ?>: </b><?php echo $active_plans ?></p>
            <p><i class="glyphicon glyphicon-remove" style="width:15px"></i> <b class="school-details-title"><?php echo LocalizedString("Closed Plans") ?>: </b><?php echo $closed_plans ?></p>
            <p><i class="glyphicon glyphicon-ok" style="width:15px"></i> <b class="school-details-title"><?php echo LocalizedString("Completed Plans") ?>: </b><?php echo $completed_plans ?></p>
            <p><i class="glyphicon glyphicon-tower" style="width:15px"></i> <b class="school-details-title"><?php echo LocalizedString("Schools that have Completed Plans") ?>: </b><?php echo $school_with_completed_plan ?></p>
            <p><i class="glyphicon glyphicon-lock" style="width:15px"></i> <b class="school-details-title"><?php echo LocalizedString("Inactive Schools in 3 Months") ?>: </b><?php echo $inactive_schools ?></p>
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->
</div>

<div id="dashboard-counter" class="col-md-6" style="padding: 0 0 0 7px">
    <div class="panel panel-default">
        <div class="panel-heading">
            <ul class="panel-actions">
                <li><a href="#" data-toggle="panel-collapse"><i class="fa fa-angle-down"></i></a></li>
                <li><a href="#" data-toggle="panel-close"><i class="fa fa-times"></i></a></li>
            </ul><!-- /panel-actions -->
            <h3 class="panel-title">Statistics</h3>
        </div><!-- /.panel-heading -->

        <div class="panel-body" style="text-align: center;">

            <div class="counter-container">
                <?php echo $total_view ?>
            </div>

            <div class="counter-container">
                <?php echo $total_recommendation ?>
            </div>

        </div><!-- /.panel-body -->
    </div><!-- /.panel -->
</div>
<div style="clear: both"></div>

<div id="dashboard-column">
    <div class="panel panel-default">
        <div class="panel-heading">
            <ul class="panel-actions">
                <li><a href="#" data-toggle="panel-collapse"><i class="fa fa-angle-down"></i></a></li>
                <li><a href="#" data-toggle="panel-close"><i class="fa fa-times"></i></a></li>
            </ul><!-- /panel-actions -->
            <h3 class="panel-title">Monthly Data Analysis</h3>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
            <?php echo $column_chart ?>
            <hr>
            <?php echo $improved_school_column ?>
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->
</div>

<div class="col-md-6" style="padding: 0 7px 0 0">
    <div class="panel panel-default">
        <div class="panel-heading">
            <ul class="panel-actions">
                <li><a href="#" data-toggle="panel-collapse"><i class="fa fa-angle-down"></i></a></li>
                <li><a href="#" data-toggle="panel-close"><i class="fa fa-times"></i></a></li>
            </ul><!-- /panel-actions -->
            <h3 class="panel-title">Searching Tool</h3>
        </div><!-- /panel-heading -->

        <div class="panel-body">
            <div class="form-group">
                <label>Location</label><br>
                <?php echo $location ?>
            </div><!-- /form-group -->

            <div class="form-group">
                <label>Target</label><br>
                <div class="nice-radio nice-radio-inline">
                    <input class="radio-o" type="radio" name="user_type" id="niceRadioAlt1" value="parent" checked="checked">
                    <label for="niceRadioAlt1">Parents</label>
                </div><!--/nice-radio-->
                <div class="nice-radio nice-radio-inline">
                    <input class="radio-o" type="radio" name="user_type" id="niceRadioAlt2" value="school">
                    <label for="niceRadioAlt2">Schools</label>
                </div><!--/nice-radio-->
            </div>

            <div class="form-group">
                <button id="start-searching" class="btn btn-default">Submit</button>
            </div><!-- /form-group -->
        </div><!-- /panel-body -->

    </div>
</div>
<div id="matching_results" class="col-md-6" style="padding: 0 0 0 7px">

</div>
<div style="clear: both"></div>
<script>
    $(function() {
        $('#start-searching').on('click', function() {
            var city_id = $('#city').val();
            var district_id = $('#district').val();
            var user_type = $('input[name=user_type]:checked').val();
            var url = "<?php echo base_url("admin/index/search_targets") ?>";
            var data = "city=" + city_id + "&district=" + district_id + "&user_type=" + user_type;
            $.ajax({
                url: url,
                type: "POST",
                context: this,
                data: data,
                beforeSend: function () {
                    $(this).html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
                },
                success: function (result) {
                    $(this).html("Submit");
                    $('#matching_results').html(result);
                }
            });
        });
    });
</script>
<script>
    jQuery(document).ready(function( $ ) {
        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });
    });
</script>
<script>
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-36251023-1']);
    _gaq.push(['_setDomainName', 'jqueryscript.net']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>