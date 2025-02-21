<?php
require_once '../../includes/header.php';
accesoAutorizado("admin");
require_once BASE_PATH.'/controllers/controlador_temas.php';
require_once BASE_PATH.'/functions/gestion_mensajes.php';
//Verificar cookie para mantener la misma pestaña activa
$activeTab = isset($_COOKIE["temas_tab"]) ? json_decode($_COOKIE["temas_tab"], true)["tab"] : "aprobar"; // Por defecto "aprobar"
?>

<section class="container my-5">
    <h2 class="text-center">Administrar Temas</h2>

    <!-- Menú de pestañas -->
    <ul class="nav nav-tabs" id="temasTabs">
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'aprobar') ? 'active' : '' ?>" id="aprobar-tab" data-bs-toggle="tab" href="#aprobar"><i class="fa-solid fa-check"></i> Aprobar temas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'aprobados') ? 'active' : '' ?>" id="aprobados-tab" data-bs-toggle="tab" href="#aprobados"><i class="fa fa-thumbs-up" aria-hidden="true"></i>            </i> Temas aprobados</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'clasificados') ? 'active' : '' ?>" id="clasificados-tab" data-bs-toggle="tab" href="#clasificados"><i class="fa-solid fa-trophy"></i> Temas clasificados</a>
        </li>
    </ul>


    <!-- Contenido de las pestañas -->
    <div class="tab-content mt-4">
        <!-- 🔹 Pestaña: Aprobar Temas -->
        <div class="tab-pane fade <?= ($activeTab == 'aprobar') ? 'active show' : '' ?>" id="aprobar">
            <?php include BASE_PATH . "pages/temas/aprobar.php"; ?> 
        </div>

        <!-- 🔹 Pestaña: aprobados Temas -->
        <div class="tab-pane fade <?= ($activeTab == 'aprobados') ? 'active show' : '' ?>" id="aprobados">
            <?php include BASE_PATH.'pages/temas/aprobados.php'; ?> 
        </div>

        <!-- 🔹 Pestaña: Temas clasificados -->
        <div class="tab-pane fade <?= ($activeTab == 'clasificados') ? 'active show' : '' ?>" id="clasificados">
            <?php include BASE_PATH.'pages/temas/clasificados.php'; ?> 
        </div>
    </div>
</section>
<?php
 require_once BASE_PATH.'/includes/footer.php'; ?>