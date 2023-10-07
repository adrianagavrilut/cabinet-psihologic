<?php

class Medic
{
    public $id;
    public $id_utilizator;
    public $nume;
    public $specialitate;
    public $data_nasterii;
    public $telefon;
    public $adresa;
    public $imagine;
    public $angajare;
    public $email;

    function __construct($row)
    {
        if ($row) {
            $this->id = $row['id'];
            $this->nume = $row['nume'];
            $this->specialitate = $row['specialitate'];
            $this->data_nasterii = $row['data_nasterii'];
            $this->telefon = $row['telefon'];
            $this->adresa = $row['adresa'];
            $this->imagine = $row['imagine'];
            $this->angajare = $row['creare'];
            $this->id_utilizator = $row['id_utilizator'];
            $this->email = array_key_exists('email', $row) ? $row['email'] : '';
        }
    }

    static function getAll()
    {
        $mysqli = createConnection();
        $sql = "SELECT m.*, u.email as email, u.nume as nume FROM Medic as m JOIN Utilizator as u ON u.id=m.id_utilizator";
        $result = $mysqli->query($sql);
        $doctors = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $medic = new Medic($row);
                $doctors[] = $medic;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $doctors;
    }

    static function getForUserId($userId)
    {
        $mysqli = createConnection();
        $sql = "SELECT m.*, u.email as email, u.nume as nume FROM Medic as m JOIN Utilizator as u ON u.id=m.id_utilizator WHERE m.id_utilizator = $userId";
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $medic = null;
        if ($row = $result->fetch_assoc()) {
            $medic = new Medic($row);
        }
        $mysqli->close();
        return $medic;
    }

    static function get($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            throw new Exception('Invalid Medic ID');
        }
        $mysqli = createConnection();
        $sql = "SELECT m.*, u.email as email, u.nume as nume FROM Medic as m JOIN Utilizator as u ON u.id=m.id_utilizator WHERE m.id = $id";
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $medic = null;
        if ($row = $result->fetch_assoc()) {
            $medic = new Medic($row);
        } else {
            throw new Exception('Resource not found');
        }
        $mysqli->close();
        return $medic;
    }

    static function create($email, $nume, $specialitate, $data_nasterii, $telefon, $adresa, $imagine)
    {
        $mysqli = createConnection();
        $errors = [];
        if (empty($email)) {
            $errors['email'] = 'Acest camp este obligatoriu';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Emailul nu este valid';
        }
        if (empty($nume)) {
            $errors['nume'] = 'Acest camp este obligatoriu';
        }
        if (empty($data_nasterii)) {
            $errors['data_nasterii'] = 'Acest camp este obligatoriu';
        }

        $sql = "SELECT * FROM Utilizator WHERE email='" . $email . "';";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            $errors['email'] = 'Emailul este deja folosit';
        }

        if (!empty($errors)) {
            return ['doctor' => $_POST, 'errors' => $errors];
        }

        $userId = self::saveUser($email, $nume);

        $sql = "INSERT INTO Medic (id_utilizator, specialitate, data_nasterii, telefon, adresa, imagine, creare) VALUES ('" . $userId . "', '" . $specialitate . "', '" . $data_nasterii . "', '" . $telefon . "', '" . $adresa . "', '" . $imagine . "', NOW())";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['doctor' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }

    static function update($id, $email, $nume, $specialitate, $data_nasterii, $telefon, $adresa, $imagine)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception('Invalid Medic ID');
        }

        $existingMedic = self::get($id);
        if (!$existingMedic) {
            return ['doctor' => $_POST, 'errors' => ['id' => 'Medic record not found']];
        }

        $mysqli = createConnection();
        $errors = [];
        if (empty($email)) {
            $errors['email'] = 'Acest camp este obligatoriu';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Emailul nu este valid';
        }
        if (empty($nume)) {
            $errors['nume'] = 'Acest camp este obligatoriu';
        }
        if (empty($data_nasterii)) {
            $errors['data_nasterii'] = 'Acest camp este obligatoriu';
        }

        if ($existingMedic->email != $email) {
            $sql = "SELECT * FROM Utilizator WHERE email='" . $email . "';";
            $result = $mysqli->query($sql);
            if ($result->num_rows > 0) {
                $errors['email'] = 'Emailul este deja folosit';
            }
        }

        if (!empty($errors)) {
            return ['doctor' => $_POST, 'errors' => $errors];
        }
        if ($existingMedic->email != $email) {
            //salvarea utilizatorului si stergerea utilizatorului vechi
            $oldUserId = $existingMedic->id_utilizator;
            $userId = self::saveUser($email, $nume);
            $deleteUserResult = self::deleteOldUser($oldUserId);
            if ($deleteUserResult !== true) {
                return ['pacient' => $_POST, 'errors' => ['general' => 'Failed to delete the old user record']];
            }
        } else {
            $userId = $existingMedic->id_utilizator;
            $query = "UPDATE Utilizator SET nume='$nume' WHERE Utilizator.id = $userId";
            if ($mysqli->query($query) !== TRUE) {
                return ['doctor' => $_POST, 'errors' => ['general' => $mysqli->error]];
            }
        }
        if ($imagine) {
            $sql = "UPDATE Medic SET id_utilizator='$userId', specialitate='$specialitate', data_nasterii='$data_nasterii', telefon='$telefon', adresa='$adresa', imagine='$imagine', creare=NOW() WHERE Medic.id = $id";
        } else {
            $sql = "UPDATE Medic SET id_utilizator='$userId', specialitate='$specialitate', data_nasterii='$data_nasterii', telefon='$telefon', adresa='$adresa', creare=NOW() WHERE Medic.id = $id";
        }

        if ($mysqli->query($sql) !== TRUE) {
            return ['doctor' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }

        $mysqli->close();
        return true;
    }

    static function saveUser($email, $nume)
    {
        $mysqli = createConnection();
        $secure_pass = password_hash('123456', PASSWORD_BCRYPT);
        $sql = "INSERT INTO Utilizator (email, nume, parola, roluri) VALUES ('" . $email . "', '" . $nume . "', '" . $secure_pass . "', 'ROL_MEDIC')";
        if ($mysqli->query($sql) !== TRUE) {
            return ['doctor' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $user = Utilizator::activeUserRequest($email);
        Mail::sendAccountInvitation($email, $nume, $user->hash_activare);
        $userId = $mysqli->insert_id;
        return $userId;
        $mysqli->close();
    }

    static function deleteOldUser($oldUserId)
    {
        $mysqli = createConnection();
        $sql = "DELETE FROM Utilizator WHERE id = $oldUserId";
        if ($mysqli->query($sql) !== TRUE) {
            return ['pacient' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
        return true;
    }

    static function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ['errors' => ['id' => 'Invalid Medic ID']];
        }
        $existingMedic = self::get($id);
        if (!$existingMedic) {
            return ['errors' => ['id' => 'Medic record not found']];
        }
        $mysqli = createConnection();

        $utilizatorId = $existingMedic->id_utilizator;
        $sqlUtilizator = "DELETE FROM Utilizator WHERE Utilizator.id = $utilizatorId";
        if ($mysqli->query($sqlUtilizator) !== TRUE) {
            return ['errors' => ['general' => $mysqli->error]];
        }

        $sql = "DELETE FROM Medic WHERE Medic.id = $id";
        if ($mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return ['errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
    }

    public function getAge()
    {
        $birth = new DateTime($this->data_nasterii);
        $now = new DateTime();
        $age = $birth->diff($now)->y;
        return $age;
    }

    static function uploadImage($file)
    {
        $targetDir = realpath(__DIR__ . '/../assets/img/profil/');
        $filename = substr(md5(mt_rand()), 0, 7) . '-' . basename(($file['name']));
        $targetFile = $targetDir . '/' . $filename;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if ($file['size'] > 500000) {
            return false;
        }
        if ($imageFileType !== 'jpg' && $imageFileType !== 'jpeg' && $imageFileType !== 'png' && $imageFileType !== 'gif' && $imageFileType !== 'svg') {
            return false;
        }
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $filename;
        } else {
            return false;
        }
    }
}
