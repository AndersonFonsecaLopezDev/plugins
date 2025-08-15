<?php
// search-styles.php - Estilos CSS para el buscador moderno
?>
<style>
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
        height: 38px;
        font-size: 0.9rem;
        padding: 0 12px;
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
