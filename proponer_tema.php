<?php
require_once "conexion.php";
require_once BASE_PATH.'includes/header.php';

if (isset($_SESSION['user_id'])) {?>
<section class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <div class="card-body">
                        <h2 class="card-title text-center">Proponer Tema</h2>
                        <form action="acciones/temas.php" method="post">
                            <input type="hidden" name="action" value="insert">
                            <div class="mb-3">
                                <label for="topic" class="form-label">Tema:</label>
                                <input type="text" id="topic" name="topic" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Proponer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
   <?php 
    if (isset($_SESSION['new_topic'])) { ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 border-0">
                    <div class="card-body">
                        <div class="alert alert-success mt-3" role="alert">
                            Su tema <strong><?php echo $_SESSION['new_topic']; ?></strong> se ha registrado correctamente.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    unset($_SESSION['new_topic']);
    }
}else{
    header("Location: 404.php");
    exit();
}
 require_once BASE_PATH.'includes/footer.php'; ?>