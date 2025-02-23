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
        <li class="nav-item">
            <a class="nav-link <?= ($activeTab == 'borrar') ? 'active' : '' ?>" id="borrar-tab" data-bs-toggle="tab" href="#borrar"><i class="fa-solid fa-trash-can"></i> Borrar temas</a>
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
        <!-- 🔹 Pestaña: Borrar todos los temas  -->
        <div class="tab-pane fade <?= ($activeTab == 'borrar') ? 'active show' : '' ?>" id="borrar">
            <div class="tab-pane fade <?= ($activeTab == 'borrar') ? 'active show' : '' ?>" id="borrar">
                <?php include BASE_PATH.'pages/temas/borrar.php'; ?> 
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="viewTopicInfo" tabindex="-1" aria-labelledby="temaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom-0 pb-2">
                <h5 class="modal-title fw-semibold text-dark" id="temaModalLabel">Detalles del Tema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Título</h6>
                    <p class="fw-semibold text-dark" id="topicTitle"></p>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Fecha de Creación</h6>
                    <p class="text-dark" id="topicCreatedAt"></p>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Tema</h6>
                    <p class="text-dark" id="topicTopic"></p>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Descripción</h6>
                    <p class="text-dark" id="topicDescription"></p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-dark px-4 py-2 rounded-pill" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?php
 require_once BASE_PATH.'/includes/footer.php'; ?>