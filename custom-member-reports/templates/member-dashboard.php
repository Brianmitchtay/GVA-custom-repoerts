<?php
/* Template Name: Member Dashboard */
wp_enqueue_style('gva_member_reports.css', plugins_url('/gva_member_reports.css', dirname(__FILE__)));
get_header();
$logged_in = false;
// 'edit_assigned_listings' is a custom user capability, deterinses what roles can see the dashboard
if (is_user_logged_in() && current_user_can('edit_assigned_listings')) {
    $logged_in = true;
}
$lostpw = sprintf( '<a class="lostpw" href="%s">%s</a>', esc_url( wp_lostpassword_url() ), __( 'Lost your password?' ) );
function custom_registration_redirect($user_id) {
    $referrer = wp_get_referer();
    $expected_page = site_url('/member-dashboard/' );

    if ($user_id && $referrer == $expected_page) {
        // User registration was successful and happened on the expected page
        wp_redirect(add_query_arg('registration', 'success', $referrer));
        exit;
    }
}
add_action('register_new_user', 'custom_registration_redirect');


/* Add custom fields to member registration form || Removed for now, as wasn't necessary, added field via ACF.
// 1. Add a new form element...
add_action( 'register_form', 'gva_register_form' );
function gva_register_form() {

    $stated_connected_business = ( ! empty( $_POST['stated_connected_business'] ) ) ? sanitize_text_field( $_POST['stated_connected_business'] ) : '';
        
    ?>
    <p>
        <label for="stated_connected_business"><?php _e( 'Stated Connected Business', 'mydomain' ) ?><br />
            <input type="text" name="stated_connected_business" id="stated_connected_business" class="input" value="<?php echo esc_attr( $stated_connected_business ); ?>" size="25" /></label>
    </p>
    <?php
}

// 2. Add validation. We make sure stated_connected_business is required.
add_filter( 'registration_errors', 'gva_registration_errors', 10, 3 );
function gva_registration_errors( $errors, $sanitized_user_login, $user_email ) {
    
    if ( empty( $_POST['stated_connected_business'] ) || ! empty( $_POST['stated_connected_business'] ) && trim( $_POST['stated_connected_business'] ) == '' ) {
        $errors->add( 'stated_connected_business_error', __( '<strong>ERROR</strong>: Please enter the name of your business.', 'custom-member-reports' ) );
    }

    return $errors;
}

// 3. Save stated_connected_business value to user meta data
add_action( 'user_register', 'gva_user_register' );
function gva_user_register( $user_id ) {
    if ( isset( $_POST['stated_connected_business'] ) ) {
        update_user_meta( $user_id, 'stated_connected_business', sanitize_text_field( $_POST['stated_connected_business'] ) );
    }
}*/ 
?>

<div id="main-content">
<?php if ($logged_in == true) {
    $menu = '<nav class="member-menu">
                <ul class="member-menu">
                    <li class="member-menu-item menu-listing"><a class="member-menu-link menu-listing" href="'. site_url('/wp-admin/edit.php?post_type=business_location') .'"><i class="fa-solid fa-pen-to-square"></i>Edit Your Listing</a></li>
                    <li class="member-menu-item menu-statistics"><a class="member-menu-link menu-statistics" href="'. site_url('/member-dashboard?statistics=1') .'"><i class="fa-solid fa-chart-line"></i>View Statistics</a></li>
                    <li class="member-menu-item menu-logout"><a class="member-menu-link menu-logout" href="'. wp_logout_url() .'"><i class="fa-solid fa-right-from-bracket"></i>Log Out</a></li>
                </ul>
            </nav>';
    echo $menu;
}
    ?>
    <div class="dashboard-container">
        <div id="content-area" class="clearfix">            

            <div class="dashboard-content">
                <?php
                    if ($logged_in == TRUE) {
                        $dashboard = '
                        <h2>Welcome to your GVA business member Dashboard</h2>
						';
                        echo $dashboard;
                        the_content();
                    } elseif (isset($_GET['registration']) && $_GET['registration'] == 'success') {
                        echo '<div class="gva-success-message">Registration successful! Please check your email for further instructions.</div>';
                        echo '<div class="gva-login-register"><section  class="member-login"> <h4> Log in </h4>';
                         wp_login_form();		
						echo $lostpw;
                         echo "</section><section class='member-register'> <h3> Register </h3>"; 
                         echo '<form name="registerform" method="post" action="' . site_url('wp-login.php?action=register', 'login_post') . '">
                                    <p class="username">
                                        <label for="user_login">Username</label>
                                        <input type="text" name="user_login" value="" id="user_login">
                                    </p>

                                    <p class="password">
                                        <label for="user_email">Email</label>
                                        <input type="text" name="user_email" value="" id="user_email">
                                    </p>
                                ';
    
                        do_action( 'register_form' );

                        echo '
                            <input type="submit" class="button-primary" value="Register" id="register">
                            ';//commented out <input type="hidden" name="redirect_to" value="https://www.gustavusak.com/member-dashboard/">
                        wp_nonce_field( 'register' );
                        echo '
                            </form>';
                         echo "</section></div>";
					}
                    else {
                        echo '<div class="gva-alert"><p>You must be logged in to view this page.</p></div>';
                         echo '<div class="gva-login-register"><section  class="member-login"> <h4> Log in </h4>';
                         wp_login_form();
						echo $lostpw;
                         echo "</section><section class='member-register'> <h3> Register </h3>"; 
                         echo '<form name="registerform" method="post" action="' . site_url('wp-login.php?action=register', 'login_post') . '">
                                    <p class="username">
                                        <label for="user_login">Username</label>
                                        <input type="text" name="user_login" value="" id="user_login">
                                    </p>

                                    <p class="password">
                                        <label for="user_email">Email</label>
                                        <input type="text" name="user_email" value="" id="user_email">
                                    </p>
                                ';
    
                        do_action( 'register_form' );

                        echo '
                            <input type="submit" class="button-primary" value="Register" id="register">
                            <input type="hidden" name="redirect_to" value="https://www.gustavusak.com/member-dashboard/">
                            ';
                        wp_nonce_field( 'register' );
                        echo '
                            </form>';
                         echo "</section></div>";
                    }
                ?>
            </div>         

        </div>
    </div>

</div>
<?php
get_footer();
?>