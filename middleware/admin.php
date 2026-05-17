<?php

require 'middleware/auth.php';

if($_SESSION['role'] != 'admin') {

    die('Access Denied');
}
?>