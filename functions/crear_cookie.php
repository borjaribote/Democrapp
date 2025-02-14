<?php
require_once '../core/init.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tab"])) {
    $tab = $_POST["tab"];
    $page = $_POST["page"];
    $cookieData = json_encode(["tab" => $tab, "page" => $page]);

    setcookie($page."_tab", $cookieData, time() + (7 * 24 * 60 * 60), "/");
}
?>
