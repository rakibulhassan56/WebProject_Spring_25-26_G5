<?php

require_once 'models/user.php';

class AuthController {

    public function register() {

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $role = trim($_POST['role']);

            if(strlen($password) < 8) {

                echo "Password must be at least 8 characters";
                return;
            }

            $userModel = new User();

            $existingUser = $userModel->findByEmail($email);

            if($existingUser) {

                echo "Email already exists";
                return;
            }

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $userModel->create(
                $name,
                $email,
                $hashedPassword,
                $role
            );

            header('Location: index.php?url=login');
            exit();
        }

        require 'Views/auth/register.php';
    }

    public function login() {

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            $userModel = new User();

            $user = $userModel->findByEmail($email);

            if($user &&
               password_verify(
                    $password,
                    $user['password_hash']
               )) {

                if($user['is_active'] == 0) {

                    echo "Your account has been suspended.";
                    return;
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                if($user['role'] == 'student') {

                    header('Location: index.php?url=student-dashboard');
                }

                elseif($user['role'] == 'instructor') {

                    header('Location: index.php?url=instructor-dashboard');
                }

                else {

                    header('Location: index.php?url=admin-dashboard');
                }

                exit();
            }

            else {

                echo "Invalid Email or Password";
            }
        }

        require 'Views/auth/login.php';
    }

    public function logout() {

        session_destroy();

        header('Location: index.php?url=login');

        exit();
    }
}
?>
