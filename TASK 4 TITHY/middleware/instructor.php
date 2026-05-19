<?php

require 'middleware/auth.php';

if($_SESSION['role'] != 'instructor') {

    die('Access Denied');
}
?>