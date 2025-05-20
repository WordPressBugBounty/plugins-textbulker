<?php
/**
 * Plugin Name: TextBulker (IA Redaction)
 * Plugin URI: https://www.textbulker.com
 * Description: Official integration plugin for TextBulker.com – the AI-powered platform for automated content publishing and SEO optimization.
 * Version: 1.0.1
 * Author: Frédéric Luddeni (ASF Collector)
 * Text Domain: textbulker
 * ≈≈
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-textbulker-loader.php';

register_activation_hook(__FILE__, 'textbulker_activate');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'textbulker_add_settings_link');

function textbulker_activate() {
    $defaults = [];

    if (is_plugin_active('wordpress-seo/wp-seo.php')) {
        $defaults['expose_yoast'] = 1;
    }

    if (is_plugin_active('seo-by-rank-math/rank-math.php')) {
        $defaults['expose_rankmath'] = 1;
    }

    $existing = get_option('textbulker_settings', []);
    update_option('textbulker_settings', array_merge($defaults, $existing));
}

function textbulker_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=textbulker">' . __('Settings', 'textbulker') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$loader = new \textbulker\textbulker\TextBulker_Loader();
$loader->run();
