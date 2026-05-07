<?php
require_once('../wp-load.php');
global $wpdb;
$wpdb->query("ALTER TABLE {$wpdb->prefix}aess_users ADD COLUMN curso_realizado varchar(200) DEFAULT '' NOT NULL, ADD COLUMN fecha_acta_curso date DEFAULT NULL, ADD COLUMN fecha_vencimiento_curso date DEFAULT NULL");
echo "Done";
