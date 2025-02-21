<section class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mt-5 p-3">
                    <div class="card-body">
                        <h2 class="card-title text-center">Login</h2>
                        <form action="<?= BASE_URL ?>functions/user_auth.php" method="post" onsubmit="return usedEmail(event, this)">
                            <input type="hidden" name="action" value="login">
                            <div class="mb-3">
                                <label for="email" class="form-label">correo</label>
                                <input type="email" id="correo" name="email" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <?php
                                if (isset($_SESSION['error_message'])) {
                                    echo '<input type="password" id="password" name="password" class="form-control is-invalid" required>';
                                    echo '<div class="invalid-feedback">' . $_SESSION['error_message'] . '</div>';
                                    unset($_SESSION['error_message']); 
                                }else{
                                    echo '<input type="password" id="password" name="password" class="form-control" required>';
                                }
                            ?>
                                
                            </div>
                          
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                          <!--   <div class="mt-3 d-flex justify-content-between align-items-end">
                                <a class="primary" href="page/registro.php">Registrate</a>
                            </div> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>