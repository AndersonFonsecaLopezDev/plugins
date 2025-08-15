<?php
// dashboard-styles.php - Estilos CSS completos para el dashboard de asistencia diaria
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
    .calendar-cell.multiple-events {
        overflow: hidden;
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
    .calendar-event.superpuesto:hover {
        z-index: 20;
        transform: scale(1.05);
        box-shadow: 0 4px 16px rgba(3,76,140,0.20);
    }
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
    }
    @media (max-width: 1024px) {
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
    }
    @media (max-width: 768px) {
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
    }
    @media (max-width: 480px) {
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
    }
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
    
    .search-input,
.filter-select,
.clear-search-btn {
    height: 38px; /* misma altura para todos */
    font-size: 0.9rem; /* mismo tamaño de texto */
    padding: 0 12px; /* padding consistente */
    border-radius: 10px;
    border: 2px solid var(--border-color);
    background: var(--bg-light);
    outline: none;
    display: flex;
    align-items: center;
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
</style>
