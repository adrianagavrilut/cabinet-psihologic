<?php

class Intrebare
{
    public $id;
    public $id_medic;
    public $id_test;
    public $continut;
    public $creare;

    function __construct($row)
    {
        $this->id = $row['id'];
        $this->id_medic = array_key_exists('id_medic', $row) ? $row['id_medic'] : null;
        $this->id_test = array_key_exists('id_test', $row) ? $row['id_test'] : null;
        $this->continut = $row['continut'];
        $this->creare = $row['creare'];
    }

    static function getAll($medicUserId, $idTest)
    {
        $mysqli = createConnection();
        $sql = "SELECT i.* FROM Intrebare AS i JOIN Medic as m on m.id=i.id_medic WHERE m.id_utilizator=$medicUserId AND i.id_test=$idTest";
        $result = $mysqli->query($sql);
        $intrebari = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $intrebare = new Intrebare($row);
                $intrebari[] = $intrebare;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $intrebari;
    }

    static function getAllForTest($idTest)
    {
        $mysqli = createConnection();
        $sql = "SELECT i.* FROM Intrebare AS i WHERE i.id_test=$idTest";
        $result = $mysqli->query($sql);
        $intrebari = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $intrebare = new Intrebare($row);
                $intrebari[] = $intrebare;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $intrebari;
    }

    static function get($medicUserId, $idTest, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            throw new Exception('Invalid ID');
        }
        $mysqli = createConnection();
        $sql = "SELECT i.* FROM Intrebare as i JOIN Medic as m on m.id=i.id_medic WHERE m.id_utilizator=$medicUserId AND i.id=$id AND i.id_test=$idTest";
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $intrebare = null;
        if ($row = $result->fetch_assoc()) {
            $intrebare = new Intrebare($row);
        } else {
            throw new Exception('Resource not found');
        }
        $mysqli->close();
        return $intrebare;
    }

    static function create($medicUserId, $idTest, $continut)
    {
        $mysqli = createConnection();

        $errors = [];
        if (empty($continut)) {
            $errors['continut'] = 'Acest camp este obligatoriu';
        }
        if (!empty($errors)) {
            return ['intrebare' => $_POST, 'errors' => $errors];
        }
        $medic = Medic::getForUserId($medicUserId);

        $sql = "INSERT INTO Intrebare (id_medic, id_test, continut, creare) VALUES ('" . $medic->id . "', '" . $idTest . "', '" . $mysqli->real_escape_string($continut) . "', NOW())";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['intrebare' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }

    static function update($medicUserId, $idTest, $id, $continut)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception('Invalid ID');
        }
        $existingIntrebare = self::get($medicUserId, $idTest, $id);
        if (!$existingIntrebare) {
            return ['intrebare' => $_POST, 'errors' => ['id' => 'Record not found']];
        }

        $mysqli = createConnection();
        $errors = [];
        if (empty($continut)) {
            $errors['continut'] = 'Acest camp este obligatoriu';
        }

        if (!empty($errors)) {
            return ['intrebare' => $_POST, 'errors' => $errors];
        }
        $sql = "UPDATE Intrebare SET continut='" . $mysqli->real_escape_string($continut) . "' WHERE Intrebare.id = $id";
        if ($mysqli->query($sql) !== TRUE) {
            return ['intrebare' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
        return true;
    }

    static function delete($medicUserId, $idTest, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ['errors' => ['id' => 'Invalid ID']];
        }
        $existingTest = self::get($medicUserId, $idTest, $id);
        if (!$existingTest) {
            return ['errors' => ['id' => 'Record not found']];
        }
        $mysqli = createConnection();

        $sql = "DELETE FROM Intrebare WHERE id = $id";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }
}
