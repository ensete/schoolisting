<?php 
	$ci=& get_instance();
	$error = $ci->session->flashdata("error");
	if (!empty($error)): ?>
	<div class="alert alert-danger fade in">
    	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><?php echo LocalizedString($error) ?></h4>
	</div>
<?php endif; ?>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default" data-color="nephem">
            <div class="panel-heading">
                <ul class="panel-actions panel-actions-alt">
                    <li><a href="#" data-toggle="panel-collapse"><i class="fa fa-angle-down"></i></a></li>
                </ul><!-- /.panel-actions -->
                <h3 class="panel-title"><?php echo LocalizedString("Files and Folders") ?></h3>
            </div><!-- /.panel-heading -->

            <div class="panel-body">
                <div id="jstree1">
                	<?php if (count($file_folder) > 0): ?>
	                    <ul>
	                    	<?php foreach ($file_folder as $folder): ?>
		                        <li data-jstree='{"opened":true, "type":"folder"}'>
		                        	<a href="#" data-id="<?php echo $folder["folder_id"] ?>" data-type="folder"><?php echo $folder["folder_name"] ?></a>
		                        	<?php if (count($folder["files"]) > 0): ?>
			                            <ul>
			                            	<?php foreach ($folder["files"] as $file): ?>
			                                	<li data-jstree='{"type":"file"}'><a href="/school/get_file/<?php echo $file["file_id"] ?>" data-id="<?php echo $file["file_id"] ?>" data-type="file"><?php echo $file["file_name"] ?></a></li>
			                            	<?php endforeach; ?>
			                            </ul>
			                        <?php endif; ?>
		                        </li>
		                	<?php endforeach; ?>
	                    </ul>
	            	<?php endif; ?>
                </div><!-- /#jstree1 -->
            </div><!-- /.panel-body -->
        </div><!-- /.panel -->
    </div><!-- /.cols -->
    <div class="col-md-6">
        <div class="panel panel-default" data-color="nephem">
            <div class="panel-heading">
                <ul class="panel-actions panel-actions-alt">
                    <li><a href="#" data-toggle="panel-collapse"><i class="fa fa-angle-down"></i></a></li>
                </ul><!-- /.panel-actions -->
                <h3 class="panel-title"><?php echo LocalizedString("Folders") ?></h3>
            </div><!-- /.panel-heading -->

            <div class="panel-body">
                <form class="form-bordered" action="/school/add_category" method="post">
                    <div class="form-group">
                        <label class="control-label" for="name"><?php echo LocalizedString("Folder Name") ?></label>
                        <div>
                            <label class="select select-o">
                                <select id="category" name="name">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category["name"] ?>"><?php echo $category["name"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </div>
                    </div><!-- /.form-group -->

                    <div class="form-group clearfix">
                        <div class="pull-right">
                            <button type="reset" class="btn btn-default"><?php echo LocalizedString("Reset") ?></button>
                            <button type="submit" class="btn btn-primary"><?php echo LocalizedString("Add") ?></button>
                        </div>
                    </div><!-- /.form-group -->
                </form><!-- /form -->

            </div><!-- /.panel-body -->
        </div><!-- /.panel -->
        <div class="panel panel-default" data-color="nephem">
            <div class="panel-heading">
                <ul class="panel-actions panel-actions-alt">
                    <li><a href="#" data-toggle="panel-collapse"><i class="fa fa-angle-down"></i></a></li>
                </ul><!-- /.panel-actions -->
                <h3 class="panel-title"><?php echo LocalizedString("Files") ?></h3>
            </div><!-- /.panel-heading -->

            <div class="panel-body">
                <form class="form-bordered" action="/school/add_file" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="control-label" for="folder"><?php echo LocalizedString("Folder") ?></label>
                        <label class="select select-o">
                            <select id="folder" name="folder" required="">
                                <option value="">- <?php echo LocalizedString("Select One") ?> -</option>
                                <?php foreach ($file_folder as $value): ?>
                                	<option value="<?php echo $value['folder_id'] ?>"><?php echo $value['folder_name'] ?></option>
                               	<?php endforeach; ?>
                            </select>
                        </label>
                    </div><!-- /.form-group -->
                    <div class="form-group">
                        <label class="control-label" for="files"><?php echo LocalizedString("File") ?></label>
                        <input id="files" class="form-control" name="files" type="file">
                    </div><!-- /.form-group -->

                    <div class="form-group clearfix">
                        <div class="pull-right">
                            <button type="reset" class="btn btn-default"><?php echo LocalizedString("Reset") ?></button>
                            <button type="submit" class="btn btn-primary"><?php echo LocalizedString("Add") ?></button>
                        </div>
                    </div><!-- /.form-group -->
                </form><!-- /form -->

            </div><!-- /.panel-body -->
        </div><!-- /.panel -->
    </div><!-- /.cols -->
</div>
<div style="clear: both"></div>