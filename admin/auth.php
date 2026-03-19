<?php
/**
 * CHRONOS Admin — Auth Guard
 * Include this at the top of every admin page
 */
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
