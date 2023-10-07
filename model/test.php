<?php

class Test
{
    public $id;
    public $id_medic;
    public $denumire;
    public $descriere;

    function __construct($row)
    {
        $this->id = $row['id'];
        $this->id_medic = array_key_exists('id_medic', $row) ? $row['id_medic'] : null;
        $this->denumire = $row['denumire'];
        $this->descriere = $row['descriere'];
    }

    static function getAll($medicUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT t.* FROM Test as t JOIN Medic as m on m.id=t.id_medic WHERE m.id_utilizator=$medicUserId";
        $result = $mysqli->query($sql);
        $tests = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $test = new Test($row);
                $tests[] = $test;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $tests;
    }

    static function getNrForMedic($medicUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT COUNT(*) as nrTeste FROM Test JOIN Medic AS m ON m.id = Test.id_medic WHERE m.id_utilizator=$medicUserId";
        $result = $mysqli->query($sql);
        $row = mysqli_fetch_assoc($result);
        $nrTeste = $row['nrTeste'];
        $mysqli->close();
        return $nrTeste;
    }

    static function get($medicUserId, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            throw new Exception('Invalid ID');
        }
        $mysqli = createConnection();
        $sql = "SELECT t.* FROM Test as t JOIN Medic as m on m.id=t.id_medic WHERE m.id_utilizator=$medicUserId AND t.id=$id";
        $result = $mysqli->query($sql);
        if (!$result) {
            // Handle the error, for example:
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $test = null;
        if ($row = $result->fetch_assoc()) {
            $test = new Test($row);
        } else {
            throw new Exception('Resource not found');
        }
        $mysqli->close();
        return $test;
    }

    static function getAllForPacient($idPacient)
    {
        $mysqli = createConnection();
        $sql = "SELECT t.* FROM Test as t JOIN PacientTest as pt ON pt.id_test=t.id WHERE pt.id_pacient=$idPacient";
        $result = $mysqli->query($sql);
        $items = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $item = new Test($row);
                $items[] = $item;
            }
        }
        $mysqli->close();
        return $items;
    }

    static function getForPacient($idPacient, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            throw new Exception('Invalid ID');
        }
        $mysqli = createConnection();
        $sql = "SELECT t.* FROM Test as t JOIN PacientTest as pt on t.id=pt.id_test WHERE pt.id_pacient=$idPacient AND t.id=$id";
        $result = $mysqli->query($sql);
        if (!$result) {
            // Handle the error, for example:
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $test = null;
        if ($row = $result->fetch_assoc()) {
            $test = new Test($row);
        } else {
            throw new Exception('Resource not found');
        }
        $mysqli->close();
        return $test;
    }

    static function create($medicUserId, $denumire, $descriere)
    {
        $mysqli = createConnection();
        $errors = [];
        if (empty($denumire)) {
            $errors['denumire'] = 'Acest camp este obligatoriu';
        }
        if (!empty($errors)) {
            return ['test' => $_POST, 'errors' => $errors];
        }
        $medic = Medic::getForUserId($medicUserId);

        $sql = "INSERT INTO Test (denumire, descriere, id_medic) VALUES ('" . $denumire . "', '" . $mysqli->real_escape_string($descriere) . "', '" . $medic->id . "')";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['test' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }

    static function update($medicUserId, $id, $denumire, $descriere)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception('Invalid ID');
        }
        $existingTest = self::get($medicUserId, $id);
        if (!$existingTest) {
            return ['test' => $_POST, 'errors' => ['id' => 'Test record not found']];
        }

        $mysqli = createConnection();
        $errors = [];
        if (empty($denumire)) {
            $errors['denumire'] = 'Acest camp este obligatoriu';
        }

        if (!empty($errors)) {
            return ['test' => $_POST, 'errors' => $errors];
        }
        $sql = "UPDATE Test SET denumire='$denumire', descriere='" . $mysqli->real_escape_string($descriere) . "' WHERE Test.id = $id";
        if ($mysqli->query($sql) !== TRUE) {
            return ['test' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
        return true;
    }

    static function delete($medicUserId, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ['errors' => ['id' => 'Invalid ID']];
        }
        $existingTest = self::get($medicUserId, $id);
        if (!$existingTest) {
            return ['errors' => ['id' => 'Test record not found']];
        }
        $mysqli = createConnection();

        $sql = "DELETE FROM Test WHERE Test.id = $id";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }
}
