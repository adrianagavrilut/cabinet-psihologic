<?php

class Pacient
{
    public $id;
    public $id_utilizator;
    public $id_medic;
    public $nume;
    public $data_nasterii;
    public $gen;
    public $telefon;
    public $adresa;
    public $inregistrare;
    public $email;
    public $bunic_p;
    public $bunic_m;
    public $bunica_p;
    public $bunica_m;
    public $tata;
    public $mama;

    function __construct($row)
    {
        $this->id = $row['id'];
        $this->id_utilizator = array_key_exists('id_utilizator', $row) ? $row['id_utilizator'] : null;
        $this->id_medic = array_key_exists('id_medic', $row) ? $row['id_medic'] : null;
        $this->nume = $row['nume'];
        $this->data_nasterii = $row['data_nasterii'];
        $this->gen = $row['gen'];
        $this->telefon = $row['telefon'];
        $this->bunic_p = $row['bunic_p'];
        $this->bunic_m = $row['bunic_m'];
        $this->bunica_p = $row['bunica_p'];
        $this->bunica_m = $row['bunica_m'];
        $this->tata = $row['tata'];
        $this->mama = $row['mama'];
        $this->adresa = $row['adresa'];
        $this->inregistrare = $row['creare'];
        $this->email = array_key_exists('email', $row) ? $row['email'] : null;
    }

    static function getAll($medicUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT p.*, u.email as email, u.nume as nume FROM Pacient as p JOIN Utilizator as u ON u.id=p.id_utilizator JOIN Medic as m on m.id=p.id_medic WHERE m.id_utilizator=$medicUserId";
        $result = $mysqli->query($sql);
        $pacients = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $pacient = new Pacient($row);
                $pacients[] = $pacient;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $pacients;
    }

    static function getNrForMedic($medicUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT COUNT(*) as nrPac FROM Pacient JOIN Medic AS m ON m.id = Pacient.id_medic WHERE m.id_utilizator=$medicUserId";
        $result = $mysqli->query($sql);
        $row = mysqli_fetch_assoc($result);
        $nrPac = $row['nrPac'];
        $mysqli->close();
        return $nrPac;
    }

    static function getForUserId($userId)
    {
        $mysqli = createConnection();
        $sql = "SELECT p.*, u.email as email, u.nume as nume FROM Pacient as p JOIN Utilizator as u ON u.id=p.id_utilizator WHERE p.id_utilizator=$userId";
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $Pacient = null;
        if ($row = $result->fetch_assoc()) {
            $Pacient = new Pacient($row);
        }
        $mysqli->close();
        return $Pacient;
    }

    static function get($medicUserId, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            throw new Exception('Invalid Pacient ID');
        }
        $mysqli = createConnection();
        $sql = "SELECT p.*, u.email as email, u.nume as nume FROM Pacient as p JOIN Utilizator as u ON u.id=p.id_utilizator JOIN Medic as m on m.id=p.id_medic WHERE m.id_utilizator=$medicUserId AND p.id=$id";
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $pacient = null;
        if ($row = $result->fetch_assoc()) {
            $pacient = new Pacient($row);
        } else {
            throw new Exception('Resource not found');
        }
        $mysqli->close();
        return $pacient;
    }

    static function create($medicUserId, $email, $nume, $data_nasterii, $gen, $telefon, $adresa, $testePacient)
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

        $sql = "SELECT * FROM Utilizator WHERE email='" . $email . "';";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            $errors['email'] = 'Emailul este deja folosit';
        }
        if (!empty($errors)) {
            return ['pacient' => $_POST, 'errors' => $errors];
        }
        $medic = Medic::getForUserId($medicUserId);

        $userId = self::saveUser($email, $nume);

        $sql = "INSERT INTO Pacient (id_utilizator, data_nasterii, gen, telefon, adresa, creare, id_medic) VALUES ('$userId', '" . $data_nasterii . "', '" . $gen . "', '" . $telefon . "', '" . $adresa . "', NOW(), '" . $medic->id . "')";
        if ($mysqli->query($sql) !== TRUE) {
            return ['pacient' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }

        foreach ($testePacient as $test) {
            PacientTest::create($mysqli->insert_id, $test);
        }
        $mysqli->close();
        return true;
    }

    static function update($medicUserId, $id, $email, $nume, $data_nasterii, $gen, $telefon, $adresa, $testePacient)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception('Invalid Pacient ID');
        }
        $existingPacient = self::get($medicUserId, $id);
        if (!$existingPacient) {
            return ['pacient' => $_POST, 'errors' => ['id' => 'Pacient record not found']];
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

        if ($existingPacient->email != $email) {
            $sql = "SELECT * FROM Utilizator WHERE email='" . $email . "';";
            $result = $mysqli->query($sql);
            if ($result->num_rows > 0) {
                $errors['email'] = 'Emailul este deja folosit';
            }
        }

        if (!empty($errors)) {
            return ['pacient' => $_POST, 'errors' => $errors];
        }
        if ($existingPacient->email != $email) {
            //salvarea utilizatorului si stergerea utilizatorului vechi
            $oldUserId = $existingPacient->id_utilizator;
            $userId = self::saveUser($email, $nume);
            $deleteUserResult = self::deleteOldUser($oldUserId);
            if ($deleteUserResult !== true) {
                return ['pacient' => $_POST, 'errors' => ['general' => 'Failed to delete the old user record']];
            }
        } else {
            $userId = $existingPacient->id_utilizator;
            $query = "UPDATE Utilizator SET nume='$nume' WHERE Utilizator.id = $userId";
            if ($mysqli->query($query) !== TRUE) {
                return ['pacient' => $_POST, 'errors' => ['general' => $mysqli->error]];
            }
        }
        $sql = "UPDATE Pacient SET id_utilizator='$userId', gen='$gen', data_nasterii='$data_nasterii', telefon='$telefon', adresa='$adresa', creare=NOW() WHERE Pacient.id = $id";
        if ($mysqli->query($sql) !== TRUE) {
            return ['pacient' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }

        PacientTest::deleteAllForPacient($id);
        foreach ($testePacient as $test) {
            PacientTest::create($id, $test);
        }
        $mysqli->close();
        return true;
    }

    static function saveUser($email, $nume)
    {
        $mysqli = createConnection();
        $secure_pass = password_hash('123456', PASSWORD_BCRYPT);
        $sql = "INSERT INTO Utilizator (email, nume, parola, roluri) VALUES ('" . $email . "', '" . $nume . "', '" . $secure_pass . "', 'ROL_PACIENT')";
        if ($mysqli->query($sql) !== TRUE) {
            return ['pacient' => $_POST, 'errors' => ['general' => $mysqli->error]];
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

    static function delete($medicUserId, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ['errors' => ['id' => 'Invalid Pacient ID']];
        }
        $existingPacient = self::get($medicUserId, $id);
        if (!$existingPacient) {
            return ['errors' => ['id' => 'Pacient record not found']];
        }
        $mysqli = createConnection();

        $utilizatorId = $existingPacient->id_utilizator;
        $sqlUtilizator = "DELETE FROM Utilizator WHERE Utilizator.id = $utilizatorId";
        if ($mysqli->query($sqlUtilizator) !== TRUE) {
            return ['errors' => ['general' => $mysqli->error]];
        }

        $sql = "DELETE FROM Pacient WHERE Pacient.id = $id";
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

    static function hasChat($userId)
    {
        $mysqli = createConnection();
        $sql = "SELECT p.* FROM Pacient as p JOIN Utilizator as u ON u.id=p.id_utilizator JOIN Conversatie AS c ON p.id_utilizator = c.id_utilizator_pacient WHERE p.id_utilizator=$userId ";
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null;
        }
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    static function updateHistory($idPacient, $field_name, $value)
    {
        $mysqli = createConnection();
        $sql = "UPDATE Pacient SET Pacient.$field_name = '$value' WHERE Pacient.id=$idPacient";
        if ($mysqli->query($sql) !== TRUE) {
            return ['pacient' => $_POST, 'errors' => ['general' => $mysqli->error]];
        }
        $mysqli->close();
        return true;
    }

    static function getHistory($idPacient)
    {
        $mysqli = createConnection();
        $sql = "SELECT bunic_m, bunic_p, bunica_m, bunica_p, tata, mama FROM Pacient WHERE id = $idPacient";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return [
                ['ruda' => 'Bunicul patern', 'continut' => $row['bunic_m']],
                ['ruda' => 'Bunica paternă', 'continut' => $row['bunic_p']],
                ['ruda' => 'Bunicul matern', 'continut' => $row['bunica_m']],
                ['ruda' => 'Bunica maternă', 'continut' => $row['bunica_p']],
                ['ruda' => 'Tata', 'continut' => $row['tata']],
                ['ruda' => 'Mama', 'continut' => $row['mama']],

            ];
        } else {
            return null;
        }
        $mysqli->close();
    }

    static function getMedicUserIdForPacient($pacientUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT Medic.id_utilizator FROM Medic JOIN Pacient ON Medic.id = Pacient.id_medic WHERE Pacient.id_utilizator = $pacientUserId";
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null;
        }
        $medicUserId = null;
        if ($row = $result->fetch_assoc()) {
            $medicUserId = $row['id_utilizator'];
        } else {
            throw new Exception('Resource not found');
        }
        $mysqli->close();
        return $medicUserId;
    }
}
