<?php
/*
Plugin Name: Hashy
Plugin URI:
Description: Rename uploaded file name with file hash.
Version: 1.1.2
Author: Hiroiku Inoue
License: MIT
 */

add_filter('wp_handle_upload_prefilter', function ($file) {
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);
    $algorithm = get_option('mb_hashy_algorithm');
    $algorithm = in_array($algorithm, hash_algos(), true) ? $algorithm : 'sha256';
    $hash = hash_file($algorithm, $file['tmp_name']);
    $filename = "{$hash}.{$extension}";
    $file['name'] = $filename;

    global $wpdb;
    $sql = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_name = '{$hash}'";

    if ($wpdb->get_var($sql)) {
        $file['error'] = "This file already exists with the name {$filename}";
    }

    return $file;
});
