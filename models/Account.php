<?php 

class Account {
    public $db;
    public $id;
    public $name;
    public $number;
    public $status;
    public $fiscalOfficer;
    public $accountManager;
    public $accountSupervisor;
    public $effective_date;
    public $expiration_date;

    public function __construct($db, $id =0, $name = '', $number = '', $status = '', $fiscalOfficer = '', $accountManager = '', $accountSupervisor = '', $effective_date= '', $expiration_date ='') {
        $this->db = $db;
       
        if ($id>0) {
            // populate the object from the database
            $stmt = $db->prepare('SELECT * FROM accounts WHERE id = :id');
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray();
            $this->id = $row['id'];

            $this->name = $row['name'];
            $this->number = $row['number'];
            $this->status = $row['status'];
            $this->fiscalOfficer = $row['fiscalOfficer'];
            $this->accountManager = $row['accountManager'];
            $this->accountSupervisor = $row['accountSupervisor'];
            $this->effective_date = $row['effective_date'];
            $this->expiration_date = $row['expiration_date'];

        } else {
            // create a new object
            $this->id = $id;
            $this->name = $name;
            $this->number = $number;
            $this->status = $status;
            $this->fiscalOfficer = $fiscalOfficer;
            $this->accountManager = $accountManager;
            $this->accountSupervisor = $accountSupervisor;
            $this->effective_date = $effective_date;
            $this->expiration_date = $expiration_date;
        }
    }

    public function save() {
        if ($this->id) {
            $stmt = $this->db->prepare('UPDATE accounts SET name = :name, number = :number, status = :status, 
            fiscalOfficer = :fiscalOfficer, accountManager = :accountManager, accountSupervisor = :accountSupervisor,
            effective_date = :effective_date, expiration_date = :expiration_date WHERE id = :id');
            $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        } else {
            $stmt = $this->db->prepare('INSERT INTO accounts (name, number, status,
            fiscalOfficer, accountManager, accountSupervisor,
            effective_date, expiration_date) VALUES (:name, :number, :status, :effective_date, :expiration_date)');
        }
        $stmt->bindValue(':name', $this->name, SQLITE3_TEXT);
        $stmt->bindValue(':number', $this->number, SQLITE3_TEXT);
        $stmt->bindValue(':status', $this->status, SQLITE3_TEXT);
        $stmt->bindValue(':fiscalOfficer', $this->fiscalOfficer, SQLITE3_TEXT);
        $stmt->bindValue(':accountManager', $this->accountManager, SQLITE3_TEXT);
        $stmt->bindValue(':accountSupervisor', $this->accountSupervisor, SQLITE3_TEXT);
        $stmt->bindValue(':effective_date', $this->effective_date, SQLITE3_TEXT);
        $stmt->bindValue(':expiration_date', $this->expiration_date, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function delete() {
        $stmt = $this->db->prepare('DELETE FROM accounts WHERE id = :id');
        $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public static function find($db, $id) {
        $stmt = $db->prepare('SELECT * FROM accounts WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray();
        return new Account($db, $row['id'], $row['name'], $row['number'], $row['status'], 
        $row['fiscalOfficer'], $row['accountManager'], $row['accountSupervisor'],
        $row['effective_date'], $row['expiration_date']);
    }

    public static function all($db) {
        $results = $db->query('SELECT * FROM accounts');
        $accounts = [];
        while ($row = $results->fetchArray()) {
            $accounts[] = new Account($db, $row['id'], $row['name'], $row['number'], $row['status'], 
            $row['fiscalOfficer'], $row['accountManager'], $row['accountSupervisor'],
            $row['effective_date'], $row['expiration_date']);
        }
        return $accounts;
    }

    public function authorized_users() {
        $stmt = $this->db->prepare('SELECT * FROM authorized_users WHERE account_id = :account_id');
        $stmt->bindValue(':account_id', $this->id, SQLITE3_INTEGER);
        $results = $stmt->execute();
        $users = [];
        while ($row = $results->fetchArray()) {
            $users[] = new AuthorizedUser($this->db, $row['id'], $row['account_id'], $row['netid'], $row['role']);
        }
        return $users;
    }

    public function add_authorized_user($netid, $role) {
        $stmt = $this->db->prepare('INSERT INTO authorized_users (account_id, netid, role) VALUES (:account_id, :netid, :role)');
        $stmt->bindValue(':account_id', $this->id, SQLITE3_INTEGER);
        $stmt->bindValue(':netid', $netid, SQLITE3_TEXT);
        $stmt->bindValue(':role', $role, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function remove_authorized_user($netid) {
        $stmt = $this->db->prepare('DELETE FROM authorized_users WHERE account_id = :account_id AND netid = :netid');
        $stmt->bindValue(':account_id', $this->id, SQLITE3_INTEGER);
        $stmt->bindValue(':netid', $netid, SQLITE3_TEXT);
        $stmt->execute();
    }

    public static function find_by_number($db, $number) {
        $stmt = $db->prepare('SELECT * FROM accounts WHERE number = :number');
        $stmt->bindValue(':number', $number, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray();
        $account_id = $row['id'];
        return new Account($db, $account_id);
    }

    public static function exists_by_number($db, $number) {
        $stmt = $db->prepare('SELECT * FROM accounts WHERE number = :number');
        $stmt->bindValue(':number', $number, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray();
        return $row ? true : false;
    }
}