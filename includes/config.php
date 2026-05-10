<?php
define('BASE_URL', '/tix');
define('APP_NAME', 'TIX');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
