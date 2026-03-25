<?php
// ============================================
// DINELOCAL · controllers/ReservationController.php
// Handles reservation form (MVC: Controller)
// ITC 6355 | Arjun & Ayomide
// ============================================
require_once __DIR__ . '/../models/Reservation.php';

class ReservationController {
    private Reservation $model;

    public function __construct() {
        $this->model = new Reservation();
    }

    /** Display reservation page */
    public function index(): void {
        $success = $_GET['success'] ?? false;
        $error   = $_GET['error']   ?? false;
        include __DIR__ . '/../views/reservations.php';
    }

    /** Handle form submission */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: reservations.php');
            exit;
        }

        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            // Store errors in session and redirect back
            session_start();
            $_SESSION['reservation_errors'] = $errors;
            $_SESSION['reservation_data']   = $_POST;
            header('Location: reservations.php?error=1');
            exit;
        }

        $data = [
            'full_name' => htmlspecialchars(trim($_POST['fullName'])),
            'email'     => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
            'guests'    => htmlspecialchars(trim($_POST['guests'])),
            'date'      => $_POST['date'],
            'time'      => htmlspecialchars(trim($_POST['time'])),
            'special'   => htmlspecialchars(trim($_POST['special'] ?? '')),
        ];

        try {
            $id = $this->model->create($data);
            // In production: send confirmation email here
            header("Location: reservations.php?success=1&id=$id");
            exit;
        } catch (Exception $e) {
            header('Location: reservations.php?error=db');
            exit;
        }
    }

    /** Validate POST data */
    private function validate(array $data): array {
        $errors = [];

        if (empty($data['fullName']) || strlen(trim($data['fullName'])) < 2) {
            $errors['fullName'] = 'Full name is required (min 2 characters).';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email address is required.';
        }
        if (empty($data['guests'])) {
            $errors['guests'] = 'Please select the number of guests.';
        }
        if (empty($data['date'])) {
            $errors['date'] = 'Please select a date.';
        } elseif (strtotime($data['date']) < strtotime('today')) {
            $errors['date'] = 'Date must be today or in the future.';
        }
        if (empty($data['time'])) {
            $errors['time'] = 'Please select a preferred time.';
        }

        return $errors;
    }
}