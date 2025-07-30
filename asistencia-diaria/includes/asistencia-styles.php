<?php
// asistencia-styles.php - Estilos CSS para el dashboard de asistencia diaria
?>
<style>
    :root {
        --primary-color: #034C8C;
        --secondary-color: #D9C077;
        --bg-light: #f6f8fa;
        --bg-cell: #fff;
        --text-main: #222;
        --text-muted: #666;
        --today-circle: #b3d4fc;
    }
    
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #f6f8fa;
        margin: 0;
        padding: 0;
        color: var(--text-main);
    }
    
    .dashboard-wrapper {
        max-width: 2200px;
        margin: 0 auto;
        padding: 4rem 3rem;
        background: var(--bg-light);
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(3,76,140,0.04);
    }
    
    .calendar-scroll {
    }
    
    .calendar-grid {
        display: grid;
        grid-template-columns: 90px repeat(7, 1fr);
        grid-template-rows: 70px repeat(12, 120px);
        gap: 1px;
        background: var(--secondary-color);
        border-radius: 12px;
        overflow: hidden;
        margin: 0;
        min-width: 1100px;
    }
    
    .calendar-header-cell {
        background: var(--primary-color);
        color: #fff;
        text-align: center;
        font-weight: bold;
        border-bottom: 1px solid #e0e0e0;
        font-size: 1.1rem;
        position: sticky;
        top: 0;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 70px;
    }
    
    .calendar-header-cell .fecha {
        margin-top: 4px;
        font-size: 1.2em;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .calendar-header-cell .hoy {
        background: #cce4ff;
        color: var(--primary-color);
        border-radius: 50%;
        padding: 2px 9px;
        font-weight: bold;
        font-size: 1em;
        margin-top: 2px;
        display: inline-block;
    }
    
    .calendar-header-cell .dia {
        font-size: 1em;
        letter-spacing: 1px;
    }
    
    .calendar-hour {
        background: var(--bg-light);
        color: var(--text-muted);
        text-align: right;
        padding: 8px 10px 0 0;
        font-size: 1rem;
        border-right: 1px solid #e0e0e0;
    }
    
    .calendar-cell {
        background: var(--bg-cell);
        min-height: 120px;
        position: relative;
        overflow: hidden;
    }
    
    .calendar-event {
        position: absolute;
        left: 10px; right: 10px;
        top: 10px;
        min-height: 60px;
        max-height: 120px;
        background: #fff;
        color: #222;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(3,76,140,0.10);
        padding: 12px 18px;
        font-size: 1.12rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        cursor: pointer;
        border: 2px solid var(--primary-color);
        transition: box-shadow 0.2s, transform 0.2s, border 0.2s;
        overflow: hidden;
        word-break: break-word;
        white-space: normal;
        text-decoration: none;
    }
    
    /* Estilos para eventos superpuestos en columna */
    .calendar-event.superpuesto {
        width: calc(100% - 20px);
        min-height: 30px;
        max-height: 50px;
        padding: 4px 8px;
        font-size: 0.8rem;
        z-index: 10;
        margin-bottom: 2px;
        position: absolute;
        left: 10px;
        right: 10px;
    }
    
    .calendar-event.superpuesto:nth-child(1) {
        top: 5px;
    }
    
    .calendar-event.superpuesto:nth-child(2) {
        top: 55px;
    }
    
    .calendar-event.superpuesto:nth-child(3) {
        top: 105px;
    }
    
    .calendar-event.superpuesto:nth-child(4) {
        top: 155px;
    }
    
    /* Mejorar la visualización de eventos superpuestos */
    .calendar-cell {
        position: relative;
    }
    
    .calendar-cell.multiple-events {
        overflow: hidden;
    }
    
    .calendar-event.superpuesto:hover {
        z-index: 20;
        transform: scale(1.05);
        box-shadow: 0 4px 16px rgba(3,76,140,0.20);
    }
    
    /* Tooltip de información */
    .event-tooltip {
        position: fixed;
        background: #2c3e50;
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        pointer-events: none;
        display: none;
        max-width: 280px;
        line-height: 1.4;
    }
    
    .tooltip-title {
        font-weight: bold;
        color: #3498db;
        margin-bottom: 6px;
        font-size: 1rem;
    }
    
    .tooltip-info {
        margin: 3px 0;
        font-size: 0.85rem;
    }
    
    .tooltip-info strong {
        color: #ecf0f1;
    }
    
    .modal-info strong {
        color: var(--text-muted);
    }
    
    .calendar-event span {
        display: block;
        line-height: 1.2;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
        text-decoration: none;
    }
    
    .calendar-event .grupo {
        font-size: 1em;
        opacity: 0.85;
    }
    
    .calendar-title {
        text-align: center;
        color: var(--primary-color);
        font-size: 2rem;
        margin-top: 2rem;
        margin-bottom: 0.2rem;
    }
    
    .calendar-month {
        text-align: center;
        color: var(--text-muted);
        font-size: 1.8rem;
        margin-bottom: 2rem;
        font-weight: 600;
    }
    
    /* Estilos para el formulario de asistencia */
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
        color: #007a1a;
        font-weight: bold;
        margin-bottom: 1.5rem;
        font-size: 18px;
        padding: 1rem;
        background: #d4edda;
        border-radius: 8px;
    }
    
    .volver-btn {
        display: inline-block;
        margin-bottom: 2rem;
        background: #e6f0fa;
        color: #034C8C;
        border: none;
        border-radius: 10px;
        padding: 0.8rem 1.5rem;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.2s;
    }
    
    .volver-btn:hover {
        background: #cce4ff;
    }
    
    .asistencia-titulo {
        text-align: center;
        color: #034C8C;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    /* Estilos para pestañas estilo Google Chrome */
    .chrome-tabs {
        display: flex;
        background: #f1f3f4;
        border-bottom: 1px solid #dadce0;
        border-radius: 8px 8px 0 0;
        padding: 0;
        margin: 0;
        position: relative;
    }
    
    .chrome-tab {
        background: #f8f9fa;
        border: 1px solid #dadce0;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        padding: 18px 36px;
        margin-right: 3px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 500;
        color: #5f6368;
        transition: all 0.2s ease;
        position: relative;
        min-width: 180px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .chrome-tab:hover {
        background: #f1f3f4;
        color: #202124;
    }
    
    .chrome-tab.active {
        background: #fff;
        color: #202124;
        border-color: #dadce0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        z-index: 1;
    }
    
    .chrome-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background: #fff;
    }
    
    .chrome-tab-content {
        display: none;
        background: #fff;
        border: 1px solid #dadce0;
        border-top: none;
        border-radius: 0 0 8px 8px;
        padding: 36px;
        min-height: 600px;
        overflow-x: auto;
    }
    
    .chrome-tab-content.active {
        display: block;
    }
    
    .tab-container {
        margin-top: 3rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        overflow: hidden;
    }

    /* Estilos para el buscador moderno */
    .search-container {
        max-width: 1000px;
        margin: 2rem auto 1rem auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(3,76,140,0.08);
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
    }

    .search-wrapper {
        display: flex;
        align-items: center;
        background: #f6f8fa;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 0.5rem 1rem;
        flex-grow: 1;
        position: relative;
        max-width: 800px;
    }

    .search-icon {
        font-size: 1.5rem;
        color: #666;
        margin-right: 0.5rem;
    }

    .search-input {
        border: none;
        background: transparent;
        padding: 0.8rem 0.5rem 0.8rem 0;
        font-size: 1.1rem;
        width: 100%;
        outline: none;
        min-height: 20px;
    }

    .search-filters {
        display: flex;
        gap: 1rem;
        margin-left: 1rem;
    }

    .filter-select {
        padding: 0.6rem 1rem;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 0.9rem;
        color: #333;
        background: #f6f8fa;
        cursor: pointer;
        outline: none;
    }
    
    .clear-search-btn {
        padding: 0.6rem 1rem;
        border-radius: 8px;
        border: 1px solid #ccc;
        background: #f6f8fa;
        color: #666;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    
    .clear-search-btn:hover {
        background: #e9ecef;
        color: #333;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 100;
        max-height: 300px;
        overflow-y: auto;
        display: none;
    }

    .search-results.active {
        display: block;
    }

    .search-result-item {
        padding: 0.8rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.95rem;
        color: #333;
    }

    .search-result-item:hover {
        background: #f0f0f0;
    }

    .search-result-item.no-results {
        padding: 0.8rem 1rem;
        text-align: center;
        color: #666;
    }
    
    .search-result-header {
        padding: 0.8rem 1rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        font-weight: bold;
        color: #333;
        font-size: 0.9rem;
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
    
    /* Estilos adicionales para asegurar que los colores de los selects se mantengan */
    .asistencia-form select.asistió,
    .asistencia-form select.faltó,
    .asistencia-form select.tarde,
    .asistencia-form select.justificado {
        transition: all 0.3s ease !important;
    }
    
    /* Estilos más específicos para forzar los colores */
    .asistencia-form select[class*="asistió"] {
        background-color: #d4edda !important;
        color: #155724 !important;
        border-color: #c3e6cb !important;
    }
    
    .asistencia-form select[class*="faltó"] {
        background-color: #f8d7da !important;
        color: #721c24 !important;
        border-color: #f5c6cb !important;
    }
    
    .asistencia-form select[class*="tarde"] {
        background-color: #fff3cd !important;
        color: #856404 !important;
        border-color: #ffeaa7 !important;
    }
    
    .asistencia-form select[class*="justificado"] {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
        border-color: #bee5eb !important;
    }
    
    /* Animaciones */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    /* ===== RESPONSIVE DESIGN ===== */
    
    /* Tablets (768px - 1024px) */
    @media (max-width: 1024px) {
        .search-container {
            margin: 1.5rem auto 1rem auto;
            padding: 1rem 1.5rem;
        }
        
        .search-wrapper {
            flex-direction: column;
            gap: 1rem;
            max-width: 600px;
        }
        
        .search-filters {
            margin-left: 0;
            width: 100%;
            justify-content: space-between;
        }
        
        .filter-select {
            flex: 1;
            margin: 0 0.5rem;
        }
        
        .asistencia-search-wrapper {
            max-width: 600px;
        }
        
        .dashboard-wrapper {
            padding: 1.5rem 0.5rem;
            margin: 0 0.5rem;
        }
        
        .calendar-grid {
            min-width: 900px;
            grid-template-columns: 80px repeat(7, 1fr);
            grid-template-rows: 60px repeat(12, 100px);
        }
        
        .calendar-header-cell {
            font-size: 1rem;
            height: 60px;
        }
        
        .calendar-header-cell .fecha {
            font-size: 1.1em;
        }
        
        .calendar-hour {
            font-size: 0.9rem;
            padding: 6px 8px 0 0;
        }
        
        .calendar-event {
            padding: 10px 14px;
            font-size: 1rem;
            min-height: 50px;
            max-height: 100px;
        }
        
        .calendar-event.superpuesto {
            min-height: 25px;
            max-height: 40px;
            padding: 3px 6px;
            font-size: 0.75rem;
        }
        
        .calendar-event.superpuesto:nth-child(2) {
            top: 45px;
        }
        
        .calendar-event.superpuesto:nth-child(3) {
            top: 85px;
        }
        
        .calendar-event.superpuesto:nth-child(4) {
            top: 125px;
        }
        
        .calendar-title {
            font-size: 1.8rem;
        }
        
        .calendar-month {
            font-size: 1.6rem;
        }
        
        .asistencia-form {
            margin: 1.5rem 0.5rem;
            padding: 2rem;
            border-radius: 12px;
        }
        
        .asistencia-form table {
            font-size: 0.9rem;
        }
        
        .asistencia-form th, .asistencia-form td {
            padding: 0.8rem 0.6rem;
        }
        
        .asistencia-form select {
            padding: 0.5rem 0.8rem;
            font-size: 0.9rem;
        }
        
        .asistencia-form button {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
        }
        
        .asistencia-titulo {
            font-size: 1.4rem;
        }
        
        .volver-btn {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }
        
        /* Asegurar que los colores se mantengan en versiones responsive */
        .asistencia-form select.asistió {
            background-color: #d4edda !important;
            color: #155724 !important;
            border-color: #c3e6cb !important;
        }
        .asistencia-form select.faltó {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            border-color: #f5c6cb !important;
        }
        .asistencia-form select.tarde {
            background-color: #fff3cd !important;
            color: #856404 !important;
            border-color: #ffeaa7 !important;
        }
        .asistencia-form select.justificado {
            background-color: #d1ecf1 !important;
            color: #0c5460 !important;
            border-color: #bee5eb !important;
        }
    }
    
    /* Mobile (480px - 768px) */
    @media (max-width: 768px) {
        .search-container {
            margin: 1rem auto 0.5rem auto;
            padding: 0.8rem 1rem;
            border-radius: 12px;
        }
        
        .search-wrapper {
            padding: 0.4rem 0.8rem;
            gap: 0.8rem;
        }
        
        .search-input {
            font-size: 0.9rem;
        }
        
        .search-filters {
            gap: 0.6rem;
        }
        
        .filter-select {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }
        
        .asistencia-search-wrapper {
            padding: 0.8rem 1.2rem;
        }
        
        .asistencia-search-input {
            font-size: 1rem;
        }
        
        .dashboard-wrapper {
            padding: 1rem 0.3rem;
            margin: 0 0.3rem;
            border-radius: 12px;
        }
        
        .calendar-grid {
            min-width: 700px;
            grid-template-columns: 70px repeat(7, 1fr);
            grid-template-rows: 50px repeat(12, 80px);
        }
        
        .calendar-header-cell {
            font-size: 0.9rem;
            height: 50px;
        }
        
        .calendar-header-cell .dia {
            font-size: 0.8em;
        }
        
        .calendar-header-cell .fecha {
            font-size: 1em;
        }
        
        .calendar-hour {
            font-size: 0.8rem;
            padding: 4px 6px 0 0;
        }
        
        .calendar-event {
            padding: 8px 10px;
            font-size: 0.9rem;
            min-height: 40px;
            max-height: 80px;
            left: 5px;
            right: 5px;
        }
        
        .calendar-event.superpuesto {
            min-height: 20px;
            max-height: 35px;
            padding: 2px 4px;
            font-size: 0.7rem;
            left: 5px;
            right: 5px;
        }
        
        .calendar-event.superpuesto:nth-child(1) {
            top: 3px;
        }
        
        .calendar-event.superpuesto:nth-child(2) {
            top: 38px;
        }
        
        .calendar-event.superpuesto:nth-child(3) {
            top: 73px;
        }
        
        .calendar-event.superpuesto:nth-child(4) {
            top: 108px;
        }
        
        .calendar-title {
            font-size: 1.5rem;
            margin-top: 1rem;
        }
        
        .calendar-month {
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        
        .asistencia-form {
            margin: 1rem 0.5rem;
            padding: 1rem;
            border-radius: 12px;
        }
        
        .asistencia-form table {
            font-size: 0.8rem;
        }
        
        .asistencia-form th, .asistencia-form td {
            padding: 0.4rem 0.2rem;
        }
        
        .asistencia-form select {
            padding: 0.2rem 0.4rem;
            font-size: 0.8rem;
        }
        
        .asistencia-form button {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }
        
        .asistencia-titulo {
            font-size: 1.3rem;
        }
        
        .volver-btn {
            padding: 0.4rem 1rem;
            font-size: 0.9rem;
        }
        
        /* Asegurar que los colores se mantengan en versiones responsive */
        .asistencia-form select.asistió {
            background-color: #d4edda !important;
            color: #155724 !important;
            border-color: #c3e6cb !important;
        }
        .asistencia-form select.faltó {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            border-color: #f5c6cb !important;
        }
        .asistencia-form select.tarde {
            background-color: #fff3cd !important;
            color: #856404 !important;
            border-color: #ffeaa7 !important;
        }
        .asistencia-form select.justificado {
            background-color: #d1ecf1 !important;
            color: #0c5460 !important;
            border-color: #bee5eb !important;
        }
    }
    
    /* Mobile Small (320px - 480px) */
    @media (max-width: 480px) {
        .search-container {
            margin: 0.8rem auto 0.4rem auto;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
        }
        
        .search-wrapper {
            gap: 0.6rem;
        }
        
        .search-input {
            font-size: 0.8rem;
        }
        
        .search-filters {
            gap: 0.4rem;
        }
        
        .filter-select {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
        }
        
        .asistencia-search-wrapper {
            padding: 0.6rem 1rem;
        }
        
        .asistencia-search-input {
            font-size: 0.9rem;
        }
        
        .dashboard-wrapper {
            padding: 0.5rem 0.1rem;
            margin: 0 0.1rem;
            border-radius: 8px;
        }
        
        .calendar-grid {
            min-width: 600px;
            grid-template-columns: 60px repeat(7, 1fr);
            grid-template-rows: 45px repeat(12, 70px);
        }
        
        .calendar-header-cell {
            font-size: 0.8rem;
            height: 45px;
        }
        
        .calendar-header-cell .dia {
            font-size: 0.7em;
        }
        
        .calendar-header-cell .fecha {
            font-size: 0.9em;
        }
        
        .calendar-hour {
            font-size: 0.7rem;
            padding: 3px 4px 0 0;
        }
        
        .calendar-event {
            padding: 6px 8px;
            font-size: 0.8rem;
            min-height: 35px;
            max-height: 70px;
            left: 3px;
            right: 3px;
            border-radius: 6px;
        }
        
        .calendar-event.superpuesto {
            min-height: 18px;
            max-height: 30px;
            padding: 1px 3px;
            font-size: 0.65rem;
            left: 3px;
            right: 3px;
        }
        
        .calendar-event.superpuesto:nth-child(1) {
            top: 2px;
        }
        
        .calendar-event.superpuesto:nth-child(2) {
            top: 32px;
        }
        
        .calendar-event.superpuesto:nth-child(3) {
            top: 62px;
        }
        
        .calendar-event.superpuesto:nth-child(4) {
            top: 92px;
        }
        
        .calendar-title {
            font-size: 1.3rem;
            margin-top: 0.8rem;
        }
        
        .calendar-month {
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
        }
        
        .asistencia-form {
            margin: 0.8rem 0.25rem;
            padding: 0.8rem;
            border-radius: 8px;
        }
        
        .asistencia-form table {
            font-size: 0.75rem;
        }
        
        .asistencia-form th, .asistencia-form td {
            padding: 0.3rem 0.15rem;
        }
        
        .asistencia-form select {
            padding: 0.15rem 0.3rem;
            font-size: 0.75rem;
        }
        
        .asistencia-form button {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
        
        .asistencia-titulo {
            font-size: 1.1rem;
        }
        
        .volver-btn {
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
        }
        
        .event-tooltip {
            max-width: 250px;
            padding: 10px 12px;
            font-size: 0.8rem;
        }
        
        .tooltip-title {
            font-size: 0.9rem;
        }
        
        .tooltip-info {
            font-size: 0.75rem;
        }
        
        /* Asegurar que los colores se mantengan en versiones responsive */
        .asistencia-form select.asistió {
            background-color: #d4edda !important;
            color: #155724 !important;
            border-color: #c3e6cb !important;
        }
        .asistencia-form select.faltó {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            border-color: #f5c6cb !important;
        }
        .asistencia-form select.tarde {
            background-color: #fff3cd !important;
            color: #856404 !important;
            border-color: #ffeaa7 !important;
        }
        .asistencia-form select.justificado {
            background-color: #d1ecf1 !important;
            color: #0c5460 !important;
            border-color: #bee5eb !important;
        }
    }
    
    /* Landscape orientation adjustments */
    @media (max-height: 600px) and (orientation: landscape) {
        .calendar-grid {
            grid-template-rows: 60px repeat(12, 90px);
        }
        
        .calendar-event {
            min-height: 45px;
            max-height: 90px;
        }
        
        .calendar-event.superpuesto {
            min-height: 22px;
            max-height: 40px;
        }
    }
</style> 