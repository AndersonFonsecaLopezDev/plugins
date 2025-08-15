<?php
// asistencia-form-styles.php - Estilos CSS para el formulario de asistencia
?>
<style>
    .asistencia-form {
        max-width: 1000px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(3,76,140,0.08);
        padding: 3rem 3rem 2rem 3rem;
    }
    .asistencia-form table {
        width: 100%;
        border-collapse: collapse;
    }
    .asistencia-form th, .asistencia-form td {
        padding: 1rem 0.8rem;
        text-align: left;
        font-size: 16px;
    }
    .asistencia-form th {
        background: #f6f8fa;
    }
    .asistencia-form tr:nth-child(even) {
        background: #f9fbfc;
    }
    .asistencia-form select {
        padding: 0.6rem 1rem;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
        transition: all 0.2s ease;
    }
    .asistencia-form select option[value="asistió"] {
        background-color: #d4edda !important;
        color: #155724 !important;
    }
    .asistencia-form select option[value="faltó"] {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }
    .asistencia-form select option[value="tarde"] {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }
    .asistencia-form select option[value="justificado"] {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
    }
    .asistencia-form select option:checked {
        font-weight: bold;
    }
    .asistencia-form select.asistió {
        background-color: #d4edda !important;
        color: #155724 !important;
        border-color: #c3e6cb !important;
        background-image: none !important;
    }
    .asistencia-form select.faltó {
        background-color: #f8d7da !important;
        color: #721c24 !important;
        border-color: #f5c6cb !important;
        background-image: none !important;
    }
    .asistencia-form select.tarde {
        background-color: #fff3cd !important;
        color: #856404 !important;
        border-color: #ffeaa7 !important;
        background-image: none !important;
    }
    .asistencia-form select.justificado {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
        border-color: #bee5eb !important;
        background-image: none !important;
    }
    .asistencia-form button {
        margin-top: 1.5rem;
        background: #034C8C;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 1rem 2rem;
        font-size: 18px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .asistencia-form button:hover {
        background: #005f8d;
    }
    .mensaje {
        text-align: center;
        color: var(--success-color);
        font-weight: 600;
        margin-bottom: 2rem;
        font-size: 1.1rem;
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-radius: 16px;
        border: 2px solid var(--success-color);
        box-shadow: var(--shadow-sm);
        backdrop-filter: blur(5px);
        animation: fadeInUp 0.6s ease-out;
    }
    .volver-btn {
        display: inline-flex;
        align-items: center;
        margin-bottom: 2rem;
        background: var(--bg-light);
        color: var(--accent-color);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
        box-shadow: var(--shadow-sm);
    }
    .volver-btn:hover {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .asistencia-titulo {
        text-align: center;
        color: var(--primary-color);
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 0.8s ease-out;
    }
    /* Estilos para los buscadores de asistencia */
    .asistencia-search-container {
        max-width: 1000px;
        margin: 0 auto 2rem auto;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 1.5rem 2rem;
        border: 1px solid #e0e0e0;
        animation: fadeInUp 0.6s ease-out;
    }

    .asistencia-search-wrapper {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.95);
        border: none;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        flex-grow: 1;
        position: relative;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        max-width: 800px;
    }

    .asistencia-search-wrapper:focus-within {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        background: rgba(255, 255, 255, 1);
    }



    .asistencia-search-input {
        border: none;
        background: transparent;
        padding: 1rem 0.5rem 1rem 0;
        font-size: 1.2rem;
        width: 100%;
        outline: none;
        color: #333;
        font-weight: 500;
        min-height: 25px;
    }

    .asistencia-search-input::placeholder {
        color: #999;
        font-weight: 400;
    }

    .asistencia-search-results-info {
        margin-top: 0.8rem;
        text-align: center;
        color: #666;
        font-weight: 500;
        font-size: 0.95rem;
    }
</style>
