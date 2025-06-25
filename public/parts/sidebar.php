<div class="sidebar_container">
    <div class="sidebar_header">
        <span class="sidebar_icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
        </span>
        <span class="sidebar_text">Inventario</span>
    </div>
    <nav class="sidebar_nav">
        <a href="dashboard.php" class="sidebar_nav-item <?php echo ($active_page ?? '') === 'dashboard' ? 'active' : ''; ?>">
            <span class="sidebar_icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </span>
            <span class="sidebar_text">Dashboard</span>
        </a>
        <a href="comprar.php" class="sidebar_nav-item <?php echo ($active_page ?? '') === 'comprar' ? 'active' : ''; ?>">
            <span class="sidebar_icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </span>
            <span class="sidebar_text">Compras</span>
        </a>
        <a href="#" class="sidebar_nav-item">
            <span class="sidebar_icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l.44-1.39a2 2 0 0 1 3.12 0l.44 1.39a8 8 0 0 1 5.66 5.66l1.39.44a2 2 0 0 1 0 3.12l-1.39.44a8 8 0 0 1-5.66 5.66l-.44 1.39a2 2 0 0 1-3.12 0l-.44-1.39a8 8 0 0 1-5.66-5.66l-1.39-.44a2 2 0 0 1 0-3.12l1.39-.44a8 8 0 0 1 5.66-5.66z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </span>
            <span class="sidebar_text">Gestionar Productos</span>
        </a>
    </nav>
    <div class="sidebar_footer">
        <a href="logout.php" class="sidebar_nav-item sidebar_logout-item">
            <span class="sidebar_icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </span>
            <span class="sidebar_text">Cerrar Sesión</span>
        </a>
    </div>
</div>