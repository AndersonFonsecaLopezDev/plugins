<?php
// asistencia-styles.php - Estilos CSS para el dashboard de asistencia diaria
?>
<style>
    :root {
        --primary-color: #1a365d;
        --secondary-color: #2d3748;
        --accent-color: #3182ce;
        --success-color: #38a169;
        --warning-color: #d69e2e;
        --danger-color: #e53e3e;
        --bg-light: #f7fafc;
        --bg-cell: #ffffff;
        --bg-hover: #edf2f7;
        --text-main: #2d3748;
        --text-muted: #718096;
        --text-light: #a0aec0;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --today-circle: #3182ce;
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    body {
        font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--bg-light);
        margin: 0;
        padding: 0;
        color: var(--text-main);
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    .dashboard-wrapper {
        max-width: 2200px;
        margin: 0 auto;
        padding: 3rem 2rem;
        background: var(--bg-cell);
        border-radius: 24px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border-color);
        backdrop-filter: blur(10px);
    }
    

    .calendar-grid {
        display: grid;
        grid-template-columns: 90px repeat(7, 1fr);
        grid-template-rows: 70px repeat(12, 120px);
        gap: 2px;
        background: var(--border-color);
        border-radius: 16px;
        overflow: visible;
        margin: 0;
        min-width: 1100px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        position: relative;
        z-index: 1;
    }
    
    .calendar-header-cell {
        background: linear-gradient(135deg, #034C8C 0%, #0456a3 100%);
        color: #fff;
        text-align: center;
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        font-size: 1.1rem;
        position: sticky;
        top: 0;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 70px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
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
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border-radius: 50%;
        padding: 4px 12px;
        font-weight: 600;
        font-size: 0.9em;
        margin-top: 4px;
        display: inline-block;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: var(--shadow-sm);
    }
    
    .calendar-header-cell .dia {
        font-size: 0.5em;
        letter-spacing: 1px;
    }
    
    .calendar-hour {
        background: var(--bg-light);
        color: var(--text-muted);
        text-align: right;
        padding: 12px 16px 0 0;
        font-size: 0.9rem;
        font-weight: 500;
        border-right: 1px solid var(--border-color);
        position: sticky;
        left: 0;
        z-index: 1;
        backdrop-filter: blur(5px);
    }
    
    .calendar-cell {
        background: var(--bg-cell);
        min-height: 120px;
        border: 1px solid var(--border-color);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    
    .calendar-cell:hover {
        background: var(--bg-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    
    .calendar-event {
        position: absolute;
        left: 10px; right: 10px;
        top: 10px;
        min-height: 50px;
        max-height: 100px;
        background: var(--bg-cell);
        color: var(--text-main);
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        padding: 16px 20px;
        font-size: 1rem;
        font-weight: 500;
        display: flex;
        flex-direction: column;
        justify-content: center;
        cursor: pointer;
        border: 2px solid var(--accent-color);
        transition: all 0.3s ease;
        overflow: hidden;
        word-break: break-word;
        white-space: normal;
        text-decoration: none;
        backdrop-filter: blur(5px);
    }
    
    .calendar-event:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-color);
        background: var(--bg-hover);
    }
    
    /* Estilos para eventos superpuestos en columna */
    .calendar-event.superpuesto {
        width: calc(100% - 20px);
        min-height: 60px;
        max-height: 90px;
        padding: 4px 8px;
        font-size: 0.5rem;
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
    
    /* Estilos para pestañas estilo Google Chrome */
    .chrome-tabs {
        display: flex;
        background: var(--bg-light);
        border-bottom: 2px solid var(--border-color);
        border-radius: 16px 16px 0 0;
        padding: 0;
        margin: 0;
        position: relative;
        box-shadow: var(--shadow-sm);
    }
    
    .chrome-tab {
        background: var(--bg-light);
        border: 2px solid var(--border-color);
        border-bottom: none;
        border-radius: 16px 16px 0 0;
        padding: 20px 40px;
        margin-right: 4px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 600;
        color: var(--text-muted);
        transition: all 0.3s ease;
        position: relative;
        min-width: 200px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
    }
    
    .chrome-tab:hover {
        background: var(--bg-hover);
        color: var(--text-main);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    
    .chrome-tab.active {
        background: var(--bg-cell);
        color: var(--text-main);
        border-color: var(--accent-color);
        box-shadow: var(--shadow-md);
        z-index: 1;
    }
    
    .chrome-tab.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--accent-color);
        border-radius: 2px;
    }
    
    .chrome-tab-content {
        display: none;
        background: var(--bg-cell);
        border: 2px solid var(--border-color);
        border-top: none;
        border-radius: 0 0 16px 16px;
        padding: 40px;
        min-height: 600px;
        overflow-x: auto;
        box-shadow: var(--shadow-sm);
    }
    
    .chrome-tab-content.active {
        display: block;
    }
    
    /* Estilos para botones de submit */
    button[type="submit"] {
        background: var(--success-color);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
        backdrop-filter: blur(5px);
        margin-top: 1rem;
    }
    
    button[type="submit"]:hover {
        background: #2f855a;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    button[type="submit"]:active {
        transform: translateY(0);
        box-shadow: var(--shadow-sm);
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
            transform: scale(1.05);
        }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .tab-container {
        margin-top: 1rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        overflow: hidden;
    }

    /* Estilos para el buscador moderno */
    .search-container {
        max-width: 50%;
        margin: 1.5rem auto 1.5rem auto;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        position: relative;
        overflow: visible;
        z-index: 10000;
    }

    .search-wrapper {
        display: flex;
        align-items: center;
        background: var(--bg-cell);
        border: 2px solid var(--border-color);
        border-radius: 20px;
        padding: 1.8rem 2.2rem;
        flex-grow: 1;
        position: relative;
        max-width: 900px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        overflow: visible;
        z-index: 10001;
        box-shadow: var(--shadow-lg);
    }
    
    .search-wrapper:hover {
        box-shadow: var(--shadow-lg), 0 0 0 1px var(--accent-color);
        transform: translateY(-1px);
    }
    
    .search-wrapper:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        background: var(--bg-cell);
    }

    .search-icon {
        font-size: 1rem;
        color: var(--text-muted);
        margin-right: 1rem;
        transition: all 0.3s ease;
    }
    
    .search-wrapper:focus-within .search-icon {
        color: var(--accent-color);
        transform: scale(1.1);
    }

    .search-input {
        border: none;
        background: transparent;
        padding: 0.8rem 0.5rem 0.8rem 0;
        font-size: 0.8rem;
        font-weight: 500;
        width: 100%;
        outline: none;
        min-height: 20px;
        color: var(--text-main);
        transition: all 0.3s ease;
    }
    
    .search-input::placeholder {
        color: var(--text-light);
        font-weight: 400;
    }

    .search-filters {
        display: flex;
        gap: 0.8rem;
        margin-left: 0.8rem;
    }

    .filter-select {
        border-radius: 10px;
        border: 2px solid var(--border-color);
        font-size: 0.5rem;
        font-weight: 500;
        color: var(--text-main);
        background: var(--bg-light);
        cursor: pointer;
        outline: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }
    
    .filter-select:hover {
        border-color: var(--accent-color);
        background: var(--bg-cell);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    
    .filter-select:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
    }
    
    .clear-search-btn {
        padding: 0.8rem 1.5rem;
        border-radius: 10px;
        border: 2px solid var(--border-color);
        background: var(--bg-light);
        color: var(--text-muted);
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }
    
    .clear-search-btn:hover {
        background: var(--danger-color);
        color: white;
        border-color: var(--danger-color);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .search-results {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: var(--bg-cell);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        z-index: 9999;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        backdrop-filter: blur(10px);
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .search-results.active {
        display: block !important;
        opacity: 1 !important;
        transform: translateY(0) !important;
        visibility: visible !important;
    }
    
    /* Estilos normales para search-results */
    .search-results {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: var(--bg-cell);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        z-index: 9999;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        backdrop-filter: blur(10px);
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .search-result-item {
        padding: 1rem 1.5rem;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-main);
        transition: all 0.2s ease;
    }

    .search-result-item:hover {
        background: var(--bg-hover);
        transform: translateX(4px);
        border-left: 3px solid var(--accent-color);
    }

    .search-result-item.no-results {
        padding: 1rem 1.5rem;
        text-align: center;
        color: var(--text-muted);
        font-style: italic;
    }
    
    .search-result-header {
        padding: 1rem 1.5rem;
        background: var(--bg-light);
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
        color: var(--text-main);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
    
    /* ESTILOS PARA EL HISTÓRICO DE ASISTENCIAS */
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
    
    .estudiante-search-container {
        max-width: 800px;
        margin: 0 auto 2rem auto;
        position: relative;
    }
    
    .estudiante-search-wrapper {
        display: flex;
        gap: 1rem;
        align-items: center;
        background: var(--bg-cell);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }
    
    .estudiante-search-wrapper:hover {
        border-color: var(--accent-color);
        box-shadow: var(--shadow-md);
    }
    
    .estudiante-search-wrapper:focus-within {
        border-color: var(--accent-color);
        box-shadow: var(--shadow-md);
    }
    
    .estudiante-search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 1rem;
        background: transparent;
        color: var(--text-main);
    }
    
    .estudiante-search-input::placeholder {
        color: var(--text-light);
    }
    
    .estudiante-search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--bg-cell);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: var(--shadow-lg);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    
    .estudiante-search-results.active {
        display: block;
    }
    
    .estudiante-result {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    .estudiante-result:hover {
        background: var(--bg-hover);
    }
    
    .estudiante-result:last-child {
        border-bottom: none;
    }
    
    .estudiante-result-nombre {
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.25rem;
    }
    
    .estudiante-result-info {
        font-size: 0.9rem;
        color: var(--text-muted);
    }
    
    .no-results {
        padding: 1rem;
        text-align: center;
        color: var(--text-muted);
        font-style: italic;
    }
    
    .historico-results {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .estudiante-info {
        background: var(--bg-light);
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        text-align: center;
        border: 1px solid var(--border-color);
    }
    
    .estudiante-info h3 {
        margin: 0 0 0.5rem 0;
        color: var(--text-main);
        font-size: 1.5rem;
    }
    
    .estudiante-info p {
        margin: 0;
        color: var(--text-muted);
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
    
    /* Responsive para el histórico */
    @media (max-width: 768px) {
        .estudiante-search-wrapper {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .historico-table {
            font-size: 0.9rem;
        }
        
        .historico-table th,
        .historico-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .estado-select {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }
        
        .btn-guardar {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
    }
    
    @media (max-width: 480px) {
        .historico-title {
            font-size: 1.3rem;
        }
        
        .historico-subtitle {
            font-size: 0.9rem;
        }
        
        .estudiante-search-wrapper {
            padding: 0.75rem 1rem;
        }
        
        .estudiante-search-input {
            font-size: 0.9rem;
        }
        
        .historico-table {
            font-size: 0.8rem;
        }
        
        .historico-table th,
        .historico-table td {
            padding: 0.5rem 0.25rem;
        }
        
        .estudiante-info {
            padding: 1rem;
        }
        
        .estudiante-info h3 {
            font-size: 1.3rem;
        }
        
        .estudiante-info p {
            font-size: 0.9rem;
        }
    }

    /* ===== AJUSTES PARA ESCRITORIO Y LAPTOP ===== */
    @media (max-width: 1400px) {
        .dashboard-wrapper {
            max-width: 98vw;
            padding: 1.5rem 0.7rem;
            border-radius: 16px;
        }
        .calendar-grid {
            min-width: 900px;
            grid-template-columns: 70px repeat(7, 1fr);
            grid-template-rows: 50px repeat(12, 90px);
        }
        .calendar-header-cell {
            font-size: 0.95rem;
            height: 50px;
            padding: 0.5rem 0.2rem;
        }
        .calendar-hour {
            font-size: 0.85rem;
            padding: 4px 6px 0 0;
        }
        .calendar-event {
            padding: 8px 10px;
            font-size: 0.95rem;
            min-height: 40px;
            max-height: 80px;
        }
        .chrome-tabs {
            border-radius: 10px 10px 0 0;
        }
        .chrome-tab {
            padding: 12px 18px;
            font-size: 1rem;
            min-width: 120px;
            border-radius: 10px 10px 0 0;
        }
        .chrome-tab-content {
            padding: 20px;
            min-height: 350px;
            border-radius: 0 0 10px 10px;
        }
        .asistencia-form {
            max-width: 700px;
            padding: 1.5rem 1rem 1rem 1rem;
            border-radius: 12px;
        }
        .asistencia-form th, .asistencia-form td {
            padding: 0.7rem 0.5rem;
            font-size: 15px;
        }
        .asistencia-form select {
            padding: 0.4rem 0.7rem;
            font-size: 15px;
        }
        .asistencia-form button {
            padding: 0.7rem 1.2rem;
            font-size: 1rem;
            border-radius: 8px;
        }
        .asistencia-titulo {
            font-size: 1.3rem;
        }
        .asistencia-search-container, .asistencia-search-wrapper {
            max-width: 600px;
            padding: 1rem 1rem;
            border-radius: 10px;
        }
        .asistencia-search-input {
            font-size: 1rem;
            padding: 0.7rem 0.5rem 0.7rem 0;
        }
    }
</style> 