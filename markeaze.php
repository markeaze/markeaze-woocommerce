<?php
/*
Plugin Name: Markeaze
Description:
Version: 1.0.0
Author: Markeaze
Author URI: https://markeaze.com
*/

/*  Copyright 2020  Markeaze

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/

define( 'MARKEAZE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MARKEAZE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once (MARKEAZE_PLUGIN_DIR . 'includes/Markeaze.class.php');

add_action( 'init', array( 'Markeaze', 'init' ) );
load_plugin_textdomain( 'markeaze', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );