<?php

class Document
{
    public $id;
    public $id_utilizator_medic;
    public $id_utilizator_pacient;
    public $fisier;
    public $numeFisier;

    function __construct($row)
    {
        $this->id = $row['id'];
        $this->id_utilizator_medic = $row['id_utilizator_medic'];
        $this->id_utilizator_pacient = $row['id_utilizator_pacient'];
        $this->fisier = $row['fisier'];
        $this->numeFisier = $row['numeFisier'];
    }

    static function getDocuments($medicUserId, $pacientUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT d.* FROM Document as d WHERE d.id_utilizator_medic=$medicUserId AND d.id_utilizator_pacient=$pacientUserId";
        $result = $mysqli->query($sql);
        $documents = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $document = new Document($row);
                $documents[] = $document;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $documents;
    }

    static function get($medicUserId, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            throw new Exception('Invalid ID');
        }
        $mysqli = createConnection();
        $sql = "SELECT d.* FROM Document as d WHERE d.id_utilizator_medic=$medicUserId AND d.id=$id";
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null;
        }
        $doc = null;
        if ($row = $result->fetch_assoc()) {
            $doc = new Document($row);
        } else {
            throw new Exception('Resource not found');
        }
        $mysqli->close();
        return $doc;
    }

    static function getDocumentsForPacient($pacientUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT d.* FROM Document as d WHERE d.id_utilizator_pacient=$pacientUserId";
        $result = $mysqli->query($sql);
        $documents = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $document = new Document($row);
                $documents[] = $document;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $documents;
    }

    static function uploadFile($filename, $tempName)
    {
        $targetDir = realpath(__DIR__ . '/../assets/img/portofoliu/');
        $filename = substr(md5(mt_rand()), 0, 7) . '-' . basename(($filename));
        $targetFile = $targetDir . '/' . $filename;
        if (move_uploaded_file($tempName, $targetFile)) {
            return $filename;
        } else {
            return false;
        }
    }

    static function insertFileForMedic($medicUserId, $pacientUserId, $file, $fileName)
    {
        $mysqli = createConnection();
        $sql = "INSERT INTO Document (id_utilizator_medic, id_utilizator_pacient, fisier, numeFisier) VALUES ('" . $medicUserId . "', '" . $pacientUserId . "', '" . $file . "', '" . $fileName . "')";
        if ($mysqli->query($sql) !== TRUE) {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
    }

    static function insertFileForPacient($pacientUserId, $file, $fileName)
    {
        $medicUserId = Pacient::getMedicUserIdForPacient($pacientUserId);
        $mysqli = createConnection();
        $sql = "INSERT INTO Document (id_utilizator_medic, id_utilizator_pacient, fisier, numeFisier) VALUES ('" . $medicUserId . "', '" . $pacientUserId . "', '" . $file . "', '" . $fileName . "')";
        if ($mysqli->query($sql) !== TRUE) {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
    }

    static function delete($medicUserId, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ['errors' => ['id' => 'Invalid ID']];
        }
        $existing = self::get($medicUserId, $id);
        if (!$existing) {
            return ['errors' => ['id' => 'Record not found']];
        }
        $mysqli = createConnection();
        $sql = "DELETE FROM Document WHERE Document.id = $id";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }
}
