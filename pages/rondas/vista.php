<?php
require_once '../../includes/header.php';
accesoAutorizado("admin");
require_once  BASE_PATH.'/controllers/controlador_rondas.php';
require_once  BASE_PATH.'/controllers/controlador_temas.php';
require_once BASE_PATH.'/functions/gestion_mensajes.php';
$temasAprobados = consultarTemasAprobados();
/* $temasClasificados = consultarTemasAprobados();
$temasEmpatados = consultarTemasAprobados(); */
$activeTab = isset($_COOKIE["rondas_tab"]) ? json_decode($_COOKIE["rondas_tab"], true)["tab"] : "gestionar"; 
?>

<section class="container my-5">
    <h2 class="text-center">Administrar Rondas</h2>

    <!-- Menú de pestañas -->
    <ul class="nav nav-tabs" id="rondasTabs">
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'gestionar') ? 'active' : '' ?>" id="gestionar-tab" data-bs-toggle="tab" href="#gestionar"><i class="fa fa-tasks" aria-hidden="true"></i>Gestionar Rondas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'nueva') ? 'active' : '' ?>" id="nueva-tab" data-bs-toggle="tab" href="#nueva"><i class="fa-solid fa-plus"></i> Crear Ronda</a>
        </li>        
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content mt-4">
        <!-- 🔹 Pestaña: gestionar Rondas -->
        <div class="tab-pane fade <?= ($activeTab == 'gestionar') ? 'active show' : '' ?>" id="gestionar">
            <?php include BASE_PATH.'pages/rondas/gestionar.php'; ?> 
        </div>
        <!-- 🔹 Pestaña: Añadir Ronda -->
        <div class="tab-pane fade <?= ($activeTab == 'nueva') ? 'active show' : '' ?>" id="nueva">
            <?php include BASE_PATH.'pages/rondas/crear.php'; ?> 
        </div>        
    </div>
</section>



<?php require_once BASE_PATH.'/includes/footer.php'; 

?>