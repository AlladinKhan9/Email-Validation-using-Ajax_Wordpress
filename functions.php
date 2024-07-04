<?php
// Enqueue js file and localize script ajax_url and nonce
add_action('wp_enqueue_scripts', 'enqueue_script_file');
function enqueue_script_file()
{
    wp_enqueue_script('custom-slider', get_stylesheet_directory_uri().'/assets/js/custom.js', array('jquery'), filemtime( get_stylesheet_directory() . '/assets/js/custom.js' ), true);
    wp_localize_script( 'custom-slider', 'custom_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('custom_nonce')
        // Add more variables as needed
    ) );
}

// AJAX handler for email validation
add_action('wp_ajax_contact_us_email_validation', 'contact_us_email_validation');
add_action('wp_ajax_nopriv_contact_us_email_validation', 'contact_us_email_validation');
function contact_us_email_validation() {
    check_ajax_referer('custom_nonce', 'nonce');
    // Check if email is not empty and sanitize it
    if (!empty($_POST['email'])) {
        $email = sanitize_email($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Extract domain from email
            $domain_name = substr(strrchr($email, "@"), 1);

            // Initialize email checker flag
            $email_checker_test = false;

            // Check if domain name is not empty
            if (!empty($domain_name)) {
                // Check if domain has MX records
                if (checkdnsrr($domain_name, "MX")) {
                    $email_checker_test = true;
                }
            }
            // Return validation result
            if ($email_checker_test) {
                wp_send_json_success(array('message' => 'Valid email.'));
            } else {
                wp_send_json_error(array('message' => 'Invalid email domain.'));
            }
        } else {
            wp_send_json_error(array('message' => 'Invalid email format.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Email field is empty.'));
    }
    wp_die(); // This is required to terminate immediately and return a proper response
}
?>
