<?php
/**
 * DOT_Debug_WooTheme_Diff
 * A debug script to create theme files diff
 *
 */

// If this file is called directly, call the cops.
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

if ( ! class_exists( 'DOT_Debug_WooTheme_Diff' ) ) {

    class DOT_Debug_WooTheme_Diff {

        /**
         * The single instance of the class.
         *
         * @since 1.0.0
         */
        protected static $instance = null;

        /**
         * Main Instance.
         * You know singleton...
         *
         * @since 1.0.0
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Init
         */
        public function init() {
            add_action( 'admin_menu', [ $this, 'admin_menu' ], 99 );
        }

        /**
         * Action: 'admin_menu'
         *
         * @return void
         */
        public function admin_menu() {
            add_management_page( 'Overrided Templates', 'Templates Diff', 'manage_options', 'diff-overrided-template-for-woocommerce', [ $this, 'render' ] );
        }

        /**
         * Run tool
         *
         * @return void
         */
        public function render() {
            echo '<div class="wrap">';
            echo '<h1>' . esc_html__( 'Templates Diff', 'diff-overrided-template-for-woocommerce' ) . '</h1>';

            if ( ! class_exists( '\Automattic\WooCommerce\Utilities\RestApiUtil' ) || ! class_exists( '\WC_REST_System_Status_V2_Controller' ) ) {
                echo '<p>' . esc_html__( 'Unable to find WooCommerce classes. Is the plugin activated?', 'diff-overrided-template-for-woocommerce' ) . '</p></div>';
                return;
            }

            // Prepare tool
            \WC_REST_System_Status_V2_Controller::clean_theme_cache();
            \WC_REST_System_Status_V2_Controller::clean_plugin_cache();

            // Run report
            $report = wc_get_container()->get( \Automattic\WooCommerce\Utilities\RestApiUtil::class )->get_endpoint_data( '/wc/v3/system_status' );
            $theme  = $report['theme'] ?? [];

            $output_escaped = '';

            foreach ( $theme['overrides'] as $override ) {
                if ( empty( $override['file'] ) ) {
                    continue;
                }

                $filename = substr( $override['file'], strlen( 'rawines/woocommerce/' ) );

                if ( empty( $override['core_version'] ) || empty( $override['version'] ) ) {
                    $output_escaped .= '<h1>' . $filename . '</h1>';
                    $output_escaped .= '<p>' . esc_html__( 'Unable to read versions — "core_version" or "version" values are invalid.', 'diff-overrided-template-for-woocommerce' ) . '</p>';
                    break;
                }

                if ( ! version_compare( $override['version'], $override['core_version'], '<' ) ) {
                    continue;
                }

                $output_escaped .= '<h1 style="user-select: all; margin-bottom: 20px;">' . $filename . '</h1>';

                $github_current = $this->get_github_url( $filename, $override['version'] );
                $github_updated = $this->get_github_url( $filename, $override['core_version'] );

                $current_file = download_url( $github_current );
                $updated_file = download_url( $github_updated );

                if ( is_wp_error( $current_file ) ) {
                    $output_escaped .= '<p>' . esc_html__( 'Unable to download the file:', 'diff-overrided-template-for-woocommerce' ) . ' <strong>' . esc_html( $github_current ) . '</strong></p>';
                    $output_escaped .= '<p>' . $current_file->get_error_message() . '</p>';
                    break;
                }

                if ( is_wp_error( $updated_file ) ) {
                    $output_escaped .= '<p>' . esc_html__( 'Unable to download the file:', 'diff-overrided-template-for-woocommerce' ) . ' <strong>' . esc_html( $github_updated ) . '</strong></p>';
                    $output_escaped .= '<p>' . $updated_file->get_error_message() . '</p>';
                    break;
                }

                $diff = $this->get_diff( $current_file, $updated_file );

                wp_delete_file( $current_file );
                wp_delete_file( $updated_file );

                if ( is_wp_error( $diff ) ) {
                    $output_escaped .= '<p>' . esc_html__( 'Failed to generate the diff.', 'diff-overrided-template-for-woocommerce' ) . '</p>';
                    $output_escaped .= '<p>' . $diff->get_error_message() . '</p>';
                    break;
                }

                $id = uniqid();

                $editor_protocol = ( defined( 'DOT_TEXT_EDITOR_PROTOCOL' ) ? DOT_TEXT_EDITOR_PROTOCOL : 'vscode://file//' );

                $edit_output = '<ul><li><a href="' . $github_current . '" target="_blank">' . esc_html__( 'View', 'diff-overrided-template-for-woocommerce' ) . '</a> ' . esc_html__( 'or', 'diff-overrided-template-for-woocommerce' ) . ' <a href="' . $editor_protocol . ( defined( 'DOT_THEME_PATH' ) ? DOT_THEME_PATH : get_stylesheet_directory() ) . '/' . $override['file'] . '" target="_blank">' . esc_html__( 'edit', 'diff-overrided-template-for-woocommerce' ) . '</a> ' . esc_html__( 'the current file.', 'diff-overrided-template-for-woocommerce' ) . '</li>';
                $view_output = '<ul><li><a href="' . $github_current . '" target="_blank">' . esc_html__( 'View the current file.', 'diff-overrided-template-for-woocommerce' ) . '</a></li>';

                $output_escaped .= defined( 'DOT_THEME_PATH' ) ? $edit_output : $view_output;
                $output_escaped .= '<li><a href="' . $github_updated . '" target="_blank">' . esc_html__( 'View the updated file.', 'diff-overrided-template-for-woocommerce' ) . '</a></li></ul>';
                $output_escaped .= '<br>';

                $output_escaped .= str_replace( 'diff-table', 'diff-table-' . $id, ( $diff['html'] ?? '' ) );
                $output_escaped .= '<style>' . str_replace( 'diff-table', 'diff-table-' . $id, ( $diff['css'] ?? '' ) ) . '</style>';
                break;
            }

            if ( empty( $output_escaped ) ) {
                echo '<p>' . esc_html__( 'No template needs attention!', 'diff-overrided-template-for-woocommerce' ) . '</p>';
                echo '<br><a href="' . esc_attr( get_admin_url( null, 'admin.php?page=wc-status#status-table-templates' ) ) . '">' . esc_html__( 'Check in the WooCommerce status...', 'diff-overrided-template-for-woocommerce' ) . '</a>';

                /**
                 * A funny end. Take a coffee.
                 *
                 * Use "add_filter( 'dot_itsok_image', '__return_false' );" to remove.
                 */
                $finished_image = apply_filters( 'dot_itsok_image', DOT_PLUGIN_URL . '/assets/images/itsok.gif' );
                if ( ! empty( $finished_image ) ) {
                    echo '<div style="width: 100%;margin: 2rem 0;"><img style="width: 100%;max-width: 600px;" src="' . esc_attr( $finished_image ) .  '"/></div>';
                }

                echo '</div>';
                return;
            }

            echo '<p>' . esc_html__( 'We will show one file at a time, after fixing it, reload the page.', 'diff-overrided-template-for-woocommerce' ) . '</p>';

            echo '<div class="diffchecker-output">' . $output_escaped . '</div>'; // phpcs:ignore.WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<br><a href="#" onclick="window.scrollTo(0,0); window.location.reload(true); return false;">' . esc_html__( 'Check again...', 'diff-overrided-template-for-woocommerce' ) . '</a>';
            echo '</div>';
        }

        /**
         * Get Github URL
         */
        protected function get_github_url( $filename, $version ) {
            if ( version_compare( $version, '6.0.0', '<' ) ) {
                return 'https://raw.githubusercontent.com/woocommerce/woocommerce/refs/tags/' . $version . '/templates/' . $filename;
            }

            return 'https://raw.githubusercontent.com/woocommerce/woocommerce/refs/tags/' . $version . '/plugins/woocommerce/templates/' . $filename;
        }

        /**
         * Get diff for two files
         */
        protected function get_diff( $old_file, $new_file ) {
            $old_content = file_get_contents( $old_file );
            $new_content = file_get_contents( $new_file );

            $email = defined( 'DOT_DIFF_EMAIL' ) ? DOT_DIFF_EMAIL : get_bloginfo( 'admin_email' );
            $base_url = 'https://api.diffchecker.com/public/text?output_type=html_json&diff_level=word&email=' . urlencode( $email );

            $args = array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body' => json_encode( array(
                    'left' => $old_content,
                    'right' => $new_content,
                ) ),
            );

            $response = wp_remote_post( $base_url, $args );

            if ( is_wp_error( $response ) ) {
                return $response;
            }

            $response = wp_remote_retrieve_body( $response );

            return json_decode( $response, true );
        }

    }

}

/**
 * Making things happening
 */
$plugin = DOT_Debug_WooTheme_Diff::instance();
add_action( 'plugins_loaded', [ $plugin, 'init' ], 99 );
