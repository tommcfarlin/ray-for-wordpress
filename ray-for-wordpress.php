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

/**
 * Iterates through the specified array of numbers.
 *
 * @param array $numbers The array of numbers through which we'll iteratoe.
 *
 * @return void.
 */
function iterateThroughArray(array $numbers)
{
    for ($i < 0; $i < count($numbers); $i++) {
        $currentNumber = $numbers[$i];

        // If the index is 10, pause execution.
        if (10 === $i) {
            ray("Pausing iterating at $i.");
            ray()->pause();

            // TODO This is where you can set custom code to run!
            ray('Now we are resuming...');
            ray(wp_get_current_user());

            // Set $i to the length of the array then hit continue.
            $i = count($numbers);
            ray("Now we've set the index to: $i."); // Print this into Ray for good measure
            ray()->separator();
        }
    }
}

/**
 * Measures the time is takes to generate random numbers based on
 * an incoming array of predefined size.
 *
 * This function would be called like measureNumbers([10, 20, 30]).
 * If any non-integers are passed, then the function would exit whenever
 * that value was found.
 *
 * @param  array $sizes The array of what size arrays to generate.
 *
 * @return void
 */
function measureNumbers(array $sizes)
{
    array_map(function ($size) {
        if (!is_int($size)) {
            return;
        }

        ray()->measure(); // Start measuring.

        iterateThroughArray(
            generateNumbers(0, 500, $size)
        );

        ray()->measure(); // Render the "Time since last call..."
        ray()->separator();
    }, $sizes);
}

/**
 * Generates a set of random numbers up to the specified size.
 *
 * This uses PHP's `rand()` function to generate the numbers that
 * are pushed into the array.
 *
 * @param  mixed $min The lower bound of the random numbers.
 * @param  mixed $max The upper mount of the random numbers.
 * @param  mixed $size The total number of random numbers to generate
 *
 * @return array $numbers The array of randomly generatored numbers.
 */
function generateNumbers(int $min, int $max, int $size): array
{
    $numbers = [];

    for ($i = 0; $i < $size; $i++) {
        $numbers[] = rand($min, $max);
    }

    return $numbers;
}

add_action(
    'plugins_loaded',
    function () {
        if ('done' === get_option('ray-for-wordpress', true)) {
            return;
        }

        // First, we want to see who called this function.
        ray()->caller();
        ray()->separator();

        measureNumbers([100, 250, 500, 750, 1000, 10000]);

        update_option('ray-for-wordpress', 'done');
    }
);

add_action(
    'shutdown',
    function () {
        ray('Deleting the ray-for-wordpress option since we are done.');
        delete_option('ray-for-wordpress');
    }
);
