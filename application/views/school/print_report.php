<div id="school-count-group" class="col-md-3">
    <div class="panel panel-default">
        <div class="panel-heading" style="height: 51px">
            <h3 class="panel-title" style="line-height: 30px;">Information</h3>
        </div><!-- /.panel-heading -->

        <div class="panel-body">
            <p>
                <i class="glyphicon glyphicon-thumbs-up"></i>
                <b class="school-details-title"> Likes: <?php echo $details['like_count'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-bookmark"></i>
                <b class="school-details-title"> Bookmarks: <?php echo $details['bookmark_count'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-share-alt"></i>
                <b class="school-details-title"> Recommendations: <?php echo $details['recommend_count'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-star-empty"></i>
                <b class="school-details-title"> Rates: <?php echo $details['rate_count'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-star"></i>
                <b class="school-details-title"> Parent Rating: <?php echo ($parent_rating) ? $parent_rating : 0 ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-star"></i>
                <b class="school-details-title"> School Rating: <?php echo ($school_rating) ? $school_rating : 0 ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-eye-open"></i>
                <b class="school-details-title"> Views: <?php echo $details['total_views'] ?></b>
            </p>
            <p>
                <i class="glyphicon glyphicon-ok"></i>
                <b class="school-details-title"> Completed Plans: <?php echo $completed_plans ?></b>
            </p>
            <p>
                <i class="fa fa-pencil-square-o"></i>
                <b class="school-details-title"> Active Plans: <?php echo $active_plans ?></b>
            </p>
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->
</div>
<div style="clear: both"></div>
<section id="data-chart" style="border-top: 1px solid #E7EDF2;background: #F6F8FA;padding: 20px 50px 30px;text-align: center">
    <h2>School Graph - Parent Rating</h2>
    <h3 style="font-size:20px;">Average Marks of each Category</h3>
    <div style="margin:20px 0 30px">
        <?php echo $pie_parent ?>
    </div>

    <?php
    $parent_line_chart = array(
        "id" => "parent-line-chart",
        "title" => LocalizedString("Total Mark"),
        "subtitle" => LocalizedString("Latest 12 Months"),
        "categories" => $line_chart['time'],
        "unit" => LocalizedString("Marks"),
        "data" => $line_chart['parent_data']
    );
    echo $this->lbplusbuilder->get_element("schoolisting/highchart-line", $parent_line_chart);
    ?>
    <script>
        $(function() {
            $('rect').attr('fill', '#F6F8FA');
        })
    </script>
</section>

<section id="data-chart" style="border-top: 1px solid #E7EDF2;padding: 20px 50px 30px;text-align: center">
    <h2>School Graph - School Self-Rating</h2>
    <h3 style="font-size:20px;">Average Marks of each Category</h3>
    <div style="margin:20px 0 30px">
        <?php echo $pie_school ?>
    </div>

    <?php
    $school_line_chart = array(
        "id" => "school-line-chart",
        "title" => LocalizedString("Total Mark"),
        "subtitle" => LocalizedString("Latest 12 Months"),
        "categories" => $line_chart['time'],
        "unit" => LocalizedString("Marks"),
        "data" => $line_chart['school_data']
    );
    echo $this->lbplusbuilder->get_element("schoolisting/highchart-line", $school_line_chart);
    ?>

</section>
<script>
    $(function() {
        setTimeout(function(){ window.print(); }, 1000);
    })
</script>