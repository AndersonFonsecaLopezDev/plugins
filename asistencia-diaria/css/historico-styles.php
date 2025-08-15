<?php
// historico-styles.php - Estilos CSS para el histórico de asistencias
?>
<style>
    .historico-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.5rem;
        text-align: center;
    }
    .historico-subtitle {
        color: var(--text-muted);
        text-align: center;
        margin-bottom: 2rem;
        font-size: 1rem;
    }
    .historico-table-container {
        background: var(--bg-cell);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
    }
    .historico-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 1rem;
    }
    .historico-table th {
        background: var(--bg-light);
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-main);
        border-bottom: 2px solid var(--border-color);
    }
    .historico-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
    }
    .historico-table tr:hover {
        background: var(--bg-hover);
    }
    .estado-select {
        padding: 0.5rem 0.75rem;
        border: 2px solid var(--border-color);
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--bg-cell);
        color: var(--text-main);
    }
    .estado-select.asistió {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }
    .estado-select.faltó {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }
    .estado-select.tarde {
        background-color: #fff3cd;
        color: #856404;
        border-color: #ffeaa7;
    }
    .estado-select.justificado {
        background-color: #d1ecf1;
        color: #0c5460;
        border-color: #bee5eb;
    }
    .estado-select:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    .btn-guardar {
        padding: 0.5rem 1rem;
        background: var(--accent-color);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-guardar:hover {
        background: var(--primary-color);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
</style>
