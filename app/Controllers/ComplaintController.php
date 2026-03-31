<?php
class ComplaintController {
    public function all() {
        return db()->query("SELECT * FROM complaints")->fetchAll();
    }
    public function byUser($user_id) {
        $stmt = db()->prepare("SELECT * FROM complaints WHERE user_id=?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}
