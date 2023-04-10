<?php
/**
 * Text Domain:         tn-import-data
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * License:             GPL-2.0+
 * Author URI:
 * Author:              kma
 * Version:             1.0.0
 * Description:         kma
 * Plugin URI:          kma
 * Plugin Name:         KMA Import Data
 */

define("TN_IMPORT_DATA_VERSION", defined('WP_DEBUG') && WP_DEBUG ? uniqid() : '1.0.0');
define("TN_IMPORT_DATA_NAMESPACE", "KMA-Import-Data");
define("TN_IMPORT_DATA_PATH", plugin_dir_path(__FILE__));
define("TN_IMPORT_DATA_URL", plugin_dir_url(__FILE__));

require_once plugin_dir_path(__FILE__) . "vendor/autoload.php";
include plugin_dir_path(__FILE__) . "assets/simplehtmldom/simple_html_dom.php";

new \ImportData\Customer\CustomerController();
new \ImportData\Product\ProductController();