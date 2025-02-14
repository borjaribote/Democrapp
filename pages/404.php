<?php
require_once '../core/init.php';
require_once BASE_PATH.'/includes/header.php';
?>

    <div id="error_page" class="container my-5">
        <div class="row justify-content-center text-center">
            <div class="col-md-8">
            <h1>404 - Page Not Found</h1>
            <div class="image">
                <img src="<?= BASE_URL ?>assets/img/cartman.png" alt="404 Image" class="img-fluid">
            </div>
            <p class="mt-5"><strong>Hey pringao! Fuera de aqui, yo mando en este territorio.</strong></p>
            <p><a href="index.php" class="home-link">Volver a inicio</a></p>
            </div>
        </div>
    </div>
<?php require_once BASE_PATH.'/includes/footer.php'; ?>
