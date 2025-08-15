<?php
// tabs-styles.php - Estilos CSS para las pestañas estilo Chrome
?>
<style>
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
</style>
