<style>
    /* Estilos encapsulados para el Sidebar */
    .sidebar {
        width: 80px; /* Ancho encogido */
        background-color: #ffffff;
        border-right: 1px solid #e5e5e5;
        height: 100vh;
        position: fixed;
        display: flex;
        flex-direction: column;
        transition: width 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
        overflow: hidden;
        white-space: nowrap;
        z-index: 1000;
    }

    .sidebar:hover {
        width: 260px; /* Ancho expandido */
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        padding: 22px 26px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #000;
    }

    .sidebar-nav {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        padding: 10px 0;
    }

    .sidebar .nav-item {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #333;
        padding: 14px 28px;
        margin: 4px 12px;
        border-radius: 8px;
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }

    .sidebar .nav-item:hover {
        background-color: #f0f0f0;
        color: #007aff;
    }
    
    .sidebar .nav-item.active {
        background-color: #007aff;
        color: #fff;
    }
    
    .sidebar .nav-item.active:hover {
        color: #fff;
    }

    .sidebar-icon {
        display: inline-block;
        min-width: 24px;
        margin-right: 20px;
    }

    .sidebar-text {
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }

    .sidebar:hover .sidebar-text {
        opacity: 1;
    }

    .sidebar-footer {
        padding-bottom: 20px;
    }
</style>

<div class="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
        </span>
        <span class="sidebar-text">Inventario PC</span>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item active">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </span>
            <span class="sidebar-text">Dashboard</span>
        </a>
        <a href="comprar.php" class="nav-item">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </span>
            <span class="sidebar-text">Registrar Compra</span>
        </a>
        <a href="#" class="nav-item">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l.44-1.39a2 2 0 0 1 3.12 0l.44 1.39a8 8 0 0 1 5.66 5.66l1.39.44a2 2 0 0 1 0 3.12l-1.39.44a8 8 0 0 1-5.66 5.66l-.44 1.39a2 2 0 0 1-3.12 0l-.44-1.39a8 8 0 0 1-5.66-5.66l-1.39-.44a2 2 0 0 1 0-3.12l1.39-.44a8 8 0 0 1 5.66-5.66z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </span>
            <span class="sidebar-text">Gestionar Productos</span>
        </a>
         <a href="#" class="nav-item">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
            </span>
            <span class="sidebar-text">Reportes</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </span>
            <span class="sidebar-text">Cerrar Sesión</span>
        </a>
    </div>
</div>