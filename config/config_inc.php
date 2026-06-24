<?php
$g_hostname = 'aws-1-ap-northeast-1.pooler.supabase.com:6543';
$g_db_type = 'pgsql';
$g_database_name = 'postgres';
$g_db_username = 'postgres.wdndnocfbiixsfzjdxcy';
$g_db_password = 'Siddhesh@1600';

$g_default_timezone = 'UTC';

$g_crypto_master_salt = 'a1vvLuWGCilppaViYjv/kIDKQFAVFKMm0im6bJVAPjI=';

$g_path = 'https://mantisbt-ba47.onrender.com/';
$g_enable_email_notification = ON;

$g_phpMailer_method = PHPMAILER_METHOD_SMTP;
$g_smtp_host = 'smtp.gmail.com';
$g_smtp_port = 587;
$g_smtp_connection_mode = 'tls';
$g_smtp_username = 'jsiddhesh40@gmail.com';
$g_smtp_password = 'kbzw rluv dhxm ohpl';
$g_smtp_auth = ON;

$g_from_name = 'MantisBT';
$g_administrator_email = 'jsiddhesh40@gmail.com';
$g_webmaster_email = 'jsiddhesh40@gmail.com';
$g_from_email = 'jsiddhesh40@gmail.com';
$g_return_path_email = 'jsiddhesh40@gmail.com';

$g_file_upload_method = DATABASE;
$g_max_file_size = 50 * 1024 * 1024; # 50 MB

$g_display_errors = array(
    E_WARNING => DISPLAY_ERROR_INLINE,
    E_NOTICE => DISPLAY_ERROR_INLINE,
    E_USER_ERROR => DISPLAY_ERROR_INLINE,
    E_USER_WARNING => DISPLAY_ERROR_INLINE,
);

error_reporting(E_ALL);
ini_set('display_errors', 1);