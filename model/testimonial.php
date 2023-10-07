<?php

class Testimonial
{
    public $id;
    public $titlu;
    public $continut;
    public $rating;
    public $nume;
    public $despre;
    public $imagine;

    function __construct($row)
    {
        $this->id = $row['id'];
        $this->titlu = $row['titlu'];
        $this->continut = $row['continut'];
        $this->rating = $row['rating'];
        $this->nume = $row['nume'];
        $this->despre = $row['despre'];
        $this->imagine = $row['imagine'];
    }

    static function getAll()
    {
        $mysqli = createConnection();
        $sql = "SELECT * FROM Testimonial;";
        $result = $mysqli->query($sql);
        $testimoniale = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $testimonial = new Testimonial($row);
                $testimoniale[] = $testimonial;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $testimoniale;
    }

    static function get($reviewId)
    {
        if (!is_numeric($reviewId) || $reviewId <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            throw new Exception('Invalid ID');
        }
        $mysqli = createConnection();
        $sql = "SELECT t.* FROM Testimonial AS t WHERE t.id=$reviewId;";
        $result = $mysqli->query($sql);
        $testimonial = null;
        if ($row = $result->fetch_assoc()) {
            $testimonial = new Testimonial($row);
        } else {
            throw new Exception('Resource not found');
        }
        $mysqli->close();
        return $testimonial;
    }

    static function create($titlu, $continut, $rating, $nume, $despre, $imagine)
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
            return ['test' => $_POST, 'errors' => $errors];
        }
        $sql = "INSERT INTO Testimonial (titlu, continut, rating, nume, despre, imagine) VALUES ('" . $titlu . "', '" . $mysqli->real_escape_string($continut) . "', '" . $rating . "', '" . $nume . "', '" . $despre . "', '" . $imagine . "')";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['test' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }

    static function update($reviewId, $titlu, $continut, $rating, $nume, $despre, $imagine)
    {
        if (!is_numeric($reviewId) || $reviewId <= 0) {
            throw new Exception('Invalid ID');
        }
        $existing = self::get($reviewId);
        if (!$existing) {
            return ['testimonial' => $_POST, 'errors' => ['id' => 'Record not found']];
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
            return ['testimonial' => $_POST, 'errors' => $errors];
        }
        if ($imagine) {
            $sql = "UPDATE Testimonial SET titlu='$titlu', rating='$rating', continut='" . $mysqli->real_escape_string($continut) . "', imagine='$imagine', nume='$nume', despre='$despre' WHERE Testimonial.id = $reviewId";
        } else {
            $sql = "UPDATE Testimonial SET titlu='$titlu', rating='$rating', continut='" . $mysqli->real_escape_string($continut) . "', nume='$nume', despre='$despre' WHERE Testimonial.id = $reviewId";
        }
        if ($mysqli->query($sql) !== TRUE) {
            return ['testimonial' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
        return true;
    }

    static function delete($reviewId)
    {
        if (!is_numeric($reviewId) || $reviewId <= 0) {
            return ['errors' => ['id' => 'Invalid ID']];
        }
        $existingTestim = self::get($reviewId);
        if (!$existingTestim) {
            return ['errors' => ['id' => 'Record not found']];
        }
        $mysqli = createConnection();
        $sql = "DELETE FROM Testimonial WHERE id = $reviewId";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }

    static function getNrTestimoniale()
    {
        $mysqli = createConnection();
        $sql = "SELECT COUNT(*) as numar_testimoniale FROM Testimonial";
        $result = $mysqli->query($sql);
        $row = mysqli_fetch_assoc($result);
        $nrTestimoiale = $row['numar_testimoniale'];
        $mysqli->close();
        return $nrTestimoiale;
    }

    static function uploadImage($file)
    {
        $targetDir = realpath(__DIR__ . '/../assets/img/testimoniale/');
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
