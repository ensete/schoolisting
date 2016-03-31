<li><a href="/admin/index">Dashboard</a></li>
<?php 
	foreach ($breadcrumb as $key => $item) 
	{
?>
        <li class="active"><?php echo $item ?></li>
<?php		
	} 
?>