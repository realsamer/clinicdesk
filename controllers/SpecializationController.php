<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

class SpecializationController
{
    private SpecializationModel $specializations;

    public function __construct()
    {
        $this->specializations = new SpecializationModel();
    }

    public function index(): void
    {
        Auth::requireRole('admin');

        $items = $this->specializations->getAll();
        $editItem = null;
        $pageTitle = 'Specializations';

        require __DIR__ . '/../views/specializations/index.php';
    }

    public function edit(): void
    {
        Auth::requireRole('admin');

        $id = (int)($_GET['id'] ?? 0);
        $editItem = $this->specializations->findById($id);

        if (!$editItem) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $items = $this->specializations->getAll();
        $pageTitle = 'Edit Specialization';

        require __DIR__ . '/../views/specializations/index.php';
    }

    public function store(): void
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('page=specializations'));
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect(url('page=specializations'));
        }

        $name = sanitize($_POST['name'] ?? '');

        if ($name === '') {
            flash('danger', 'Specialization name is required.');
            redirect(url('page=specializations'));
        }

        if ($this->specializations->create($name)) {
            flash('success', 'Specialization added successfully.');
        } else {
            flash('danger', 'Could not add specialization. It may already exist.');
        }

        redirect(url('page=specializations'));
    }

    public function update(): void
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('page=specializations'));
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect(url('page=specializations'));
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');

        if ($id <= 0 || $name === '') {
            flash('danger', 'Please enter a valid specialization name.');
            redirect(url('page=specializations'));
        }

        if (!$this->specializations->findById($id)) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        if ($this->specializations->update($id, $name)) {
            flash('success', 'Specialization updated successfully.');
        } else {
            flash('danger', 'Could not update specialization. It may already exist.');
        }

        redirect(url('page=specializations'));
    }

    public function delete(): void
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('page=specializations'));
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect(url('page=specializations'));
        }

        $id = (int)($_POST['id'] ?? 0);

        if (!$this->specializations->findById($id)) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        if (!$this->specializations->isSafeToDelete($id)) {
            flash('danger', 'Cannot delete this specialization because doctors are using it.');
            redirect(url('page=specializations'));
        }

        if ($this->specializations->delete($id)) {
            flash('success', 'Specialization deleted successfully.');
        } else {
            flash('danger', 'Could not delete specialization.');
        }

        redirect(url('page=specializations'));
    }
}
