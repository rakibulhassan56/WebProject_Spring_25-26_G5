<?php

class DashboardController {

    public function student() {

        require 'middleware/student.php';

        require 'Views/dashboard/student.php';
    }

    public function instructor() {

        require 'middleware/instructor.php';

        require 'Views/dashboard/instructor.php';
    }

    public function admin() {

        require 'middleware/admin.php';

        require 'Views/dashboard/admin.php';
    }
}
?>
