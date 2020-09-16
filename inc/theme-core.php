<?php
/**
 * 核心函数
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.07.29
 */

if (kratos_option('g_cdn', false)) {
    $asset_path = 'https://cdn.jsdelivr.net/gh/Virace/kratos-pe@' . THEME_VERSION;
} else {
    $asset_path = get_template_directory_uri();
}
define('ASSET_PATH', $asset_path);

// 自动跳转主题设置
function init_theme()
{
    global $pagenow;
    if ('themes.php' == $pagenow && isset($_GET['activated'])) {
        wp_redirect(admin_url('admin.php?page=kratos_options'));
        exit;
    }
}

add_action('load-themes.php', 'init_theme');

// 语言国际化
function theme_languages()
{
    load_theme_textdomain('kratos', get_template_directory() . '/languages');
}

add_action('after_setup_theme', 'theme_languages');

// 资源加载
function theme_autoload()
{
    if (!is_admin()) {
        // css
        wp_enqueue_style('bootstrap', ASSET_PATH . '/assets/css/bootstrap.min.css', array(), '4.5.1');
        wp_enqueue_style('vicon', ASSET_PATH . '/assets/css/iconfont.min.css', array(), THEME_VERSION);
        wp_enqueue_style('layer', ASSET_PATH . '/assets/css/layer.min.css', array(), '3.1.1');
        wp_enqueue_style('ballon', ASSET_PATH . '/assets/css/ballon.min.css', array(), '1.2.1');
        wp_enqueue_style('np', ASSET_PATH . '/assets/css/nprogress.min.css', array(), '0.2.0');
        if (kratos_option('g_animate', false)) {
            wp_enqueue_style('animate', ASSET_PATH . '/assets/css/animate.min.css', array(), '4.1.0');
        }
        wp_enqueue_style('kratos', ASSET_PATH . '/assets/css/kratos.css', array(), THEME_VERSION);
        wp_enqueue_style('aos', ASSET_PATH . '/assets/css/aos.min.css', array(), '3.0.0-6');
        wp_enqueue_style('custom', get_template_directory_uri() . '/custom/custom.css', array(), THEME_VERSION);

        $bg_color = kratos_option('g_background', '#f5f5f5');
        $theme_color1 = kratos_option('g_theme_color1', '#00a2ff');
        $theme_color2 = kratos_option('g_theme_color2', '#0097ee');
        $top_color_1 = kratos_option('top_color_1', 'rgba(40, 42, 44, 0.6)');
        $top_color_2 = kratos_option('top_color_2', '#fff');
        $mb_sidebar_color = kratos_option('mb_sidebar_color', '#242b31');

        $root = "body{--bg-color:{$bg_color};--theme-color-1:{$theme_color1}; --theme-color-2:{$theme_color2};--navbar-color-1:{$top_color_1}; --navbar-color-2:{$top_color_2};--mb-sidebar-color:{$mb_sidebar_color}}";
        wp_add_inline_style('kratos', $root);

        // js
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', ASSET_PATH . '/assets/js/jquery.min.js', array(), '3.4.1', false);
        wp_enqueue_script('np', ASSET_PATH . '/assets/js/nprogress.min.js', array(), '0.2.0', true);
        wp_enqueue_script('pjax', ASSET_PATH . '/assets/js/pjax.min.js', array(), THEME_VERSION, true);
        wp_enqueue_script('aos', ASSET_PATH . '/assets/js/aos.min.js', array(), '3.0.0-6', true);
        wp_enqueue_script('bootstrap', ASSET_PATH . '/assets/js/bootstrap.min.js', array(), '4.5.1', true);
        wp_enqueue_script('bootstrap-ahn', ASSET_PATH . '/assets/js/jquery.bootstrap.autohidingnavbar.min.js', array(), '4.0.0', true);
        wp_enqueue_script('layer', ASSET_PATH . '/assets/js/layer.min.js', array(), '3.1.1', true);
        wp_enqueue_script('kratos', ASSET_PATH . '/assets/js/kratos.min.js', array(), THEME_VERSION, true);
        wp_enqueue_script('custom', get_template_directory_uri() . '/custom/custom.js', array(), THEME_VERSION, true);
        $data = array(
            'site' => home_url(),
            'directory' => get_stylesheet_directory_uri(),
            'alipay' => kratos_option('g_donate_alipay', ASSET_PATH . '/assets/img/donate.png'),
            'wechat' => kratos_option('g_donate_wechat', ASSET_PATH . '/assets/img/donate.png'),
            'repeat' => __('您已经赞过了', 'kratos'),
            'thanks' => __('感谢您的支持', 'kratos'),
            'donate' => __('打赏作者', 'kratos'),
            'scan' => __('扫码支付', 'kratos'),
        );
        wp_localize_script('kratos', 'kratos', $data);
    }

    if (is_page() || is_single()) {
        wp_enqueue_style('highlight', get_template_directory_uri() . '/assets/css/highlight/style.min.css', array(), '10.2.0');
        wp_enqueue_script('highlight', ASSET_PATH . '/assets/js/highlight/highlight.pack.js', array(), '10.2.0', true);
        wp_enqueue_script('highlight-ln', ASSET_PATH . '/assets/js/highlight/highlightjs-line-numbers.min.js', array(), '2.8.0', true);
        wp_enqueue_script('highlight-copy', ASSET_PATH . '/assets/js/highlight/highlightjs-copy-button.min.js', array(), '1.0.5', true);
    }

    // 哀悼黑白站点
    if (is_home() && kratos_option('g_rip', false)) {
        $data = 'html{filter: grayscale(100%);-webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);-ms-filter: grayscale(100%);-o-filter: grayscale(100%);filter: progid:DXImageTransform.Microsoft.BasicImage(grayscale=1);filter: gray;-webkit-filter: grayscale(1); }';
        wp_add_inline_style('kratos', $data);
    }
}

add_action('wp_enqueue_scripts', 'theme_autoload');

// 禁用 Admin Bar
add_filter('show_admin_bar', '__return_false');

// 移除自动保存、修订版本
remove_action('post_updated', 'wp_save_post_revision');

// 添加友情链接
add_filter('pre_option_link_manager_enabled', '__return_true');

// 禁用转义
$qmr_work_tags = array('the_title', 'the_excerpt', 'single_post_title', 'comment_author', 'comment_text', 'link_description', 'bloginfo', 'wp_title', 'term_description', 'category_description', 'widget_title', 'widget_text');

foreach ($qmr_work_tags as $qmr_work_tag) {
    remove_filter($qmr_work_tag, 'wptexturize');
}

remove_filter('the_content', 'wptexturize');
add_filter('run_wptexturize', '__return_false');

// 禁用 Emoji
add_filter('emoji_svg_url', '__return_false');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content', 'wptexturize');
remove_filter('comment_text', 'wptexturize');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('embed_head', 'print_emoji_detection_script');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

// 禁用 Trackbacks
add_filter('xmlrpc_methods', function ($methods) {
    $methods['pingback.ping'] = '__return_false';
    $methods['pingback.extensions.getPingbacks'] = '__return_false';
    return $methods;
});
remove_action('do_pings', 'do_all_pings', 10);
remove_action('publish_post', '_publish_post_hook', 5);

// 优化 wp_head() 内容
foreach (array('rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 'comments_atom_head', 'opml_head', 'app_head') as $action) {
    remove_action($action, 'the_generator');
}
remove_action('wp_head', 'wp_print_head_scripts', 9);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10);
remove_action('wp_head', 'start_post_rel_link', 10);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
remove_action('wp_head', 'wp_shortlink_wp_head', 10);
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'wp_shortlink_header', 11);
remove_action('template_redirect', 'rest_output_link_header', 11);

// 禁用 WordPress 拼写修正
remove_filter('the_title', 'capital_P_dangit', 11);
remove_filter('the_content', 'capital_P_dangit', 11);
remove_filter('comment_text', 'capital_P_dangit', 31);

// 禁用后台 Google Fonts
add_filter('style_loader_src', function ($href) {
    if (strpos($href, "fonts.googleapis.com") === false) {
        return $href;
    }
    return false;
});

// 禁用 Auto Embeds
remove_filter('the_content', array($GLOBALS['wp_embed'], 'autoembed'), 8);

// 替换国内 Gravatar 源
function get_https_avatar($avatar)
{
    if (kratos_option('g_gravatar', false)) {
        $cdn = "gravatar.loli.net";
    } else {
        $cdn = "cn.gravatar.com";
    }

    $avatar = str_replace(array("www.gravatar.com", "0.gravatar.com", "1.gravatar.com", "2.gravatar.com", "3.gravatar.com", "secure.gravatar.com"), $cdn, $avatar);
    $avatar = str_replace("http://", "https://", $avatar);
    return $avatar;
}

add_filter('get_avatar', 'get_https_avatar');

// 主题更新检测
//$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
//    'https://github.com/vtrois/kratos/',
//    get_template_directory() . '/functions.php',
//    'Kratos'
//);

// 禁止生成多种尺寸图片
if (kratos_option('g_removeimgsize', false)) {
    function remove_default_images($sizes)
    {
        unset($sizes['thumbnail']);
        unset($sizes['medium']);
        unset($sizes['large']);
        unset($sizes['medium_large']);
        return $sizes;
    }

    add_filter('intermediate_image_sizes_advanced', 'remove_default_images');
}

// 重定向优化
add_action('template_redirect', 'redirect_single_post');
function redirect_single_post()
{
    if (is_search()) {
        global $wp_query;
        if ($wp_query->post_count == 1) {
            wp_redirect(get_permalink($wp_query->posts['0']->ID));
        }
    }
}

function get_404()
{
    global $wpdb, $wp_rewrite;

    if (get_query_var('name')) {
        $where = $wpdb->prepare("post_name LIKE %s", like_escape(get_query_var('name')) . '%');

        $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $where AND post_status = 'publish'");
        if (!$post_id)
            return false;
        if (get_query_var('feed'))
            return get_post_comments_feed_link($post_id, get_query_var('feed'));
        elseif (get_query_var('page'))
            return trailingslashit(get_permalink($post_id)) . user_trailingslashit(get_query_var('page'), 'single_paged');
        else
            return get_permalink($post_id);
    }

    return false;
}

//解决日志改变 post type 之后跳转错误的问题，
add_action('template_redirect', 'old_slug_redirect');
function old_slug_redirect()
{
    global $wp_query;
    if (is_404() && '' != $wp_query->query_vars['name']) :
        global $wpdb;

        $query = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_old_slug' AND meta_value = %s", $wp_query->query_vars['name']);

        $id = (int)$wpdb->get_var($query);

        if (!$id) {
            $link = get_404();
        } else {
            $link = get_permalink($id);
        }

        if (!$link)
            return;

        wp_redirect($link, 301);
        exit;
    endif;
}


// 禁用admin登录,

if (kratos_option('g_no_admin', false)) {
    add_filter('wp_authenticate', 'no_admin_user');
    function no_admin_user($user)
    {
        if ($user == 'admin') {
            exit;
        }
    }

    add_filter('sanitize_user', 'sanitize_user_no_admin', 10, 3);
    function sanitize_user_no_admin($username, $raw_username, $strict)
    {
        if ($raw_username == 'admin' || $username == 'admin') {
            exit;
        }
        return $username;
    }
}
function get_current_page_url()
{
    return set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

add_action('template_redirect', function () {
    if (!is_404()) {
        return;
    }

    $request_url = get_current_page_url();

    if (strpos($request_url, 'feed/atom/') !== false) {
        wp_redirect(str_replace('feed/atom/', '', $request_url), 301);
        exit;
    }

    if (strpos($request_url, 'comment-page-') !== false) {
        wp_redirect(preg_replace('/comment-page-(.*)\//', '', $request_url), 301);
        exit;
    }

    if (strpos($request_url, 'page/') !== false) {
        wp_redirect(preg_replace('/page\/(.*)\//', '', $request_url), 301);
        exit;
    }

    if ($_301_redirects = get_option('301-redirects')) {
        foreach ($_301_redirects as $_301_redirect) {
            if ($_301_redirect['request'] == $request_url) {
                wp_redirect($_301_redirect['destination'], 301);
                exit;
            }
        }
    }
}, 99);



