<?php

class PacientTest
{
    public $id;
    public $id_pacient;
    public $id_test;

    function __construct($row)
    {
        $this->id = $row['id'];
        $this->id_pacient = array_key_exists('id_pacient', $row) ? $row['id_pacient'] : null;
        $this->id_test = array_key_exists('id_test', $row) ? $row['id_test'] : null;
    }

    static function getAllTestIdsForPacient($idPacient)
    {
        $mysqli = createConnection();
        $sql = "SELECT pt.id_test FROM PacientTest as pt WHERE pt.id_pacient=$idPacient";
        $result = $mysqli->query($sql);
        $items = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row['id_test'];
            }
        }
        $mysqli->close();
        return $items;
    }

    static function create($idPacient, $idTest)
    {
        $mysqli = createConnection();
        $sql = "INSERT INTO PacientTest (id_pacient, id_test) VALUES ($idPacient, $idTest)";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        }
        $mysqli->close();
    }

    static function delete($id)
    {
        $mysqli = createConnection();

        $sql = "DELETE FROM PacientTest WHERE id = $id";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        }
        $mysqli->close();
    }

    static function deleteAllForPacient($idPacient)
    {
        $mysqli = createConnection();

        $sql = "DELETE FROM PacientTest WHERE id_pacient = $idPacient";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        }
        $mysqli->close();
    }
}
