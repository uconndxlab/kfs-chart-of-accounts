<?php

class AuthorizedUser {
    public $db;
    public $id;
    public $account_id;
    public $netid;
    public $role;

    public function __construct($db, $id, $account_id, $netid, $role) {
        $this->db = $db;
        $this->id = $id;
        $this->account_id = $account_id;
        $this->netid = $netid;
        $this->role = $role;
    }

    public function save() {
        if ($this->id) {
            $stmt = $this->db->prepare('UPDATE authorized_users SET account_id = :account_id, netid = :netid, role = :role WHERE id = :id');
            $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        } else {
            $stmt = $this->db->prepare('INSERT INTO authorized_users (account_id, netid, role) VALUES (:account_id, :netid, :role)');
        }
        $stmt->bindValue(':account_id', $this->account_id, SQLITE3_INTEGER);
        $stmt->bindValue(':netid', $this->netid, SQLITE3_TEXT);
        $stmt->bindValue(':role', $this->role, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function delete() {
        $stmt = $this->db->prepare('DELETE FROM authorized_users WHERE id = :id');
        $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public static function find($db, $id) {
        $stmt = $db->prepare('SELECT * FROM authorized_users WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray();
        return new AuthorizedUser($db, $row['id'], $row['account_id'], $row['netid'], $row['role']);
    }

    public static function all($db) {
        $results = $db->query('SELECT * FROM authorized_users');
        $users = [];
        while ($row = $results->fetchArray()) {
            $users[] = new AuthorizedUser($db, $row['id'], $row['account_id'], $row['netid'], $row['role']);
        }
        return $users;
    }

    public function account() {
        return Account::find($this->db, $this->account_id);
    }

    public function user() {
        return User::find($this->db, $this->netid);
    }

    public function role() {
        return $this->role;
    }
}