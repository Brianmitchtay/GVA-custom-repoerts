<?php
/* Template Name: Member Dashboard - Listing 
Deprecated and not used since V0.2.8*/
wp_enqueue_style('gva_member_reports.css', plugins_url('/gva_member_reports.css', dirname(__FILE__)));
get_header();
?>

<div id="main-content">
<?php if ($logged_in == true) {
    $menu = '<nav class="member-menu">
                <ul class="member-menu">
                    <li class="member-menu-item menu-listing"><a class="member-menu-item menu-listing" href="'. site_url('/member-dashboard?listing=1') .'"><i class="fa-solid fa-pen-to-square"></i>Edit Your Listing</a></li>
                    <li class="member-menu-item menu-statistics"><a class="member-menu-item menu-statistics" href="'. site_url('/member-dashboard?statistics=1') .'"><i class="fa-solid fa-chart-line"></i>View Statistics</a></li>
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
                    } else {
                        echo '<div class="gva-alert"><p>You must be logged in to view this page.</p></div>';
                         echo '<div class="gva-login-register"><section  class="member-login"> <h4> Log in </h4>';
                         wp_login_form();
                         echo "</section><section class='member-register'> <h4> Register </h4>"; 
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