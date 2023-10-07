<?php
class Utilizator
{
    public $id;
    public $email;
    public $nume;
    public $rol;
    public $parola;
    public $hash_resetare;
    public $hash_activare;

    public static function resetPasswordRequest($email)
    {
        $user = self::getUserByEmail($email, true);
        if (!$user) {
            return false;
        }
        $mysqli = createConnection();
        $hash = md5(uniqid($email . time(), true));
        $sql = "UPDATE Utilizator SET hash_resetare='$hash' WHERE email = '$email'";
        if ($mysqli->query($sql) !== TRUE) {
            return false;
        }
        $user->hash_resetare = $hash;
        return $user;
    }

    public static function checkUserByResetToken($resetToken)
    {
        $mysqli = createConnection();
        $sql = "SELECT * FROM Utilizator WHERE hash_resetare = '$resetToken'";
        $result = $mysqli->query($sql);
        if ($result && $result->num_rows == 1) {
            return true;
        }
        return true;
    }

    public static function resetPassword($resetToken, $password)
    {
        $secure_pass = password_hash($password, PASSWORD_BCRYPT);
        $mysqli = createConnection();
        $sql = "UPDATE Utilizator SET hash_resetare=NULL, parola='$secure_pass' WHERE hash_resetare = '$resetToken'";
        if ($mysqli->query($sql) !== TRUE) {
            return false;
        }
        return true;
    }

    public static function activeUserRequest($email)
    {
        $user = self::getUserByEmail($email);
        if (!$user) {
            return false;
        }
        $mysqli = createConnection();
        $hash = md5(uniqid($email . time(), true));
        $sql = "UPDATE Utilizator SET hash_activare='$hash' WHERE email = '$email'";
        if ($mysqli->query($sql) !== TRUE) {
            return false;
        }
        $user->hash_activare = $hash;
        return $user;
    }

    public static function activateUser($activationToken, $password)
    {
        $secure_pass = password_hash($password, PASSWORD_BCRYPT);
        $mysqli = createConnection();
        $secure_pass = mysqli_real_escape_string($mysqli, $secure_pass);
        $sql = "UPDATE Utilizator SET activ=1, hash_activare=NULL, parola='$secure_pass' WHERE hash_activare = '$activationToken'";
        if ($mysqli->query($sql) !== TRUE) {
            return false;
        }
        return true;
    }

    public static function getUserByEmail($email, $onlyActive = false)
    {
        $mysqli = createConnection();
        $sql = "SELECT * FROM Utilizator WHERE email = '$email'" . ($onlyActive ? '  AND activ=1' : '');
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $user = false;
        while ($row = $result->fetch_assoc()) {
            $user = new Utilizator();
            $user->id = $row['id'];
            $user->email = $row['email'];
            $user->nume = $row['nume'];
            $user->rol = $row['roluri'];
            $user->parola = $row['parola'];
            break;
        }
        $mysqli->close();
        if (!$user) {
            return false;
        }
        return $user;
    }

    public static function getUserByHashResetare($hash, $onlyActive = false)
    {
        $mysqli = createConnection();
        $sql = "SELECT * FROM Utilizator WHERE hash_resetare = '$hash'" . ($onlyActive ? '  AND activ=1' : '');
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $user = false;
        while ($row = $result->fetch_assoc()) {
            $user = new Utilizator();
            $user->id = $row['id'];
            $user->email = $row['email'];
            $user->nume = $row['nume'];
            $user->rol = $row['roluri'];
            $user->parola = $row['parola'];
            break;
        }
        $mysqli->close();
        if (!$user) {
            return false;
        }
        return $user;
    }

    public static function getUserByHashActivare($hash)
    {
        $mysqli = createConnection();
        $sql = "SELECT * FROM Utilizator WHERE hash_activare = '$hash'";
        $result = $mysqli->query($sql);
        if (!$result) {
            echo 'Error executing the query: ' . $mysqli->error;
            return null; // Or appropriate error handling
        }
        $user = false;
        while ($row = $result->fetch_assoc()) {
            $user = new Utilizator();
            $user->id = $row['id'];
            $user->email = $row['email'];
            $user->nume = $row['nume'];
            $user->rol = $row['roluri'];
            $user->parola = $row['parola'];
            break;
        }
        $mysqli->close();
        if (!$user) {
            return false;
        }
        return $user;
    }

    public static function checkUser($email, $password)
    {
        $user = self::getUserByEmail($email, true);
        if (!$user) {
            return false;
        }
        $isPasswordCorrect = password_verify($password, $user->parola);
        if (!$isPasswordCorrect) {
            return false;
        }
        return $user;
    }

    public static function checkUserAuth()
    {
        if (isset($_SESSION['email'])) {
            return true;
        }
        return false;
    }

    public static function checkUserAdmin()
    {
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'ROL_ADMIN') {
            return true;
        }
        return false;
    }

    public static function checkUserMedic()
    {
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'ROL_MEDIC') {
            return true;
        }
        return false;
    }

    public static function checkUserPacient()
    {
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'ROL_PACIENT') {
            return true;
        }
        return false;
    }

    public static function authenticateUser($email, $rol, $userId)
    {
        $_SESSION['email'] = $email;
        $_SESSION['rol'] = $rol;
        $_SESSION['userId'] = $userId;
    }

    public static function deauthenticateUser()
    {
        unset($_SESSION['email']);
        unset($_SESSION['rol']);
        unset($_SESSION['userId']);
    }

    public static function getNrMedic()
    {
        $mysqli = createConnection();
        $sql = "SELECT COUNT(*) as numar_medici FROM Utilizator WHERE roluri='ROL_MEDIC'";
        $result = $mysqli->query($sql);
        $row = mysqli_fetch_assoc($result);
        $nrMedici = $row['numar_medici'];
        $mysqli->close();
        return $nrMedici;
    }

    public static function getNrPacient()
    {
        $mysqli = createConnection();
        $sql = "SELECT COUNT(*) as numar_pacienti FROM Utilizator WHERE roluri='ROL_PACIENT'";
        $result = $mysqli->query($sql);
        $row = mysqli_fetch_assoc($result);
        $nrPacienti = $row['numar_pacienti'];
        $mysqli->close();
        return $nrPacienti;
    }
}
