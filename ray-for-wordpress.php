<?php

/**
 * Ray For WordPress
 *
 * An example project for how to use Spatie Ray in WordPress Development.
 *
 * PHP version 7.4.27
 *
 * @category WordPress_Plugin
 * @package  RayForWP
 * @author   Tom McFarlin <tom@tommcfarlin.com>
 * @license  GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 * @link     https://github.com/tommcfarlin/ray-for-wordpress/
 * @since    11 January 2022
 *
 * @wordpress-plugin
 * Plugin Name: Ray For WordPress
 * Plugin URI:  https://github.com/tommcfarlin/ray-for-wordpress/
 * Description: An example project for how to use Spatie Ray in WordPress Development.
 * Author:      Tom McFarlin <tom@tommcfarlin.com>
 * Version:     1.0.0
 */

namespace RayForWP;

defined('WPINC') || die;
require_once __DIR__ . '/vendor/autoload.php';

add_filter(
    'the_content',
    /**
     * Renders the content to the browser from the database.
     *
     * @param string $content The content coming from WordPress.
     *
     * @return string $content The processed content to be sent to the browser.
     */
    function (string $content): string {
        if (!is_single()) {
            return $content;
        }

        $user = wp_get_current_user();
        ray($user);

        ray(
            get_user_meta($user->data->ID, 'show_admin_bar_front', true)
        );

        $capabilities = [];
        foreach ($user->allcaps as $key => $value) {
            $capabilities[$key] = $value;
            if (15 < count($capabilities)) {
                break;
            }
        }
        ray()->table($capabilities);

        return $content;
    }
);
