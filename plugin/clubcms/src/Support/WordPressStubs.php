<?php

declare(strict_types=1);

// These stubs are for static analysis and local tooling only.
// They are guarded so they do not override real WordPress functions.

if (! function_exists('add_action')) {
    function add_action($hook_name, $callback, $priority = 10, $accepted_args = 1)
    {
        return null;
    }
}

if (! function_exists('add_filter')) {
    function add_filter($hook_name, $callback, $priority = 10, $accepted_args = 1)
    {
        return null;
    }
}

if (! function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback)
    {
        return null;
    }
}

if (! function_exists('add_menu_page')) {
    function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url = '', $position = null)
    {
        return null;
    }
}

if (! function_exists('add_submenu_page')) {
    function add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback, $position = null)
    {
        return null;
    }
}

if (! function_exists('add_query_arg')) {
    function add_query_arg($args, $url = '')
    {
        if (! is_array($args) || $args === []) {
            return (string) $url;
        }

        $separator = str_contains((string) $url, '?') ? '&' : '?';

        return (string) $url . $separator . http_build_query($args);
    }
}

if (! function_exists('admin_url')) {
    function admin_url($path = '', $scheme = 'admin')
    {
        return (string) $path;
    }
}

if (! function_exists('check_admin_referer')) {
    function check_admin_referer($action = -1, $query_arg = '_wpnonce')
    {
        return 1;
    }
}

if (! function_exists('current_user_can')) {
    function current_user_can($capability, ...$args)
    {
        return true;
    }
}

if (! function_exists('delete_option')) {
    function delete_option($option)
    {
        return true;
    }
}

if (! function_exists('esc_attr')) {
    function esc_attr($text)
    {
        return (string) $text;
    }
}

if (! function_exists('esc_html')) {
    function esc_html($text)
    {
        return (string) $text;
    }
}

if (! function_exists('get_option')) {
    function get_option($option, $default = false)
    {
        return $default;
    }
}

if (! function_exists('is_user_logged_in')) {
    function is_user_logged_in()
    {
        return false;
    }
}

if (! function_exists('home_url')) {
    function home_url($path = '', $scheme = null)
    {
        return (string) $path;
    }
}

if (! function_exists('load_plugin_textdomain')) {
    function load_plugin_textdomain($domain, $deprecated = false, $plugin_rel_path = false)
    {
        return true;
    }
}

if (! function_exists('plugin_basename')) {
    function plugin_basename($file)
    {
        return basename((string) $file);
    }
}

if (! function_exists('plugin_dir_path')) {
    function plugin_dir_path($file)
    {
        return rtrim(str_replace('\\', '/', dirname((string) $file)), '/') . '/';
    }
}

if (! function_exists('plugin_dir_url')) {
    function plugin_dir_url($file)
    {
        return '';
    }
}

if (! function_exists('sanitize_key')) {
    function sanitize_key($key)
    {
        $key = strtolower((string) $key);
        $key = preg_replace('/[^a-z0-9_\-]/', '', $key) ?? '';

        return trim($key, "_-");
    }
}

if (! function_exists('sanitize_text_field')) {
    function sanitize_text_field($str)
    {
        $str = strip_tags((string) $str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str) ?? $str;

        return trim($str);
    }
}

if (! function_exists('sanitize_title')) {
    function sanitize_title($title)
    {
        $title = strtolower((string) $title);
        $title = preg_replace('/[^a-z0-9]+/', '-', $title) ?? '';

        return trim($title, '-');
    }
}

if (! function_exists('submit_button')) {
    function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null)
    {
        return '';
    }
}

if (! function_exists('show_admin_bar')) {
    function show_admin_bar($show = true)
    {
        return (bool) $show;
    }
}

if (! function_exists('update_option')) {
    function update_option($option, $value, $autoload = null)
    {
        return true;
    }
}

if (! function_exists('wp_die')) {
    function wp_die($message = '', $title = '', $args = [])
    {
        throw new RuntimeException((string) $message);
    }
}

if (! function_exists('wp_nonce_field')) {
    function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $display = true)
    {
        return '';
    }
}

if (! function_exists('wp_safe_redirect')) {
    function wp_safe_redirect($location, $status = 302, $x_redirect_by = 'WordPress')
    {
        return true;
    }
}
