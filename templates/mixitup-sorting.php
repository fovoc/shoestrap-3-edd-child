<div class="pull-left mix-sort">
	<button class="sort btn btn-default" data-sort="default"><?php _e( 'Default', 'shoestrap_edd' ); ?></button>
	<div class="btn-group">
		<button class="btn btn-default" disabled="disabled"><?php _e( 'Name', 'shoestrap_edd' ); ?></button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li class="sort" data-sort="data-name" data-order="desc"><i class="el-icon-chevron-down"></i> <?php _e( 'Descending', 'shoestrap_edd' ); ?></li>
			<li class="sort" data-sort="data-name" data-order="asc"><i class="el-icon-chevron-up"></i> <?php _e( 'Ascending', 'shoestrap_edd' ); ?></li>
		</ul>
	</div>
	<div class="btn-group">
		<button class="btn btn-default" disabled="disabled"><?php _e( 'Price', 'shoestrap_edd' ); ?></button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li class="sort" data-sort="data-price" data-order="desc"><i class="el-icon-chevron-down"></i> <?php _e( 'Descending', 'shoestrap_edd' ); ?></li>
			<li class="sort" data-sort="data-price" data-order="asc"><i class="el-icon-chevron-up"></i> <?php _e( 'Ascending', 'shoestrap_edd' ); ?></li>
		</ul>
	</div>
</div>