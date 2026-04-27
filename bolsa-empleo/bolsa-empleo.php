<?php
/*
Plugin Name: Bolsa de empleo
Description: Plugin para consultar informacion de las hojas de vida
Version: 0.1
Author: Anderson Fonseca Lopez
*/

require_once plugin_dir_path(__FILE__) . 'includes/be-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/ru-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/lu-functions.php';

// Hook de activación
register_activation_hook(__FILE__, 'be_plugin_activate');

function be_plugin_activate() {
    global $wpdb;

    // Crear roles de WordPress
    add_role('estudiante', 'Estudiante', ['read' => true]);
    add_role('secretario', 'Secretario', ['read' => true]);
    add_role('talento_humano', 'Talento Humano', ['read' => true]);

    // Modificar tabla wp_aess_users
    $table_name = $wpdb->prefix . 'aess_users';
    
    // Lista de columnas a agregar
    $columns = [
        'academico' => "VARCHAR(255) DEFAULT ''",
        'profesion' => "VARCHAR(255) DEFAULT ''",
        'estado_laboral' => "VARCHAR(100) DEFAULT ''",
        'libreta_militar' => "VARCHAR(100) DEFAULT ''",
        'experiencia' => "TEXT",
        'diploma' => "VARCHAR(255) DEFAULT ''",
        'estatura' => "VARCHAR(50) DEFAULT ''",
        'barrio' => "VARCHAR(255) DEFAULT ''",
        'cv_aprobado' => "TINYINT(1) DEFAULT 0"
    ];

    foreach ($columns as $column_name => $column_definition) {
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE '{$column_name}'");
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN {$column_name} {$column_definition}");
        }
    }
}