<?php
/*
Plugin Name: TrashMail Contact Me
Plugin URI: https://github.com/AiondaDotCom/trashmail-wordpress-plugin
Description: Generate unique disposable contact addresses for each visitor.
Version: 1.1
Author: Sam Bull
Author URI: https://sambull.org
License: GPLv3+
*/

function trashmail_settings_init() {
    // Register all our options for the settings page.
    register_setting('tm-contact-me', 'trashmail_options');
    add_settings_section('trashmail_section_login', __('Login', 'tm-contact-me'),
                         null, 'tm-contact-me');
    add_settings_field('trashmail_username', __('Username', 'tm-contact-me'),
                       'trashmail_username_render', 'tm-contact-me',
                       'trashmail_section_login', ['label_for' => 'trashmail_username']);
    add_settings_field('trashmail_password', __('Password', 'tm-contact-me'),
                       'trashmail_password_render', 'tm-contact-me',
                       'trashmail_section_login', ['label_for' => 'trashmail_password']);
    add_settings_section('trashmail_section_settings', __('Settings', 'tm-contact-me'),
                         null, 'tm-contact-me');
    add_settings_field('trashmail_email', __('Your real email address', 'tm-contact-me'),
                       'trashmail_email_render', 'tm-contact-me',
                       'trashmail_section_settings', ['label_for' => 'trashmail_email']);
    add_settings_field('trashmail_prefix', __('Email address prefix', 'tm-contact-me'),
                       'trashmail_prefix_render', 'tm-contact-me',
                       'trashmail_section_settings', ['label_for' => 'trashmail_prefix']);
    add_settings_field('trashmail_length', __('Random alias length', 'tm-contact-me'),
                       'trashmail_length_render', 'tm-contact-me',
                       'trashmail_section_settings', ['label_for' => 'trashmail_length']);
    add_settings_field('trashmail_domain', __('Email address domain', 'tm-contact-me'),
                       'trashmail_domain_render', 'tm-contact-me',
                       'trashmail_section_settings', ['label_for' => 'trashmail_domain']);
    add_settings_field('trashmail_forwards', __('Number of forwards', 'tm-contact-me'),
                       'trashmail_forwards_render', 'tm-contact-me',
                       'trashmail_section_settings', ['label_for' => 'trashmail_forwards']);
    add_settings_field('trashmail_expire', __('Life span', 'tm-contact-me'),
                       'trashmail_expire_render', 'tm-contact-me',
                       'trashmail_section_settings', ['label_for' => 'trashmail_expire']);
    add_settings_field('trashmail_challenge', __('Challenge-Response System', 'tm-contact-me'),
                       'trashmail_challenge_render', 'tm-contact-me',
                       'trashmail_section_settings', ['label_for' => 'trashmail_challenge']);
    add_settings_field('trashmail_masq', __('Reply-Masquerading', 'tm-contact-me'),
                       'trashmail_masq_render', 'tm-contact-me',
                       'trashmail_section_settings', ['label_for' => 'trashmail_masq']);
    add_settings_field('trashmail_notify', __('Notify me when my account has expired', 'tm-contact-me'),
                       'trashmail_notify_render', 'tm-contact-me',
                       'trashmail_section_settings', ['label_for' => 'trashmail_notify']);

    // Add metabox to edit menu page
    add_meta_box('tm-contact-me', __('TrashMail Contact Me', 'tm-contact-me'),
                 'trashmail_meta_box_render', 'nav-menus', 'side');
}
add_action('admin_init', 'trashmail_settings_init');

function trashmail_username_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <input type="text" autocomplete="username" inputmode="verbatim" required
        id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]"
        value="<?= $options[$args['label_for']] ?>" />
    <?php
}

function trashmail_password_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <input type="password" autocomplete="current-password" minlength="6" required
        id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]"
        value="<?= $options[$args['label_for']] ?>" />
    <?php
}

function trashmail_email_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <select id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]">
    <?php
        foreach (array_keys($options['real_emails']) as $email) {
            ?>
            <option value="<?= $email; ?>" <?php selected($options[$args['label_for']], $email); ?>><?= $email; ?></option>
            <?php
        } ?>
    </select>
    <?php
}

function trashmail_prefix_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <input type="text" inputmode="verbatim" maxlength="16"
        id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]"
        value="<?= $options[$args['label_for']] ?>" />
    <?php
}

function trashmail_length_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <input type="number" inputmode="verbatim" min="2" max="16"
        id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]"
        value="<?= $options[$args['label_for']] ?? 7 ?>" />
    <?php
}

function trashmail_domain_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <select id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]">
    <?php
        foreach ($options['domains'] as $domain) {
            ?>
            <option value="<?= $domain; ?>" <?php selected($options[$args['label_for']], $domain); ?>><?= $domain; ?></option>
            <?php
        } ?>
    </select>
    <?php
}

function trashmail_forwards_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <select id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]">
        <option value="1" <?php selected($options[$args['label_for']], 1); ?>>1</option>
        <option value="2" <?php selected($options[$args['label_for']], 2); ?>>2</option>
        <option value="3" <?php selected($options[$args['label_for']], 3); ?>>3</option>
        <option value="4" <?php selected($options[$args['label_for']], 4); ?>>4</option>
        <option value="5" <?php selected($options[$args['label_for']], 5); ?>>5</option>
        <option value="10" <?php selected($options[$args['label_for']], 10); ?>>10</option>
        <option value="100" <?php selected($options[$args['label_for']], 100); ?>>100*</option>
        <option value="1000" <?php selected($options[$args['label_for']], 1000); ?>>1000*</option>
        <option value="-1" <?php selected($options[$args['label_for']], -1); ?>><?= __('unlimited', 'tm-contact-me'); ?></option>
    </select>
    <?php
}

function trashmail_expire_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <select id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]">
        <option value="1" <?php selected($options[$args['label_for']], 1); ?>>1 <?= __('day', 'tm-contact-me'); ?></option>
        <option value="2" <?php selected($options[$args['label_for']], 2); ?>>2 <?= __('days', 'tm-contact-me'); ?></option>
        <option value="3" <?php selected($options[$args['label_for']], 3); ?>>3 <?= __('days', 'tm-contact-me'); ?></option>
        <option value="4" <?php selected($options[$args['label_for']], 4); ?>>4 <?= __('days', 'tm-contact-me'); ?></option>
        <option value="5" <?php selected($options[$args['label_for']], 5); ?>>5 <?= __('days', 'tm-contact-me'); ?></option>
        <option value="6" <?php selected($options[$args['label_for']], 6); ?>>6 <?= __('days', 'tm-contact-me'); ?></option>
        <option value="7" <?php selected($options[$args['label_for']], 7); ?>>1 <?= __('week', 'tm-contact-me'); ?></option>
        <option value="14" <?php selected($options[$args['label_for']], 14); ?>>2 <?= __('weeks', 'tm-contact-me'); ?></option>
        <option value="21" <?php selected($options[$args['label_for']], 21); ?>>3 <?= __('weeks', 'tm-contact-me'); ?></option>
        <option value="30" <?php selected($options[$args['label_for']], 30); ?>>1 <?= __('month', 'tm-contact-me'); ?></option>
        <option value="60" <?php selected($options[$args['label_for']], 60); ?>>2 <?= __('months', 'tm-contact-me'); ?></option>
        <option value="90" <?php selected($options[$args['label_for']], 90); ?>>3 <?= __('months', 'tm-contact-me'); ?></option>
        <option value="120" <?php selected($options[$args['label_for']], 120); ?>>4 <?= __('months', 'tm-contact-me'); ?></option>
        <option value="150" <?php selected($options[$args['label_for']], 150); ?>>5 <?= __('months', 'tm-contact-me'); ?></option>
        <option value="180" <?php selected($options[$args['label_for']], 180); ?>>6 <?= __('months', 'tm-contact-me'); ?></option>
        <option value="-1" <?php selected($options[$args['label_for']], -1); ?>><?= __('never', 'tm-contact-me'); ?></option>
    </select>
    <?php
}

function trashmail_challenge_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <input type="checkbox" value="1" <?php checked(1, $options[$args['label_for']]); ?>
        id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]" />
    <?php
}

function trashmail_masq_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <input type="checkbox" value="1" <?php checked(1, $options[$args['label_for']]); ?>
        id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]" />
    <?php
}

function trashmail_notify_render($args) {
    $options = get_option('trashmail_options');
    ?>
    <input type="checkbox" value="1" <?php checked(1, $options[$args['label_for']]); ?>
        id="<?= esc_attr($args['label_for']); ?>"
        name="trashmail_options[<?= esc_attr($args['label_for']); ?>]" />
    <?php
}

function trashmail_options_page() {
    add_submenu_page(
        'options-general.php', 'TrashMail Contact Me', 'TrashMail Options',
        'manage_options', 'tm-contact-me', 'trashmail_options_page_render');
}
add_action('admin_menu', 'trashmail_options_page');

/** Displays our options page. */
function trashmail_options_page_render() {
    if (!current_user_can('manage_options'))
        return;

    if (isset($_GET['settings-updated'])) {
        $options = get_option('trashmail_options');
        $res = trashmail_login($options['trashmail_username'], $options['trashmail_password']);
        if ($res[0] === true)
            add_settings_error('trashmail_messages', 'trashmail_message', __('Settings Saved', 'tm-contact-me'), 'updated');
        else
            add_settings_error('trashmail_messages', 'trashmail_message', $res[1]);
    }

    settings_errors('trashmail_messages');
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('tm-contact-me');
            do_settings_sections('tm-contact-me');
            echo '<p>' . __('* requires TrashMail Plus', 'tm-contact-me') . '</p>';
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

/** Add link to settings on plugin page. */
function trashmail_add_action_links($links) {
    $mylinks = array(
        '<a href="' . admin_url('options-general.php?page=tm-contact-me') . '">' . __('Settings') . '</a>',
    );
    return array_merge($links, $mylinks);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'trashmail_add_action_links');


/** Log into TM and updates domains/real_emails. Sets and returns session id transient. */
function trashmail_login($user, $password) {
    $url = 'https://trashmail.com/?api=1&cmd=login&fe-login-user=' . $user . '&fe-login-pass=' . $password;
    $result = wp_remote_post($url);
    if (is_wp_error($result)) {
        // Handle network errors
        $result = array('success' => false, 'msg' => $result->get_error_message());
    } else {
        $result = json_decode($result['body'], true);
    }

    if ($result['success']) {
        $options = get_option('trashmail_options');
        $options['domains'] = $result['msg']['domain_name_list'];
        $options['real_emails'] = $result['msg']["real_email_list"];
        update_option('trashmail_options', $options);

        set_transient('trashmail_session_id', $result['msg']['session_id'], MONTH_IN_SECONDS);

        return array(true, $result['msg']['session_id']);
    } else {
        return array(false, $result['msg']);
    }
}


/** The shortcode which will output our mailto link with required JS. */
function trashmail_shortcode($atts) {
    if ($_COOKIE['tm-contact-me']) {
        // Use previous address from cookie.
        $email = $_COOKIE['tm-contact-me'];
        return '<a href="mailto:' . $email . '">' . $email . '</a>';
    }

    $atts = shortcode_atts(array(
        'text' => __('Contact Me', 'tm-contact-me')
    ), $atts);
    $options = get_option('trashmail_options');

    $loading = __('Loading...', 'tm-contact-me');
    $domain = parse_url(get_home_url(), PHP_URL_HOST);
    $age = ($options['trashmail_expire'] > 0) ? DAY_IN_SECONDS * $options['trashmail_expire'] : YEAR_IN_SECONDS;

    $script = '
        event.preventDefault();
        this.removeAttribute("onclick");
        this.textContent = "' . $loading . '";

        var elem = this;
        let headers = new Headers({"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"});
        let body = "action=trashmail_create_address&url=" + window.location.href;
        fetch("' . admin_url('admin-ajax.php') . '", {"method": "POST", "headers": headers, "body": body}).then(function (res) {
            return res.json();
        }).then(function (res) {
            if (res[0]) {
                elem.href = "mailto:" + res[1];
                elem.textContent = res[1];
                document.cookie = "tm-contact-me=" + res[1] + ";path=/;domain=' . $domain . ';max-age=' . $age . '";
            } else {
                elem.parentNode.replaceChild(document.createTextNode("ERROR: " + res[1]), elem);
            }
        });';

    $output = '<a href="?tm-contact-me=true" onclick=\'' . $script . '\'>' . $atts['text'] . '</a>';

    return $output;
}
add_shortcode('trashmail_contact_me', 'trashmail_shortcode', 'tm-contact-me');

/** Generates the disposable name given a prefix and the number of random chars. */
function _trashmail_generate_name($prefix, $length) {
    $chars = str_split('abcdefghijklmnopqrstuvwxyz0123456789');
    $name = $prefix;
    for ($i=0; $i < $length; ++$i)
        $name .= $chars[rand(0, 35)];

    return $name;
}

/** AJAX function that generates a new address and returns it to the JS code. */
function trashmail_create_address($retry=false) {
    $options = get_option('trashmail_options');

    if (($session_id = get_transient('trashmail_session_id')) === false) {
        $res = trashmail_login($options['trashmail_username'], $options['trashmail_password']);
        if ($res[0] === true) {
            $session_id = $res[1];
        } else {
            return array(false, $res[1]);
        }
    }

    $url = 'https://trashmail.com/?api=1&cmd=create_dea&session_id=' . $session_id;
    $data = array('data' => array(
            'disposable_name' => _trashmail_generate_name($options['trashmail_prefix'] ?? '', $options['trashmail_length'] ?? 7),
            'disposable_domain' => $options['trashmail_domain'] ?? 'trashmail.com',
            'destination' => $options['trashmail_email'] ?? $options['real_emails'][0],
            'forwards' => $options['trashmail_forwards'] ?? 1,
            'expire' => $options['trashmail_expire'] ?? 1,
            'cs' => $options['trashmail_challenge'] ?? false,
            'masq' => $options['trashmail_masq'] ?? false,
            'notify' => $options['trashmail_notify'] ?? false,
            'desc' => 'Autogenerated by WordPress at: ' . $_POST['url'] ?? 'unknown URL'
        )
    );
    $result = wp_remote_post($url, array('body' => json_encode($data)));
    if (is_wp_error($result)) {
        // Handle network errors
        if (!$retry) {
            delete_transient('trashmail_session_id');
            return trashmail_create_address(true);
        } else {
            $result = array('success' => false, 'msg' => $result->get_error_message());
        }
    } else {
        $result = json_decode($result['body'], true);
    }

    if ($result['success']) {
        $email = $data['data']['disposable_name'] . '@' . $data['data']['disposable_domain'];
        return array(true, $email);
    } else {
        // Expired session ID
        if ($result['error_code'] == 2 && !$retry) {
            delete_transient('trashmail_session_id');
            return trashmail_create_address(true);
        }

        return array(false, $result['msg'] ?? $result['message']);
    }
}


function trashmail_ajax_create_address() {
    echo json_encode(trashmail_create_address());
    wp_die();
}
add_action('wp_ajax_trashmail_create_address', 'trashmail_ajax_create_address');
add_action('wp_ajax_nopriv_trashmail_create_address', 'trashmail_ajax_create_address');


/*****
 * Custom Menu Item
 *****/

/** Enqueue a script on edit menu page. */
function trashmail_enqueue($hook) {
    if ($hook == 'nav-menus.php')
        wp_enqueue_script('tm-admin-menu', plugin_dir_url(__FILE__) . 'admin-menu.js', array('nav-menu'));
}
add_action('admin_enqueue_scripts', 'trashmail_enqueue');

/** Render the meta box to add our custom menu items. */
function trashmail_meta_box_render() {
    global $_nav_menu_placeholder, $nav_menu_selected_id;
    $nav_menu_placeholder = $_nav_menu_placeholder < 0 ? $_nav_menu_placeholder - 1 : -1;
    ?>
    <div id="tm-div" class="customlinkdiv">
        <p class="wp-clearfix">
            <label class="howto" for="tm-menu-text"><?php esc_html_e('Link Text'); ?></label>
            <input id="tm-menu-text" name="menu-item[<?= esc_attr($nav_menu_placeholder); ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox" value="<?= __('Contact Me', 'tm-contact-me') ?>" />
        </p>

        <p class="button-controls wp-clearfix">
            <span class="add-to-menu">
                <input id="submit-tm" type="submit" <?php wp_nav_menu_disabled_check($nav_menu_selected_id); ?> class="button submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-tm-menu-item" />
                <span class="spinner"></span>
            </span>
        </p>
    </div>
    <?php
}

/** Customise the label displayed in the menu structure. */
function trashmail_setup_menu_item($item) {
    if (is_object($item) && $item->object == 'tm-contact-me')
        $item->type_label = __('TrashMail', 'tm-contact-me');

    return $item;
}
add_filter('wp_setup_nav_menu_item', 'trashmail_setup_menu_item');

/** Customise the frontend output of our menu items. */
function trashmail_menu_item_frontend($item_output, $item) {
    if ($item->object == 'tm-contact-me')
        return do_shortcode('[trashmail_contact_me text="' . $item->title . '"]');

    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'trashmail_menu_item_frontend', 20, 2);
add_filter('megamenu_walker_nav_menu_start_el', 'trashmail_menu_item_frontend', 20, 2);

/** If JS disabled, generate address when link followed. */
function trashmail_nojs() {
    if (!empty($_GET['tm-contact-me']) && empty($_COOKIE['tm-contact-me'])) {
        $result = trashmail_create_address();
        if ($result[0]) {
            $options = get_option('trashmail_options');
            $age = ($options['trashmail_expire'] > 0) ? DAY_IN_SECONDS * $options['trashmail_expire'] : YEAR_IN_SECONDS;
            $domain = parse_url(get_home_url(), PHP_URL_HOST);
            setcookie('tm-contact-me', $result[1], time() + $age, '/', $domain);
        }
        $_COOKIE['tm-contact-me'] = $result[1];
    }
}
add_action('send_headers', 'trashmail_nojs');
