<?php
class Database {

    private $host  = DB_HOST;
    private $username  = DB_USER;
    private $password   = DB_PASSWORD;
    private $db_name  = DB_NAME;

    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }
        return $this->conn;
    }
 
    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }
        
        return $stmt->execute();
    }

    public function update($table, $data, $condition) {
        $columns = '';
        foreach ($data as $key => $value) {
            $columns .= "$key = :$key, ";
        }
        $columns = rtrim($columns, ', ');

        $conditionStr = '';
        foreach ($condition as $key => $value) {
            $conditionStr .= "$key = :$key AND ";
        }
        $conditionStr = rtrim($conditionStr, ' AND ');

        $query = "UPDATE $table SET $columns WHERE $conditionStr";

        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }
        
        foreach ($condition as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        return $stmt->execute();
    }

    public function delete($table, $condition) {
        $conditionStr = '';
        foreach ($condition as $key => $value) {
            $conditionStr .= "$key = :$key AND ";
        }
        $conditionStr = rtrim($conditionStr, ' AND ');

        $query = "DELETE FROM $table WHERE $conditionStr";

        $stmt = $this->conn->prepare($query);

        foreach ($condition as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        return $stmt->execute();
    }
}
?>
