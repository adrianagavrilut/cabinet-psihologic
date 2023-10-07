<?php

class Chat
{
    static function createConversation($medicUserId, $pacientId)
    {
        $pacient = Pacient::get($medicUserId, $pacientId);

        $mysqli = createConnection();

        $sql = "INSERT INTO Conversatie (id_utilizator_medic, id_utilizator_pacient, creare) VALUES ('" . $medicUserId . "', '" . $pacient->id_utilizator . "', NOW())";
        if ($mysqli->query($sql) !== TRUE) {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        //id-ul noului utilizator
        $conversationId = $mysqli->insert_id;

        $conversatie = self::getConversation($conversationId);
        $mysqli->close();
        return $conversatie;
    }

    static function getConversation($conversationId)
    {
        $mysqli = createConnection();
        $sql = "SELECT c.* FROM Conversatie as c WHERE c.id=$conversationId";
        $result = $mysqli->query($sql);
        $conversation = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $conversation = $row;
                $conversation['pacient'] = Pacient::getForUserId($conversation['id_utilizator_pacient']);
            }
        } else {
            //throw error
        }
        $mysqli->close();
        return $conversation;
    }

    static function getConversationForPacient($pacientUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT c.* FROM Conversatie as c WHERE c.id_utilizator_pacient=$pacientUserId";
        $result = $mysqli->query($sql);
        $conversation = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $conversation = $row;
                $conversation['pacient'] = Pacient::getForUserId($conversation['id_utilizator_pacient']);
            }
        } else {
            //throw error
        }
        $mysqli->close();
        return $conversation;
    }

    static function getAllConverations($medicUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT c.* FROM Conversatie as c JOIN Utilizator as u ON u.id=c.id_utilizator_medic WHERE c.id_utilizator_medic=$medicUserId";
        $result = $mysqli->query($sql);
        $conversations = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['pacient'] = Pacient::getForUserId($row['id_utilizator_pacient']);
                $conversations[] = $row;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        return $conversations;
    }

    static function getNrConvForMedic($medicUserId)
    {
        $mysqli = createConnection();
        $sql = "SELECT COUNT(*) as nrConv FROM Conversatie WHERE Conversatie.id_utilizator_medic=$medicUserId";
        $result = $mysqli->query($sql);
        $row = mysqli_fetch_assoc($result);
        $nrConv = $row['nrConv'];
        $mysqli->close();
        return $nrConv;
    }

    static function getAllMessages($conversationId, $currentUserId, $isMedic)
    {
        $mysqli = createConnection();
        $sql = "SELECT m.* FROM Mesaj as m WHERE m.id_conversatie=$conversationId";
        $result = $mysqli->query($sql);
        $messages = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if (($row['id_utilizator'] == $currentUserId && $isMedic) || ($row['id_utilizator'] != $currentUserId && !$isMedic)) {
                    $row['type'] = $isMedic ? 'outgoing' : 'incoming';
                    $row['user'] = Medic::getForUserId($row['id_utilizator']);
                } else {
                    $row['user'] = Pacient::getForUserId($row['id_utilizator']);
                    $row['type'] = $isMedic ? 'incoming' : 'outgoing';
                }
                $row['fisier'] = json_decode($row['fisier']);
                $messages[] = $row;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        self::seeMessages($messages, $currentUserId);
        return $messages;
    }

    static function getUnseenMessages($conversationId, $currentUserId, $isMedic)
    {
        $mysqli = createConnection();
        $sql = "SELECT m.* FROM Mesaj as m WHERE m.id_conversatie=$conversationId AND m.vazut=0 AND m.id_utilizator != $currentUserId";
        $result = $mysqli->query($sql);
        $messages = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if (($row['id_utilizator'] == $currentUserId && $isMedic) || ($row['id_utilizator'] != $currentUserId && !$isMedic)) {
                    $row['type'] = $isMedic ? 'outgoing' : 'incoming';
                    $row['user'] = Medic::getForUserId($row['id_utilizator']);
                } else {
                    $row['user'] = Pacient::getForUserId($row['id_utilizator']);
                    $row['type'] = $isMedic ? 'incoming' : 'outgoing';
                }
                $row['fisier'] = json_decode($row['fisier']);
                $messages[] = $row;
            }
        } else {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $mysqli->close();
        self::seeMessages($messages, $currentUserId);
        return $messages;
    }

    static function getMessage($messageId, $currentUserId, $isMedic)
    {
        $mysqli = createConnection();
        $sql = "SELECT m.* FROM Mesaj as m WHERE m.id=$messageId";
        $result = $mysqli->query($sql);
        $message = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $message = $row;
                if (($message['id_utilizator'] == $currentUserId && $isMedic) || ($message['id_utilizator'] != $currentUserId && !$isMedic)) {
                    $message['type'] = $isMedic ? 'outgoing' : 'incoming';
                    $message['user'] = Medic::getForUserId($row['id_utilizator']);
                } else {
                    $message['user'] = Pacient::getForUserId($row['id_utilizator']);
                    $message['type'] = $isMedic ? 'incoming' : 'outgoing';
                }
                $message['fisier'] = json_decode($row['fisier']);
            }
        } else {
            //throw error
        }
        $mysqli->close();
        self::seeMessages([$message], $currentUserId);
        return $message;
    }

    static function createMessage($currentUserId, $conversationId, $content, $isMedic, $files)
    {
        $mysqli = createConnection();

        $sql = "INSERT INTO Mesaj (id_conversatie, id_utilizator, continut, creare, fisier) VALUES ('" . $conversationId . "', '" . $currentUserId . "', '" . $content . "', NOW(), '" . $files . "')";
        if ($mysqli->query($sql) !== TRUE) {
            echo 'Error executing the query: ' . $mysqli->error;
        }
        $messageId = $mysqli->insert_id;

        $message = self::getMessage($messageId, $currentUserId, $isMedic);
        $mysqli->close();
        return $message;
    }

    static function seeMessages($messages, $currentUserId)
    {
        $mysqli = createConnection();
        foreach ($messages as $message) {
            if ($message['id_utilizator'] != $currentUserId) {
                $sql = "UPDATE Mesaj SET vazut=1 WHERE id=" . $message['id'];
                $mysqli->query($sql);
            }
        }
        $mysqli->close();
    }

    static function uploadFile($filename, $tempName)
    {
        $targetDir = realpath(__DIR__ . '/../assets/img/chat/');
        $filename = substr(md5(mt_rand()), 0, 7) . '-' . basename(($filename));
        $targetFile = $targetDir . '/' . $filename;
        if (move_uploaded_file($tempName, $targetFile)) {
            return $filename;
        } else {
            return false;
        }
    }
}
