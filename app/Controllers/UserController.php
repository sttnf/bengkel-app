<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        $users = $this->userModel->findAll();
        return $this->render('users/index', ['users' => $users]);
    }

    public function show($id) {
        $user = $this->userModel->findById($id);

        if (!$user) {
            return $this->render('404');
        }

        return $this->render('users/show', ['user' => $user]);
    }

    public function create() {
        return $this->render('users/create');
    }

    public function store() {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Simple validation
        if (empty($name) || empty($email) || empty($password)) {
            return $this->render('users/create', [
                'error' => 'All fields are required',
                'name' => $name,
                'email' => $email
            ]);
        }

        if ($this->userModel->findByEmail($email)) {
            return $this->render('users/create', [
                'error' => 'Email already exists',
                'name' => $name,
                'email' => $email
            ]);
        }

        $userId = $this->userModel->register($name, $email, $password);

        if ($userId) {
            $this->redirect('/users');
        } else {
            return $this->render('users/create', [
                'error' => 'Something went wrong',
                'name' => $name,
                'email' => $email
            ]);
        }
    }

    public function edit($id) {
        $user = $this->userModel->findById($id);

        if (!$user) {
            return $this->render('404');
        }

        return $this->render('users/edit', ['user' => $user]);
    }

    public function update($id) {
        $user = $this->userModel->findById($id);

        if (!$user) {
            return $this->render('404');
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';

        // Simple validation
        if (empty($name) || empty($email)) {
            return $this->render('users/edit', [
                'error' => 'Name and email are required',
                'user' => [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email
                ]
            ]);
        }

        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $id) {
            return $this->render('users/edit', [
                'error' => 'Email already exists',
                'user' => [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email
                ]
            ]);
        }

        $updated = $this->userModel->update($id, [
            'name' => $name,
            'email' => $email
        ]);

        if ($updated) {
            $this->redirect("/users/$id");
        } else {
            return $this->render('users/edit', [
                'error' => 'Something went wrong',
                'user' => [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email
                ]
            ]);
        }

        throw new \Exception('Invalid request method');
    }

    public function delete($id) {
        $user = $this->userModel->findById($id);

        if (!$user) {
            return $this->render('404');
        }

        $this->userModel->delete($id);
        $this->redirect('/users');
    }
}