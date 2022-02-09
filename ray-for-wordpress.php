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

use WP_User;

defined('WPINC') || die;
require_once __DIR__ . '/vendor/autoload.php';

add_action(
    'plugins_loaded', function () {
        $number = 1000;
        ray("Testing $number records...");
        $records = getMetadataRecords($number, false);

        if (true) {
            ray()->measure()->green("Start foreach...");
            foreach ($records as $record) {
                $record;
            }
            ray()->measure()->green("...Done.");
        } else {
            ray()->measure()->red("Start array_map...");
            array_map(
                function ($record) {
                    $record;
                }, $records
            );
            ray()->measure()->red("...Done.");
        }
    }
);

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

        ray()->table(getUserCapabilities($user));
        ray()->trace();
        return $content;
    }
);

/**
 * Retrieves the first 100 results from the postmeta table.
 * This function is used specifically for demonstration purposes
 * of measuring performance.
 *
 * @param int  $number  The number of records to return.
 * @param bool $measure Whether or not to meawsure the performance with Ray.
 *
 * @return array $results The array of results queried.
 */
function getMetadataRecords(int $number, bool $measure): array
{
    global $wpdb;
    if ($measure) {
        ray()->measure();
    }

    $results = $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT *
            FROM $wpdb->postmeta
            LIMIT %d
            ",
            $number,
        ),
        ARRAY_A
    );

    if ($measure) {
        ray()->measure();
    }

    return $results;
}

/**
 * Retrieves the first 15 capabilities from the specified user.
 *
 * @param WP_User $user The user from which to retrieve the capabilities.
 *
 * @return array $capabilities The array of the first 15 capabilities from the user.
 */
function getUserCapabilities(WP_User $user): array
{
    $capabilities = [];
    ray('Pausing execution...');
    ray()->caller();
    ray($user->data->ID);
    ray($capabilities);
    ray()->pause();
    foreach ($user->allcaps as $key => $value) {
        $capabilities[$key] = $value;
        if (15 < count($capabilities)) {
            break;
        }
    }

    return $capabilities;
}
