<?php

// Prioritize loading of some necessary core modules
require_once get_template_directory() . '/lib/modules/core.redux/module.php';
require_once get_template_directory() . '/lib/modules/core/module.php';
require_once get_template_directory() . '/lib/modules/core.layout/module.php';
require_once get_template_directory() . '/lib/modules/core.images/module.php';

// Load the EDD admin options
require_once get_stylesheet_directory() . '/lib/modules/edd/module.php';

// Load the EDD-Specific functions
require_once get_stylesheet_directory() . '/lib/edd-functions.php';
require_once get_stylesheet_directory() . '/lib/edd-widgets.php';