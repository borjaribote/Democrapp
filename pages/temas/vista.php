<?php
require_once '../../includes/header.php';
accesoAutorizado("admin");
require_once BASE_PATH.'/controllers/controlador_temas.php';
require_once BASE_PATH.'/functions/gestion_mensajes.php';
//Verificar cookie para mantener la misma pestaña activa
$activeTab = isset($_COOKIE["temas_tab"]) ? json_decode($_COOKIE["temas_tab"], true)["tab"] : "nueva"; // Por defecto "nueva"
?>

<section class="container my-5">
    <h2 class="text-center">Administrar Temas</h2>

    <!-- Menú de pestañas -->
    <ul class="nav nav-tabs" id="temasTabs">
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'nueva') ? 'active' : '' ?>" id="nueva-tab" data-bs-toggle="tab" href="#nueva"><i class="fa-solid fa-check"></i> Aprobar temas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'actualizar') ? 'active' : '' ?>" id="actualizar-tab" data-bs-toggle="tab" href="#actualizar"><i class="fa-solid fa-arrows-rotate"></i> Actualizar temas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'clasificados') ? 'active' : '' ?>" id="clasificados-tab" data-bs-toggle="tab" href="#clasificados"><i class="fa-solid fa-trophy"></i> Temas clasificados</a>
        </li>
    </ul>


    <!-- Contenido de las pestañas -->
    <div class="tab-content mt-4">
        <!-- 🔹 Pestaña: Aprobar Temas -->
        <div class="tab-pane fade <?= ($activeTab == 'nueva') ? 'active show' : '' ?>" id="nueva">
            <?php include BASE_PATH . "pages/temas/aprobar.php"; ?> 
        </div>

        <!-- 🔹 Pestaña: Actualizar Temas -->
        <div class="tab-pane fade <?= ($activeTab == 'actualizar') ? 'active show' : '' ?>" id="actualizar">
            <?php include BASE_PATH.'pages/temas/actualizar.php'; ?> 
        </div>

        <!-- 🔹 Pestaña: Temas clasificados -->
        <div class="tab-pane fade <?= ($activeTab == 'clasificados') ? 'active show' : '' ?>" id="clasificados">
            <?php include BASE_PATH.'pages/temas/clasificados.php'; ?> 
        </div>
    </div>
</section>
<?php
 require_once BASE_PATH.'/includes/footer.php'; ?>