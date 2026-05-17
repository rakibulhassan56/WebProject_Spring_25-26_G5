<?php

if(!isset($_SESSION['user_id'])) {

    header('Location: index.php?url=login');

    exit();
}
?>