<div class="pull-left mix-sort">
	<button class="sort btn btn-default" data-sort="default"><?php _e( 'Default', 'shoestrap_edd' ); ?></button>
	<div class="btn-group">
		<button class="btn btn-default" disabled="disabled"><?php _e( 'Name', 'shoestrap_edd' ); ?></button>
		<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li class="sort" data-sort="data-name" data-order="desc"> <?php _e( 'Descending', 'shoestrap_edd' ); ?> <i class="el-icon-chevron-down"></i></li>
			<li class="sort" data-sort="data-name" data-order="asc"> <?php _e( 'Ascending', 'shoestrap_edd' ); ?> <i class="el-icon-chevron-up"></i></li>
		</ul>
	</div>
	<div class="btn-group">
		<button class="btn btn-default" disabled="disabled"><?php _e( 'Price', 'shoestrap_edd' ); ?></button>
		<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li class="sort" data-sort="data-price" data-order="desc"> <?php _e( 'Descending', 'shoestrap_edd' ); ?> <i class="el-icon-chevron-down"></i></li>
			<li class="sort" data-sort="data-price" data-order="asc"> <?php _e( 'Ascending', 'shoestrap_edd' ); ?> <i class="el-icon-chevron-up"></i></li>
		</ul>
	</div>
</div>