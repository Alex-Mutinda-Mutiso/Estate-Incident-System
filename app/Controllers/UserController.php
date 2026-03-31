<?php
class UserController {
    public function all() {
        return db()->query("SELECT * FROM users")->fetchAll();
    }
    public function find($id) {
        $stmt = db()->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
