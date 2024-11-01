<?php
/*
Plugin Name: WP Guest Bar
Plugin URI:   https://wordpress.org/plugins/wp-guest-bar
Description: Adds a BuddyPress guest bar (login+register) to your WordPress site and show a message!
Version: 2.3
Author: Marco Milesi
Author URI:   https://wordpress.org/plugins/wp-guest-bar
Contributors: Milmor
*/
class WpGuestBar {

	function __construct() {
		add_action( 'init', array( $this, 'show_admin_bar' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_bar_menu', array( $this, 'customize_admin_bar' ), 11 );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
	}

	function customize_admin_bar( $wp_admin_bar) {

        if ( is_user_logged_in() ) {
            return;
        }

        $wp_admin_bar->remove_node( 'wp-logo' );
        
        $wp_admin_bar->add_menu( array(
            'id'     => 'wpgb-home',
            'parent' => null,
            'group'  => null,
            'title' => '<span class="ab-icon dashicons dashicons-admin-home"></span><span class="wpdb-hide-mobile">'.get_bloginfo( 'name' ).'</span>',
            'href'   => get_site_url()
        ) );
        
        if ( get_option( 'users_can_register' ) ) {
            $wp_admin_bar->add_menu(
                array(
                    'id'     => 'wpgb-register',
                    'title' => __( 'Register' ),
                    'href' => wp_registration_url(),
                    'parent' => 'top-secondary'
                )
            );
        }

        $wp_admin_bar->add_menu(
            array(
                'id'     => 'wpgb-login',
                'title' => '<span class="ab-icon dashicons dashicons-lock"></span><span class="wpdb-hide-mobile">'.__( 'Log In' ).'</span>',
                'href' => wp_login_url(),
                'parent' => 'top-secondary'
            )
        );
        
        $options = get_option('wpgov_wpgb');
        
        if ( isset( $options['message'] ) && $options['message'] ) {
            $wp_admin_bar->add_menu(
                array(
                'id'     => 'wpgb-custom-message',
                'title' => ( $options['message'] )
                )
            );
        }
	}

    function enqueue_script() {
        $admin_css = '
        @media screen and (max-width: 782px) {
            .wpdb-hide-mobile {
                display: none;
            }
			#wpadminbar li#wp-admin-bar-wpgb-home,
			#wpadminbar li#wp-admin-bar-wpgb-login {
				display: block;
			}

			#wpadminbar li#wp-admin-bar-wpgb-home a,
			#wpadminbar li#wp-admin-bar-wpgb-login a {
				padding: 2px 8px;
			}
		}';
        
        wp_add_inline_style( 'admin-bar', $admin_css );
    }

	function show_admin_bar( $show ) {
        add_filter( 'show_admin_bar', '__return_true' , 1000 );
	}

    function register_setting() {
        register_setting( 'wpgov_wpgb_options', 'wpgov_wpgb' );
    }
    function register_menu() {
        add_options_page('WP Guest Bar', 'WP Guest Bar', 'manage_options', 'wpgov_wpgb', function() { ?>
            <div class="wrap">
                <h2>WP Guest Bar</h2><p>The bar is only visible to non-logged users.</p>
                <form method="post" action="options.php">
                        <?php 
                settings_fields('wpgov_wpgb_options');
                $options = get_option('wpgov_wpgb');
                        ?>
                        <table class="form-table">
                            <tr valign="top"><th scope="row"><label for="networkshareurl">Top Bar Message</label></th>
                                <td><input id="networkshareurl" type="text" name="wpgov_wpgb[message]" value='<?php echo esc_attr( isset($options['message']) ? $options['message'] : '' ); ?>' size="80"/><br><small>You can use html. Example:<code>&lt;span style="background-color:red;color:white;padding: 5px;"&gt;Hi User :)&lt;/span&gt;</code></small></td>
                            </tr>
                            
                        </table>
                    <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                        </p>
                    </form>
                    
            </div>
        <?php } );
    }

}

$WpGuestBar = new WpGuestBar();
