<?php
/**
 * CHRONOS — Helpers
 * Site settings helper functions
 */
require_once __DIR__ . '/db.php';

/**
 * Get all settings as key => value array
 */
function getAllSettings() {
    static $cache = null;
    if ($cache !== null) return $cache;

    try {
        $db = getDB();
        $rows = $db->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll();
        $cache = [];
        foreach ($rows as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
        return $cache;
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get a single setting value
 */
function getSetting($key, $default = '') {
    $settings = getAllSettings();
    return isset($settings[$key]) && $settings[$key] !== '' ? $settings[$key] : $default;
}

/**
 * Output setting value with htmlspecialchars
 */
function e($key, $default = '') {
    return htmlspecialchars(getSetting($key, $default));
}
