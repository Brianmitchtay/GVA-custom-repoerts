<?php
/**
 * Plugin Name: GVA Custom Member Reports
 * Plugin URI: https://gustavusak.com
 * Description: Custom plugin created to add a member dashboard with a listing editor and statistics page.
 * Text Domain: custom-member-reports
 * Version: 0.3.1
 * Author: Brian Taylor, GPT-4
 * Author URI: https://brianmt.blog
 * License: GPL3.0
 */



// Define Query Vars for membership dashoboard page
 function gva_add_query_vars($vars) {
    $vars[] = 'listing';
    $vars[] = 'statistics';
    return $vars;
}
add_filter('query_vars', 'gva_add_query_vars');

// Specify the templates to use for our pages
function gva_member_template_include($template) {
    if (is_page('member-dashboard')) {
        if (get_query_var('listing')) {
            return plugin_dir_path(__FILE__) . 'templates/listing.php';
        } elseif (get_query_var('statistics')) {
            return plugin_dir_path(__FILE__) . 'templates/statistics.php';
        } else {
            return plugin_dir_path(__FILE__) . 'templates/member-dashboard.php';
        }
    }
    return $template;
}
add_filter('template_include', 'gva_member_template_include', 10001);

// When the plugin is activated, flush the rewrite rules for good measure
function gva_activate() {
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'gva_activate');

// When the plugin is deactivated, flush the rewrite rules again
function gva_deactivate() {
    flush_rewrite_rules();
}

// Redirect Logged in business members to Member Dashboard
add_filter( 'login_redirect', 'business_member_login_redirect', 10, 3 );
function business_member_login_redirect( $redirect_to, $request, $user ) {
    $user = wp_get_current_user();
    $admin_roles = ['administrator', 'editor'];
    $subscriber_roles = ['subscriber', 'member', 'contributor'];
    // If the user has the 'member' role, redirect them to the home page.
    if (array_intersect($subscriber_roles, $user->roles)){
        return home_url() . "member-dashboard/";
    }
    return $redirect_to;
}
/* Commented out old way
 * add_filter( 'login_redirect', 'business_member_login_redirect', 10, 3 );
function business_member_login_redirect( $redirect_to, $request, $user ) {
    // If the user has the 'member' role, redirect them to the home page.
    if ( isset($user->roles) && in_array( 'member', (array) $user->roles ) ) {
        return home_url() . "member-dashboard/";
    }
    return $redirect_to;
}
*/

//Redirect logged out users based on their role. Business members, Subscribers, and Contributors return to the home page.
add_action('clear_auth_cookie', 'gva_logout_redirect_based_on_role', 10);

function gva_logout_redirect_based_on_role() {
    $user = wp_get_current_user();

    $admin_roles = ['administrator', 'editor'];
    $subscriber_roles = ['subscriber', 'member', 'contributor'];

    if (array_intersect($admin_roles, $user->roles)) {
        // Redirect logic for administrators and editors
        add_action('wp_logout', function() {
            wp_redirect(admin_url());
            exit();
        });
    } elseif (array_intersect($subscriber_roles, $user->roles)) {
        // Redirect logic for subscribers, members, and contributors
        add_action('wp_logout', function() {
            wp_redirect(home_url());
            exit();
        });
    }
}
// Include Admin permissions
include( plugin_dir_path( __FILE__ ) . 'admin-permissions.php' );

register_deactivation_hook(__FILE__, 'gva_deactivate');