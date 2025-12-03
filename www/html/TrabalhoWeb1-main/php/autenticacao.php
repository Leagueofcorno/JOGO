<?php
session_start();

if (isset($_SESSION["user_id"])) {
    $login = true;
    $user_id    = $_SESSION["user_id"];
    $user_name  = $_SESSION["user_name"] ?? null;
    $user_email = $_SESSION["user_email"] ?? null;
} else {
    $login = false;
}
?>
