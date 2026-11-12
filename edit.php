<?php 

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';

    $id = $_POST['id'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $filiere = $_POST['filiere'] ?? '';

    
    

    if($action === 'update' && $id !== '')
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);

        $new_nom = trim($_POST['nom'] ?? '');
        $new_email = trim($_POST['email'] ?? '');
        $new_filiere = trim($_POST['filiere'] ?? '');

        if (
            $new_nom !== '' &&
            strlen($new_nom) <= 100 &&
            filter_var($new_email, FILTER_VALIDATE_EMAIL) &&
            strlen($new_email) <= 150 &&
            $new_filiere !== '' &&
            strlen($new_filiere) <= 100
        ) {
            $sql = "UPDATE students 
                    SET nom = :nom, email = :email, filiere = :filiere
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                'nom' => $new_nom,
                'email' => $new_email,
                'filiere' => $new_filiere,
                'id' => $id
            ]);

            if ($stmt->rowCount() > 0) {
                session_start();
                $_SESSION['success_update_message'] = "L'étudiant a été mis à jour avec succès.";
                header('Location: index.php');
                exit;
            } 
                
        }
    }
    
 
    

   

   

   
}











?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>editer etudient</title>
    <link rel="stylesheet" href="edit_style.css">
</head>
<body>
    <div class="container">
        <h1>Editer etudient</h1>

        <!-- Formulaire d'ajout -->
        <div class="card">
            <h2>Editer <span class="title-name"><?= $nom ?></span></h2>

            <form method="POST" >

                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $id ?>">

                <label>Nom Complete</label>
                <input 
                    type="text"
                    name="nom"
                    maxlength="100"
                    value="<?= htmlspecialchars($nom) ?>"
                    required
                >

                <label>Email</label>
                <input 
                    type="email"
                    name="email"
                    maxlength="150"
                    value="<?= htmlspecialchars($email) ?>"
                    required
                >

                <label>Filiere</label>
                <input 
                    type="text"
                    name="filiere"
                    maxlength="100"
                    value="<?= htmlspecialchars($filiere) ?>"
                    required
                >

                <button class="edit" type="submit">
                    Editer l'etudient
                </button>

                <a href="index.php" style="text-decoration: none;background: yellow; color: black; padding: 10px 15px; border-radius: 6px; margin-left: 10px;" >Annuler</a>
                
            </form>
        </div>
    </div>
</body>
</html>