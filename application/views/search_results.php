<div style="background: #F6F8FA">
    <div class="panel panel-minimalize col-md-4" style="margin:20px 0px 0 0px;padding-bottom: 20px">
        <div class="panel-body">
            <div class="tab-content sr-wrapper">
                <form action="<?php echo base_url("parents/search_submit") ?>" method="get">

                    <div class="form-group has-feedback">
                        <div class="input-group input-group-in">
                            <span class="input-group-addon"><i class="fa fa-font"></i></span>
                            <input name="school_name" id="school_name" class="form-control" placeholder="<?php echo LocalizedString("School Name") ?>"
                                   value="<?php echo @$school_name ?>">
                            <span class="form-control-feedback"></span>
                        </div>
                    </div> <!-- /.form-group -->

                    <div class="form-group has-feedback">
                        <div class="input-group input-group-in">
                            <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                            <input name="establishment_year" id="establishment_year" class="form-control" placeholder="<?php echo LocalizedString("Year of Estalishment") ?>"
                                   type="number" value="<?php echo @$establishment_year ?>">
                            <span class="form-control-feedback"></span>
                        </div>
                    </div> <!-- /.form-group -->

                    <?php echo $location ?>

                    <div class="form-group has-feedback">
                        <div class="input-group input-group-in">
                            <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
                            <select class="form-control" id="rating_sort" name="rating_sort">
                                <option value="">- <?php echo LocalizedString("Sort By Rating") ?> -</option>
                                <option <?php echo ($this->input->get('rating_sort') == 1) ? "selected" : "" ?> value="1"><?php echo LocalizedString("Parent Rating from highest") ?></option>
                                <option <?php echo ($this->input->get('rating_sort') == 2) ? "selected" : "" ?> value="2"><?php echo LocalizedString("Parent Rating from lowest") ?></option>
                                <option <?php echo ($this->input->get('rating_sort') == 3) ? "selected" : "" ?> value="3"><?php echo LocalizedString("School Rating from highest") ?></option>
                                <option <?php echo ($this->input->get('rating_sort') == 4) ? "selected" : "" ?> value="4"><?php echo LocalizedString("School Rating from lowest") ?></option>
                            </select>
                        </div><!-- /input-group-in -->
                    </div>

                    <?php echo $all ?>

                    <div class="form-group animated-hue clearfix" style="margin-bottom: 0">
                        <div class="text-center">
                            <!--<a href="<?php /*echo base_url("school/search_results") */?>" class="btn btn-primary" role="button">Reset</a>-->
                            <button type="submit" class="btn btn-primary"><?php echo LocalizedString("Begin Search") ?> <i
                                    class="fa fa-chevron-circle-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="tab-pane col-md-8" id="posts" style="margin-top:20px;padding-bottom: 20px;">
        <div class="posts-wrapper" id="mediaObject">
            <div id="loading_image" class="text-center text-muted">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
            </div>
        </div><!-- /.posts-wrapper -->
    </div><!-- /.tab-pane #posts -->
    <div style="clear: both"></div>
</div>

<?php echo $this->lbplusbuilder->get_element('schoolisting/refer-modal', array('placeholder'=>LocalizedString("Enter your friend's email"),'submit'=>LocalizedString("Submit"),'close'=>LocalizedString("Close"),'title' => LocalizedString("Refer this school to your friend"))); ?>

<script>
    $(document).ready(function() {
        var no_more = "<?php echo LocalizedString("End of Results") ?>";
        var track_load = 0;
        var loading  = false;
        var loading_url = "<?php echo base_url("parents/autoload") ?>";
        var total_groups = <?php echo $total_groups; ?>;
        var container = $('#mediaObject');
        var key = "<?php echo $key ?>";
        var loading_image = '<div id="loading_image" class="text-center text-muted"><i class="fa fa-spinner fa-spin fa-2x"></i></div>';

        container.load(loading_url, {'group_no':track_load,'key':key,'get_data':<?php echo json_encode($get_data) ?>}, function() {track_load++;});
        $(window).scroll(function() {
            $('.autoload-effect').addClass('autoload_hidden').viewportChecker({
                classToAdd: 'visible animated fadeIn',
                offset: 100
            });
            if($(window).scrollTop() + $(window).height() > $(document).height() - 400)
            {
                if(track_load <= total_groups && loading==false)
                {
                    container.append(loading_image);
                    loading = true;
                    $.post(loading_url,{'group_no': track_load,'key':key,'get_data':<?php echo json_encode($get_data) ?>}, function(data){
                        if(data) {
                            container.append(data);
                            track_load++;
                            loading = false;
                        } else {
                            container.append("<p class='text-center'><b>"+no_more+"</b><p>");
                        }
                        container.find('#loading_image').remove();
                    })
                }
            }
        });
    });
</script>