<?php
// ============================================
// DINELOCAL · models/Menu.php
// Menu model — CRUD operations (MVC: Model)
// ITC 6355 | Arjun & Ayomide
// ============================================
require_once __DIR__ . '/../config/db.php';

class Menu {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Get all menu items, optionally filtered by category */
    public function getAll(string $category = ''): array {
        if ($category) {
            $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE category = ? ORDER BY name");
            $stmt->execute([$category]);
        } else {
            $stmt = $this->db->query("SELECT * FROM menu_items ORDER BY category, name");
        }
        return $stmt->fetchAll();
    }

    /** Get featured items for homepage */
    public function getFeatured(int $limit = 6): array {
        $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE is_featured = 1 AND is_available = 1 LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Get single item by ID */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Get all distinct categories */
    public function getCategories(): array {
        return $this->db->query("SELECT DISTINCT category FROM menu_items ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Create new menu item */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO menu_items (name, description, price, category, image_url, is_available, is_featured)
            VALUES (:name, :description, :price, :category, :image_url, :is_available, :is_featured)
        ");
        $stmt->execute([
            ':name'         => $data['name'],
            ':description'  => $data['description'],
            ':price'        => $data['price'],
            ':category'     => $data['category'],
            ':image_url'    => $data['image_url'] ?? null,
            ':is_available' => $data['is_available'] ?? 1,
            ':is_featured'  => $data['is_featured'] ?? 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update existing menu item */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE menu_items
            SET name=:name, description=:description, price=:price,
                category=:category, image_url=:image_url,
                is_available=:is_available, is_featured=:is_featured
            WHERE id=:id
        ");
        return $stmt->execute([
            ':id'           => $id,
            ':name'         => $data['name'],
            ':description'  => $data['description'],
            ':price'        => $data['price'],
            ':category'     => $data['category'],
            ':image_url'    => $data['image_url'] ?? null,
            ':is_available' => $data['is_available'] ?? 1,
            ':is_featured'  => $data['is_featured'] ?? 0,
        ]);
    }

    /** Delete menu item */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM menu_items WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Toggle availability */
    public function toggleAvailability(int $id): bool {
        $stmt = $this->db->prepare("UPDATE menu_items SET is_available = NOT is_available WHERE id = ?");
        return $stmt->execute([$id]);
    }
}