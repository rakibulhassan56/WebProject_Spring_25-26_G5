<?php

require 'middleware/auth.php';

if($_SESSION['role'] != 'student') {

    die('Access Denied');
}
?>