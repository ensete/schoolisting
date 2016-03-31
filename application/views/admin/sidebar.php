<!-- 
	generate sidebar items
	3 item types
	- clickable
	- dropdown
	- panel only
-->
<ul class="nav">
	<?php foreach($sidebar as $item): ?>
		<?php if (isset($item['is_panel_only'])): ?>
			<li class="nav-header">
		        <!-- <a class="nav-header-icon" href="#"><i class="fa fa-wrench"></i></a> -->
		        <span class="nav-header-text"><?php echo $item['title']; ?></span>
		    </li><!--/.nav-header-->
		<?php elseif (isset($item['items'])): ?>    
		    <li class="nav-item">
		        <a href="#" data-toggle="nav-item-child">
		            <span class="caret"></span>
		            <i class="nav-item-icon <?php echo $item['icon'] ?>"></i> 
		            <span class="nav-item-text"><?php echo $item['title']; ?></span>
		        </a>
		        <ul class="nav-item-child">
		        	<?php foreach ($item['items'] as $child): ?>
		            	<li>
		            		<a href="<?php echo $child['link'] ?>">
		            			<span class="nav-item-text"><?php echo $child['title'] ?></span>
		            		</a>
		            	</li>
		            <?php endforeach; ?>
		        </ul>
		    </li><!--/.nav-item-->
		<?php else: ?>    
		    <li class="nav-item <?php if ($item_active == $item['mark']): ?>active<?php endif; ?>">
		        <a href="<?php echo $item['link']; ?>">
		            <i class="nav-item-icon <?php echo $item['icon'] ?>"></i>
		            <span class="nav-item-text"><?php echo $item['title']; ?></span>
		        </a>
		    </li><!--/.nav-item-->
		<?php endif; ?>
	<?php endforeach; ?>
</ul><!--/.nav-->
