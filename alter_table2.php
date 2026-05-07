<?php
require_once('../wp-load.php');
global $wpdb;
$wpdb->query("ALTER TABLE {$wpdb->prefix}cc_certificados ADD COLUMN estado varchar(20) DEFAULT 'activo' NOT NULL");
echo "Done";
