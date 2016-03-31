<div class="content">
	<!-- WIZARD
    ================================================== -->
    <div class="panel panel-default">
        <div class="panel-body">
        	<p class="lead">
        		This is the most basic example of Jcrop implementation. Since no event handlers are attached, it's not very exciting, but displays the out of box functionality.
        		This is the most basic example of Jcrop implementation. Since no event handlers are attached, it's not very exciting, but displays the out of box functionality.
        	</p>
            <div class="wizard" id="rootwizard">
                <ul>
                    <li>
                        <a href="#wizard1" data-toggle="tab">
                            <span class="number">1</span>
                            <span class="desc">Basic Information</span>
                        </a>
                    </li>
                    <li>
                        <a href="#wizard2" data-toggle="tab">
                            <span class="number">2</span>
                            <span class="desc">Tasks Setup</span>
                        </a>
                    </li>
                </ul><!-- /wizard-nav -->

                <div class="progress progress-striped">
                    <div class="progress-bar"></div>
                </div><!-- /.progressbar -->

                <form action="/planaction/handle_add" role="form" class="form-horizontal" method="post" onsubmit="return validate_create_planaction()">
	                <div class="tab-content">
	                    <div class="tab-pane" id="wizard1">
	                        <h3 class="lead">Provide your plan details</h3>
	                        <?php if (isset($error) && strlen($error) > 0): ?>
		                        <div class="alert alert-danger fade in">
		                        	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		                            <h4><?php echo $error ?></h4>
	                        	</div>
	                        <?php endif; ?>	

	                        
	                        <div class="form-group">
                                <label class="col-sm-3 control-label" for="category">Select category</label>
                                <div class="col-sm-5">
	                                <label class="select select-o">
	                                    <select id="category" name="category" onchange="get_element_html(this)">
	                                    	<?php foreach ($categories as $category): ?>
		                                        <option value="<?php echo $category["id"] ?>"><?php echo $category["name"] ?></option>
		                                	<?php endforeach; ?>
	                                    </select>
	                                </label>
	                        	</div>
                            </div><!-- /.form-group -->

	
	                        <div class="form-group">
	                            <label class="col-sm-3 control-label">Date Range</label>
	                            <div class="col-sm-5">
	                                <div class="input-group input-group-in">
	                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	                                    <input data-input="daterangepicker" class="form-control" name="date_range">
	                                </div><!-- /input-group-in -->
	                            </div><!--/cols-->
	                        </div><!--/form-group-->
	
	                    </div><!-- /.tab-pane -->
	
	                    <div class="tab-pane" id="wizard2">
	                        <h3 class="lead">Select tasks for your plan</h3>
	                        <div class="row">
	                        	<div class="col-md-6 col-md-offset-3" id="task_selection">
									<div class="panel panel-default">
									    
									</div><!-- /.panel -->
								</div>
	                        </div>
	                    </div><!-- /.tab-pane -->
	
	                    <div class="wizard-actions">
	                        <div class="form-group">
	                            <div class="col-md-5 col-md-offset-3">
	                                <button type="button" class="btn btn-silc wizard-prev"><i class="fa fa-arrow-circle-o-left"></i> Back</button>
	                                <button type="button" class="btn btn-primary wizard-next">Continue <i class="fa fa-arrow-circle-o-right"></i></button>
	                                <button type="submit" class="btn btn-primary finish">Submit</button>
	                            </div><!-- /.cols -->
	                        </div><!-- /form-group -->
	                    </div><!-- /.wizard-actions -->
	
	                </div><!-- /.tab-content -->
                </form><!-- /form -->
            </div><!-- /#rootwizard -->
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->
</div>