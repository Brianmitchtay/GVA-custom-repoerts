<?php

/*
//Degugging Function to list menu pages, take item [2] from the array to target 
add_action( 'admin_init', function () {
    echo '<pre>' . print_r( $GLOBALS[ 'menu' ], true) . '</pre>';
} );
add_action( 'admin_menu', 'wpdocs_list_menus', 99999 );
*/


function hide_metaboxes_for_business_members() {
    // Check if the current user has the 'member' role
    $user = wp_get_current_user();
    $screens = array('donor', 'business_location', 'project', 'post', 'comment');
    if ( in_array( 'member', (array) $user->roles ) ) {
        // Remove the metaboxes we don't want this role to see
        remove_meta_box('postimagediv', $screens, 'side');  // Featured Image metabox
        remove_meta_box('tagsdiv-post_tag', $screens, 'side');  // Tags metabox
        remove_meta_box('acf-group_59840e6d0b343', $screens,'normal' ); //post layout
        remove_meta_box('et_settings_meta_box', $screens,'side' ); // Divi Page Settings
        remove_meta_box('permalinks-customizer-edit-box', $screens,'side' ); // Permalink settings
        remove_meta_box('cpb-metabox', $screens,'normal' ); // Custom Post Builder/Template
        remove_meta_box('business_location_taxonomydiv', $screens,'side' ); //Business Location Category
        remove_meta_box('postexcerpt', $screens,'normal' ); //Post Excerpt
        remove_meta_box('slugdiv', $screens,'normal' ); //Slug Editor
        remove_meta_box('advanced-sortables', $screens,'normal' ); //Custom Post Builder
    }
}
add_action( 'add_meta_boxes', 'hide_metaboxes_for_business_members', 99, 2);

function gva_member_dashboard_redirect() {
    $url = home_url('/member-dashboard/?statistics=1');
    echo '<script>window.location = "' . esc_js($url) . '";</script>';
}

function hide_admin_bar_for_members() {
    $user = wp_get_current_user();
    if ( in_array( 'member', (array) $user->roles ) || in_array( 'subscriber', (array) $user->roles ) ) {
        add_filter('show_admin_bar', '__return_false');
    }
}
add_action( 'init', 'hide_admin_bar_for_members' );


function modify_admin_menu_for_business_members() {
    $user = wp_get_current_user();
    if ( in_array( 'member', (array) $user->roles ) ) {
        remove_menu_page( 'edit.php?post_type=page' );  // Remove Pages menu
        remove_menu_page( 'plugins.php' );               // Remove Plugins menu
        remove_menu_page( 'edit.php?post_type=project' ); //project menu item
        remove_menu_page( 'edit.php?post_type=donor' ); // Donor menu
        remove_menu_page( 'edit.php?post_type=df_post_builder' ); // Posts Builder
        remove_menu_page( 'tools.php' ); // Tools Menu
        remove_menu_page( 'edit.php?post_type=project' ); // Projects 
        remove_menu_page( 'cp_contact_form_paypal' ); //PayPal Contact Form
        remove_menu_page( 'jetpack' ); // Jetpack
        remove_menu_page( 'index.php' ); //Dashboard 
        remove_menu_page( 'edit-comments.php'); // Comments
        remove_menu_page( 'edit.php');
        remove_menu_page( 'edit.php?post_type=simple-pay'); //WP SimplePay
        remove_submenu_page( 'edit.php?post_type=business_location', 'df-business-location-settings' );
		remove_submenu_page( 'edit.php?post_type=business_location', 'post-new.php?post_type=business_location' );
        add_menu_page( 'Listing Statistics', 'Listing Statistics', 'edit_assigned_listings', 'listing-statistics', 'gva_member_dashboard_redirect', 'dashicons-chart-line', 0);
    }
        if ( in_array( 'subscriber', (array) $user->roles ) ) {
            remove_menu_page( 'cp_contact_form_paypal' ); //PayPal Contact Form
        }
}

add_action( 'admin_menu', 'modify_admin_menu_for_business_members', 9999 );

add_filter('views_edit-business_location', 'gva_modify_edit_views'); 

function gva_modify_edit_views($views) {
    // Check if current user can manage_options (is an admin)
    if (current_user_can('manage_options')) {
        return $views;
    }// Show all views to admins
    
    // Remove all views except "Mine"
    $mine = isset($views['mine']) ? $views['mine'] : '';
    
    $views = [];
    
    if ($mine) {
        $views['mine'] = $mine;
    }

    return $views;
}

add_action('load-edit.php', 'redirect_to_mine_view');

function redirect_to_mine_view() {
    global $typenow, $pagenow;

    if (current_user_can('manage_options')) {
        return;
    }//Don't redirect admins

    if ('edit.php' === $pagenow) {
        if (!isset($_GET['author'])) {
            // Get the current user ID
            $user_id = get_current_user_id();
            
            // Redirect to "Mine" view
            $redirect_url = add_query_arg('author', $user_id, admin_url('edit.php'));
            if ($typenow) {
                $redirect_url = add_query_arg('post_type', $typenow, $redirect_url);
            }
            
            wp_redirect($redirect_url);
            exit;
        }
    }
}

add_action('admin_enqueue_scripts', 'remove_add_new_button');

function remove_add_new_button($hook) {
    global $typenow, $pagenow;

    if (current_user_can('manage_options')) {
        return;
    }
    
    /* Verify that we're on the edit.php page, post.php page, or profile.php page.
    Then CSS remove "quick edit" and "add-new" buttons as well as several admin elements we don't want members messing with */
    if ('edit.php' === $pagenow || 'post.php' === $pagenow || 'profile.php' === $pagenow) {
        echo '<style type="text/css">
        .page-title-action, button.button-link.editinline, span.inline.hide-if-no-js, #wpadminbar, #regenerate_permalink, #edit-slug-box > strong, span#editable-post-name {
            display: none !important;
        }
        #edit-slug-box { color: transparent !important; }
        html.wp-toolbar {padding-top: 0px !important;}
        #adminmenuwrap {margin-top: 0 !important;}
        </style>';
    }
}

?>