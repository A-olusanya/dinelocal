<?php
// ============================================
// DINELOCAL · controllers/MenuController.php
// Handles menu page logic (MVC: Controller)
// ITC 6355 | Arjun & Ayomide
// ============================================
require_once __DIR__ . '/../models/Menu.php';

class MenuController {
    private Menu $model;

    public function __construct() {
        $this->model = new Menu();
    }

    /** Display menu page with optional category filter */
    public function index(): void {
        $category   = $_GET['category'] ?? '';
        $items      = $this->model->getAll($category);
        $categories = $this->model->getCategories();

        // Group items by category for display
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item['category']][] = $item;
        }

        include __DIR__ . '/../views/menu.php';
    }

    /** Return JSON for AJAX requests */
    public function getJSON(): void {
        header('Content-Type: application/json');
        $category = $_GET['category'] ?? '';
        echo json_encode($this->model->getAll($category));
    }
}