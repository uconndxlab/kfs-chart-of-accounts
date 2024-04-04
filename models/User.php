<?php

class User {
    public $db;
    public $id;
    public $netid;
    public $name;
    public $email;
    public $price_group;

    public function __construct($db, $id, $netid, $name, $email, $price_group) {
        $this->db = $db;
        $this->id = $id;
        $this->netid = $netid;
        $this->name = $name;
        $this->email = $email;
        $this->price_group = $price_group;
    }

    public function save() {
        if ($this->id) {
            $stmt = $this->db->prepare('UPDATE users SET netid = :netid, name = :name, email = :email, price_group = :price_group WHERE id = :id');
            $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        } else {
            $stmt = $this->db->prepare('INSERT INTO users (netid, name, email, price_group) VALUES (:netid, :name, :email, :price_group)');
        }
        $stmt->bindValue(':netid', $this->netid, SQLITE3_TEXT);
        $stmt->bindValue(':name', $this->name, SQLITE3_TEXT);
        $stmt->bindValue(':email', $this->email, SQLITE3_TEXT);
        $stmt->bindValue(':price_group', $this->price_group, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function delete() {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public static function find($db, $id) {
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray();
        return new User($db, $row['id'], $row['netid'], $row['name'], $row['email'], $row['price_group']);
    }

    public static function all($db) {
        $results = $db->query('SELECT * FROM users');
        $users = [];
        while ($row = $results->fetchArray()) {
            $users[] = new User($db, $row['id'], $row['netid'], $row['name'], $row['email'], $row['price_group']);
        }
        return $users;
    }


    public function accounts() {
        $results = $this->db->query('SELECT * FROM authorized_users WHERE netid = ' . $this->netid);
        $accounts = [];
        while ($row = $results->fetchArray()) {
            $accounts[] = Account::find($this->db, $row['account_id']);
        }
        return $accounts;
    }
}