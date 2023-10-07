<?php

class Articol
{
    public $id;
    public $titlu;
    public $continut;
    public $imagine;
    public $categorie;
    public $publicare;

    function __construct($row)
    {
        $this->id = $row['id'];
        $this->titlu = $row['titlu'];
        $this->continut = $row['continut'];
        $this->imagine = $row['imagine'];
        $this->categorie = $row['categorie'];
        $this->publicare = $row['publicare'];
    }

    static function getAll()
    {
        $mysqli = createConnection();
        $sql = "SELECT * FROM Articol;";
        $result = $mysqli->query($sql);
        $articole = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $articol = new Articol($row);
                $articole[] = $articol;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $articole;
    }

    static function get($articolId)
    {
        if (!is_numeric($articolId) || $articolId <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            throw new Exception('Invalid ID');
        }
        $mysqli = createConnection();
        $sql = "SELECT a.* FROM Articol AS a WHERE a.id=$articolId;";
        $result = $mysqli->query($sql);
        $articol = null;
        if ($row = $result->fetch_assoc()) {
            $articol = new Articol($row);
        } else {
            throw new Exception('Resource not found');
        }
        $mysqli->close();
        return $articol;
    }

    static function create($titlu, $continut, $imagine, $categorie)
    {
        $mysqli = createConnection();
        $errors = [];
        if (empty($titlu)) {
            $errors['titlu'] = 'Acest camp este obligatoriu';
        }
        if (empty($continut)) {
            $errors['continut'] = 'Acest camp este obligatoriu';
        }
        if (!empty($errors)) {
            return ['articol' => $_POST, 'errors' => $errors];
        }
        $sql = "INSERT INTO Articol (titlu, continut, imagine, categorie, publicare) VALUES ('" . $titlu . "', '" . $mysqli->real_escape_string($continut) . "', '" . $imagine . "', '" . $categorie . "', NOW())";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['articol' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }

    static function update($articolId, $titlu, $continut, $imagine, $categorie)
    {
        if (!is_numeric($articolId) || $articolId <= 0) {
            throw new Exception('Invalid ID');
        }
        $existingArticol = self::get($articolId);
        if (!$existingArticol) {
            return ['articol' => $_POST, 'errors' => ['id' => 'Record not found']];
        }
        $mysqli = createConnection();
        $errors = [];
        if (empty($titlu)) {
            $errors['titlu'] = 'Acest camp este obligatoriu';
        }
        if (empty($continut)) {
            $errors['continut'] = 'Acest camp este obligatoriu';
        }
        if (!empty($errors)) {
            return ['articol' => $_POST, 'errors' => $errors];
        }
        if ($imagine) {
            $sql = "UPDATE Articol SET titlu='$titlu', continut='" . $mysqli->real_escape_string($continut) . "', imagine='$imagine', categorie='$categorie' WHERE Articol.id = $articolId";
        } else {
            $sql = "UPDATE Articol SET titlu='$titlu', continut='" . $mysqli->real_escape_string($continut) . "', categorie='$categorie' WHERE Articol.id = $articolId";
        }
        if ($mysqli->query($sql) !== TRUE) {
            return ['articol' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
        return true;
    }

    static function delete($articolId)
    {
        if (!is_numeric($articolId) || $articolId <= 0) {
            return ['errors' => ['id' => 'Invalid ID']];
        }
        $existingArticol = self::get($articolId);
        if (!$existingArticol) {
            return ['errors' => ['id' => 'Record not found']];
        }
        $mysqli = createConnection();
        $sql = "DELETE FROM Articol WHERE id = $articolId";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'titlu' => $this->titlu,
            'continut' => $this->continut,
            'imagine' => $this->imagine,
            'categorie' => $this->categorie,
            'publicare' => $this->publicare
        ];
    }

    static function searchByTitle($searchQuery)
    {
        $mysqli = createConnection();
        $searchQuery = $mysqli->real_escape_string($searchQuery);
        $sql = "SELECT * FROM Articol WHERE titlu LIKE '%$searchQuery%'";
        $result = $mysqli->query($sql);
        $filteredArticles = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $articol = new Articol($row);
                $filteredArticles[] = $articol;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $filteredArticles;
    }

    static function getNrArticole()
    {
        $mysqli = createConnection();
        $sql = "SELECT COUNT(*) as numar_articole FROM Articol";
        $result = $mysqli->query($sql);
        $row = mysqli_fetch_assoc($result);
        $nrArticole = $row['numar_articole'];
        $mysqli->close();
        return $nrArticole;
    }

    static function uploadImage($file)
    {
        $targetDir = realpath(__DIR__ . '/../assets/img/blog/');
        $filename = substr(md5(mt_rand()), 0, 7) . '-' . basename(($file['name']));
        $targetFile = $targetDir . '/' . $filename;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        // Check file size
        if ($file['size'] > 500000) {
            return false;
        }
        // Allow only certain file formats
        if ($imageFileType !== 'jpg' && $imageFileType !== 'jpeg' && $imageFileType !== 'png' && $imageFileType !== 'gif' && $imageFileType !== 'svg') {
            return false;
        }
        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $filename;
        } else {
            return false;
        }
    }
}
