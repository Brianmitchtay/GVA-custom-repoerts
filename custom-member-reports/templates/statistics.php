<?php
/* Template Name: Member Dashboard - Statistics */
wp_enqueue_style('gva_member_reports.css', plugins_url('/gva_member_reports.css', dirname(__FILE__)));
get_header();
$logged_in = FALSE;
// 'edit_assigned_listings' is a custom user capability, deterinses what roles can see the dashboard
if (is_user_logged_in() && current_user_can('edit_assigned_listings')) {
    $logged_in = TRUE;
}
?>
<div id="main-content">
<?php if ($logged_in == TRUE) {
    $menu = '<nav class="member-menu">
                <ul class="member-menu">
                    <li class="member-menu-item menu-listing"><a class="member-menu-link menu-listing" href="'. site_url('wp-admin/edit.php?post_type=business_location') .'"><i class="fa-solid fa-pen-to-square"></i>Edit Your Listing</a></li>
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
                    } else {
                        echo '<div class="gva-alert"><p>You must be logged in as a business member to view this page.</p></div>';
                         echo '<div class="gva-login-register"><section  class="member-login"> <h4> Log in </h4>';
                         wp_login_form();
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
                        <?php
                            $user_id =  'user_' . get_current_user_id();
                            $field_value = get_field('connected_businesses', $user_id); // uses custom field added by ACF set by admin on signup
                            $post_object = $field_value[0];
                            $bn = $post_object->post_name;
                            $name = $post_object->post_title;
                        ?>
                    <p>
	                    <?php if ($logged_in == TRUE) { echo "Now viewing reports for $name"; }?>
                    </p>

                    <?php
                    $encoded_bn = urlencode($bn);
                    $new_embed_url = "https://lookerstudio.google.com/embed/reporting/b47c5389-ad29-4216-a8e7-286ca6e523cb/page/p_s344yj532c";
                    $iframe_src = "{$new_embed_url}?params=%7B%22ds31.business_name_filter%22%3A%22{$encoded_bn}%22%7D";
                    
                        if ($logged_in == TRUE) { 
                            echo '<iframe width="1200" height="1000" src="'.$iframe_src.'" frameborder="1" style="border:2px solid #002157;" allowfullscreen></iframe>';
                            the_content();
                        }
                    ?>
            </div>         

        </div>
    </div>

</div>
<?php
get_footer();
?>