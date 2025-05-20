<?php

/**
 * Class responsible for the initialization and management of the TextBulker plugin.
 */
namespace textbulker\textbulker;

class TextBulker_Loader
{
    const VERSION = '1.0.1';

    public function run()
    {
        add_action('init', [$this, 'load_textdomain']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('init', [$this, 'maybe_register_meta_fields']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Loads the plugin's text domain for localization.
     *
     * @return void
     */
    public function load_textdomain()
    {
        load_plugin_textdomain('textbulker', false, dirname(plugin_basename(__FILE__), 2) . '/languages');
    }

    /**
     * Registers custom REST API routes for the application.
     *
     * @return void
     */
    public function register_rest_routes()
    {
        register_rest_route('textbulker/v1', '/version', [
            'methods' => 'GET',
            'callback' => function () {
                return new WP_REST_Response(['version' => self::VERSION], 200);
            },
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);

        register_rest_route('textbulker/v1', '/ping', [
            'methods' => 'GET',
            'callback' => function () {
                return new WP_REST_Response([
                    'status' => 'ok',
                    'plugin' => 'TextBulker',
                    'version' => TextBulker_Loader::VERSION,
                ]);
            },
            'permission_callback' => '__return_true', // public
        ]);
    }

    /**
     * Conditionally registers meta fields for SEO plugins to be exposed via the REST API.
     *
     * This method checks plugin activation and settings options to determine whether
     * to register meta fields for Yoast SEO or Rank Math, enabling them to be available
     * through the WordPress REST API.
     *
     * @return void
     */
    public function maybe_register_meta_fields()
    {
        $options = get_option('textbulker_settings');

        if (!empty($options['expose_yoast'])) {
            if (is_plugin_active('wordpress-seo/wp-seo.php')) {
                foreach (['_yoast_wpseo_title', '_yoast_wpseo_metadesc', '_yoast_wpseo_focuskw'] as $field) {
                    register_meta('post', $field, [
                        'type' => 'string',
                        'single' => true,
                        'show_in_rest' => true,
                        'auth_callback' => function () {
                            return current_user_can('edit_posts');
                        }
                    ]);
                }
            }
        }

        if (is_plugin_active('seo-by-rank-math/rank-math.php') && !empty($options['expose_rankmath'])) {
            foreach (['rank_math_title', 'rank_math_description', 'rank_math_focus_keyword'] as $field) {
                register_meta('post', $field, [
                    'type' => 'string',
                    'single' => true,
                    'show_in_rest' => true,
                    'auth_callback' => function () {
                        return current_user_can('edit_posts');
                    }
                ]);
            }
        }
    }

    /**
     * Adds an administration menu option to the WordPress dashboard for the plugin.
     *
     * @return void
     */
    public function add_admin_menu()
    {
        add_options_page(__('TextBulker Settings', 'textbulker'), __('TextBulker', 'textbulker'), 'manage_options', 'textbulker', [$this, 'settings_page']);
    }

    /**
     * Registers settings, sections, and fields for the plugin in the WordPress settings API.
     *
     * @return void
     */
    public function register_settings()
    {
        register_setting(
            'textbulker',
            'textbulker_settings',
            array(
                'sanitize_callback' => array( $this, 'textbulker_sanitize_settings' )
            )
        );
        add_settings_section('textbulker_main', __('Main Settings', 'textbulker'), function () {
        }, 'textbulker');

        add_settings_field('expose_yoast', __('Expose Yoast Metadata', 'textbulker'), function () {
            $options = get_option('textbulker_settings');
            echo '<input type="checkbox" name="textbulker_settings[expose_yoast]" value="1"' . checked(1, $options['expose_yoast'] ?? '', false) . ' />';
        }, 'textbulker', 'textbulker_main');

        add_settings_field('expose_rankmath', __('Expose Rank Math Metadata', 'textbulker'), function () {
            $options = get_option('textbulker_settings');
            echo '<input type="checkbox" name="textbulker_settings[expose_rankmath]" value="1"' . checked(1, $options['expose_rankmath'] ?? '', false) . ' />';
        }, 'textbulker', 'textbulker_main');
    }

    function textbulker_sanitize_settings($input) {
        $output = array();

        // Sanitize "Expose Yoast Metadata"
        if (isset($input['expose_yoast'])) {
            $output['expose_yoast'] = $input['expose_yoast'] == '1' ? 1 : 0;
        } else {
            $output['expose_yoast'] = 0;
        }

        // Sanitize "Expose Rank Math Metadata"
        if (isset($input['expose_rankmath'])) {
            $output['expose_rankmath'] = $input['expose_rankmath'] == '1' ? 1 : 0;
        } else {
            $output['expose_rankmath'] = 0;
        }

        return $output;
    }

    /**
     * Renders the settings page for the textbulker plugin in the WordPress dashboard.
     *
     * @return void
     */
    public function settings_page()
    {
        $options = get_option('textbulker_settings');
        echo '<div class="wrap"><h1>' . esc_html__('TextBulker Settings', 'textbulker') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('textbulker');
        do_settings_sections('textbulker');
        submit_button();
        echo '</form>';
        echo '<hr><h2>' . esc_html__('Diagnostics', 'textbulker') . '</h2><ul>';
        echo '<li>' . (is_plugin_active('wordpress-seo/wp-seo.php') ? '🟢 Yoast SEO active' : '🔴 Yoast SEO inactive or missing') . '</li>';
        echo '<li>' . (is_plugin_active('seo-by-rank-math/rank-math.php') ? '🟢 Rank Math active' : '🔴 Rank Math inactive or missing') . '</li>';
        echo '<li>' . (!empty($options['expose_yoast']) ? '✅ Yoast exposure enabled' : '❌ Yoast exposure disabled') . '</li>';
        echo '<li>' . (!empty($options['expose_rankmath']) ? '✅ Rank Math exposure enabled' : '❌ Rank Math exposure disabled') . '</li>';
        echo '</ul></div>';
    }
}