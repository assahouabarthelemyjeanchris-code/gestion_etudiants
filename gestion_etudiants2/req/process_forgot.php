<?php
// On active l'affichage des erreurs PHP pour voir ce qui se cache
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once "../DB_connexion.php";

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $token = bin2hex(random_bytes(16)); 

    echo "--- PHASE DE DIAGNOSTIC ---<br>";
    echo "Email saisi : [" . htmlspecialchars($email) . "]<br>";

    // 1. TESTER LA TABLE ADMINISTRATEUR
    try {
        $sql_admin = "SELECT * FROM administrateur WHERE email = :email";
        $stmt_admin = $conn->prepare($sql_admin);
        $stmt_admin->execute([':email' => $email]);
        $res_admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

        if ($res_admin) {
            echo "SUCCÈS : L'email existe dans la table 'administrateur'.<br>";
            
            // Tentative de mise à jour du token
            $up = "UPDATE administrateur SET reset_token = :token WHERE email = :email";
            $conn->prepare($up)->execute([':token' => $token, ':email' => $email]);
            
            echo "Token mis à jour ! Redirection...<br>";
            header("Refresh: 2; url=../reset_password.php?token=$token");
            exit;
        } else {
            echo "ÉCHEC : L'email n'a pas été trouvé dans 'administrateur'.<br>";
        }
    } catch (PDOException $e) {
        die("ERREUR SQL CRITIQUE (Admin) : " . $e->getMessage());
    }

    // 2. TESTER LA TABLE RESPONSABLE (si admin non trouvé)
    echo "<br>--- TEST TABLE RESPONSABLE ---<br>";
    try {
        // Recherche dans la table responsablefiliere (utilisée dans votre reset_password.php)
        $sql_resp = "SELECT * FROM responsable_departement WHERE email = :email";
        $stmt_resp = $conn->prepare($sql_resp);
        $stmt_resp->execute([':email' => $email]);
        $res_resp = $stmt_resp->fetch(PDO::FETCH_ASSOC);

        if ($res_resp) {
            echo "SUCCÈS : L'email existe dans la table 'responsable_departement'.<br>";
            
            // Mise à jour du token pour le responsable
            $up_resp = "UPDATE responsable_departement SET reset_token = :token WHERE email = :email";
            $conn->prepare($up_resp)->execute([':token' => $token, ':email' => $email]);
            
            echo "Token responsable mis à jour ! Redirection...<br>";
            header("Refresh: 2; url=../reset_password.php?token=$token");
            exit;
        } else {
            echo "ÉCHEC : L'email n'a pas été trouvé dans 'responsable_departement'.<br>";
            
            // Vérification de format si la table contient au moins une entrée
            $check_format_resp = $conn->query("SELECT email FROM responsable_departement LIMIT 1")->fetch();
            if($check_format_resp) {
                echo "Note : Un email trouvé en BDD (Responsable) ressemble à ceci : [" . $check_format_resp['email'] . "]<br>";
            } else {
                echo "ALERTE : La table 'responsable_departement' est totalement VIDE ou la colonne email est vide.<br>";
            }
        }
    } catch (PDOException $e) {
        die("ERREUR SQL CRITIQUE (Responsable) : " . $e->getMessage());
    }

} else {
    echo "Aucun email reçu.";
}
?>