<?php

class Raspuns
{
    public $id;
    public $id_pacient;
    public $id_intrebare;
    public $continut;
    public $creare;
    public $intrebare_continut;
    public $intrebare_test;

    function __construct($row)
    {
        $this->id = $row['id'];
        $this->id_pacient = array_key_exists('id_pacient', $row) ? $row['id_pacient'] : null;
        $this->id_intrebare = array_key_exists('id_intrebare', $row) ? $row['id_intrebare'] : null;
        $this->continut = $row['continut'];
        $this->creare = $row['creare'];
        $this->intrebare_continut = $row['intrebare_continut'];
        $this->intrebare_test = $row['intrebare_test'];
    }

    static function getAll($idPacient)
    {
        $mysqli = createConnection();
        $sql = "SELECT r.*, i.continut as intrebare_continut, i.id_test as intrebare_test FROM Raspuns AS r LEFT JOIN Intrebare as i on i.id=r.id_intrebare WHERE r.id_pacient=$idPacient";
        $result = $mysqli->query($sql);
        $raspunsuri = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $raspuns = new Raspuns($row);
                $raspunsuri[] = $raspuns;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $raspunsuri;
    }

    static function getAllForTest($idPacient, $idTest)
    {
        $mysqli = createConnection();
        $sql = "SELECT r.*, i.continut as intrebare_continut, i.id_test as intrebare_test FROM Raspuns AS r LEFT JOIN Intrebare as i on i.id=r.id_intrebare WHERE r.id_pacient=$idPacient AND i.id_test = $idTest";
        $result = $mysqli->query($sql);
        $raspunsuri = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $raspuns = new Raspuns($row);
                $raspunsuri[] = $raspuns;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $raspunsuri;
    }

    static function getAllForQuestion($idPacient, $idIntrebare)
    {
        $mysqli = createConnection();
        $sql = "SELECT r.* FROM Raspuns AS r JOIN Pacient as p on p.id=r.id_pacient JOIN Intrebare as i on i.id=r.id_intrebare WHERE r.id_pacient=$idPacient AND r.id_intrebare=$idIntrebare";
        $result = $mysqli->query($sql);
        $raspunsuri = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $raspuns = new Raspuns($row);
                $raspunsuri[] = $raspuns;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $raspunsuri;
    }

    static function create($idPacient, $idIntrebare, $continut)
    {
        $mysqli = createConnection();
        $errors = [];
        if (empty($continut)) {
            $errors['continut'] = 'Acest camp este obligatoriu';
        }
        if (!empty($errors)) {
            return ['raspuns' => $_POST, 'errors' => $errors];
        }

        $sql = "INSERT INTO Raspuns (id_pacient, id_intrebare, continut, creare) VALUES ('" . $idPacient . "','" . $idIntrebare . "','" . $mysqli->real_escape_string($continut) . "', NOW())";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['raspuns' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }

    static function update($id, $continut)
    {
        $mysqli = createConnection();
        $sql = "UPDATE Raspuns SET continut='" . $mysqli->real_escape_string($continut) . "' WHERE Raspuns.id = $id";
        if ($mysqli->query($sql) !== TRUE) {
            return ['raspuns' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
        return true;
    }

    static function checkAnswerExist($idPacient, $idIntrebare)
    {
        $mysqli = createConnection();
        $existAnswer = "SELECT r.* FROM Raspuns AS r WHERE r.id_pacient='$idPacient' AND r.id_intrebare='$idIntrebare'";
        $result = $mysqli->query($existAnswer);
        $raspuns = null;
        if ($row = $result->fetch_assoc()) {
            $raspuns = new Raspuns($row);
        }
        $mysqli->close();
        return $raspuns;
    }
}
