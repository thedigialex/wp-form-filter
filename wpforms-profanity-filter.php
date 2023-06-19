<?php
/*
Plugin Name: WPForms Profanity Filter
Plugin URI: https://your-plugin-url.com
Description: Prevents profanity words from Paragraph Text form fields in WPForms.
Version: 1.0.0
Author: TheDigiAlex
Author URI: https://thedigialex.com
*/

function wpf_dev_profanity_filter_settings() {
    add_options_page(
        'Profanity Filter Settings',
        'Profanity Filter',
        'manage_options',
        'wpf-dev-profanity-filter',
        'wpf_dev_profanity_filter_settings_page'
    );
}
add_action('admin_menu', 'wpf_dev_profanity_filter_settings');

function wpf_dev_profanity_filter_settings_page() 
{
    if (isset($_POST['wpf_dev_blocked_words'])) 
    {
        $blocked_words = sanitize_text_field($_POST['wpf_dev_blocked_words']);
        update_option('wpf_dev_blocked_words', $blocked_words);
    }
    $blocked_words = get_option('wpf_dev_blocked_words', '');

    ?>
    <div class="wrap">
        <h1>Profanity Filter Settings</h1>
        <form method="post" action="">
            <label for="wpf_dev_blocked_words">Blocked Words (comma-separated):</label>
            <input type="text" id="wpf_dev_blocked_words" name="wpf_dev_blocked_words" value="<?php echo esc_attr($blocked_words); ?>">
            <p class="description">Enter the list of blocked words separated by commas.</p>
            <p><input type="submit" class="button button-primary" value="Save Changes"></p>
        </form>
    </div>
    <?php
}

function wpf_dev_profanity_filter_paragraph($field_id, $field_submit, $form_data) {
    $blocked_words = get_option('wpf_dev_blocked_words', '');
    $blocked_words_array = explode(',', $blocked_words);
    $blocked_words_array = array_map('trim', $blocked_words_array);

    foreach ($blocked_words_array as $word) {
        if (strpos($field_submit, $word) !== false) {
            wpforms()->process->errors[$form_data['id']][$field_id] = esc_html__('Spam comment detected.', 'plugin-domain');
            return;
        }
    }
}
function wpf_dev_add_profanity_filter() {
    add_action('wpforms_process_validate_textarea', 'wpf_dev_profanity_filter_paragraph', 10, 3);
}
add_action('wpforms_loaded', 'wpf_dev_add_profanity_filter');
