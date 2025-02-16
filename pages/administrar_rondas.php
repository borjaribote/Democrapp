<?php
require_once '../core/init.php';
accesoAutorizado("admin");
require_once  BASE_PATH.'/controladores/controlador_rondas.php';
require_once  BASE_PATH.'/controladores/controlador_temas.php';
require_once BASE_PATH.'/includes/header.php';

$temas = consultarTemasAprobados();
$activeTab = isset($_COOKIE["rondas_tab"]) ? json_decode($_COOKIE["rondas_tab"], true)["tab"] : "nueva"; 
?>

<section class="container my-5">
    <h2 class="text-center">Administrar Rondas</h2>

    <!-- Menú de pestañas -->
    <ul class="nav nav-tabs" id="rondasTabs">
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'nueva') ? 'active' : '' ?>" id="nueva-tab" data-bs-toggle="tab" href="#nueva"><i class="fa-solid fa-plus"></i> Crear Ronda</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'actualizar') ? 'active' : '' ?>" id="actualizar-tab" data-bs-toggle="tab" href="#actualizar"><i class="fa-solid fa-arrows-rotate"></i> Actualizar Rondas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'pasadas') ? 'active' : '' ?>" id="pasadas-tab" data-bs-toggle="tab" href="#pasadas"><i class="fa-regular fa-calendar"></i> Rondas Pasadas</a>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content mt-4">
        <!-- 🔹 Pestaña: Añadir Ronda -->
        <div class="tab-pane fade <?= ($activeTab == 'nueva') ? 'active show' : '' ?>" id="nueva">
            <?php include BASE_PATH.'pages/rondas/crear_ronda.php'; ?> 
        </div>

        <!-- 🔹 Pestaña: Actualizar Rondas -->
        <div class="tab-pane fade <?= ($activeTab == 'actualizar') ? 'active show' : '' ?>" id="actualizar">
            <?php include BASE_PATH.'pages/rondas/actualizar_rondas.php'; ?> 
        </div>

        <!-- 🔹 Pestaña: Rondas Pasadas -->
        <div class="tab-pane fade <?= ($activeTab == 'pasadas') ? 'active show' : '' ?>" id="pasadas">
            <?php include BASE_PATH.'pages/rondas/rondas_pasadas.php'; ?> 
        </div>
    </div>
</section>



<?php require_once BASE_PATH.'/includes/footer.php'; 

?>