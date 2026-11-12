<?php 

require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';

    if ($action === 'add') 
    {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $filiere = trim($_POST['filiere'] ?? '');

        if (
            $nom == '' ||
            strlen($nom) > 100 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 150 ||
            $filiere == '' ||
            strlen($filiere) > 100
            ) {
                $error_message = "Veuillez remplir tous les champs correctement.";
            }
            else {
                $check = $pdo->prepare(
                    "SELECT id FROM students WHERE email = :email"
                );

                $check->execute(['email' => $email]);

                if ($check->fetch()) {
                    $error_message = "L'email existe déjà. Veuillez utiliser un autre email.";
                }
                else {
                    
                    $stmt = $pdo->prepare(
                        "INSERT INTO students (nom, email, filiere) 
                        VALUES (:nom, :email, :filiere)"
                    );

                    $stmt->execute([
                        'nom' => $nom,
                        'email' => $email,
                        'filiere' => $filiere
                    ]);

                    if ($stmt->rowCount() > 0) {
                        $_SESSION['success_add_message'] = "L'étudiant a été ajouté avec succès.";
                    }

                    header('Location: index.php');
                    exit;
                }
            }
        

       
            
                
                
               
           
        
    }



    if ($action === 'delete') {
        $id = filter_var(
            $_POST['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($id !== false && $id !== null && $id > 0) {
           $stmt = $pdo->prepare(
                "DELETE FROM students WHERE id = :id"
           );

           $stmt->execute(['id' => $id]);

              if ($stmt->rowCount() > 0) {
                 $_SESSION['success_delete_message'] = "L'étudiant a été supprimé avec succès.";
                }
        }
    }

    header('Location: index.php');
    exit;
}


$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare(
        "SELECT * FROM students
        WHERE nom like :search
        ORDER BY id DESC"
    );

    $stmt->execute([
        'search' => '%' . $search . '%'
    ]);

    $students = $stmt->fetchAll();
} else {
    $stmt = $pdo->query(
        "SELECT * FROM students ORDER BY id DESC"
    );

    $students = $stmt->fetchAll();
}

function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}






?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des etudients</title>
    <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>
<body>
    <div class="container">
        <h1>Gestion des etudients</h1>

        <!-- Formulaire d'ajout -->
        <div class="card">

             <?php if (isset($_SESSION['success_update_message'])): ?>        
                    <div class="success-message">
                       <h3><?= $_SESSION['success_update_message'] ?></h3>
                    </div>

                    <?php unset($_SESSION['success_update_message']); ?>
            <?php endif; ?> 
                  
            <?php if (isset($_SESSION['success_add_message'])): ?>        
                    <div class="success-message">
                       <h3><?= $_SESSION['success_add_message'] ?></h3>
                    </div>

                    <?php unset($_SESSION['success_add_message']); ?>
            <?php endif; ?> 

            <?php if (isset($_SESSION['success_delete_message'])): ?>        
                    <div class="success-delete-message">
                       <h3><?= $_SESSION['success_delete_message'] ?></h3>
                    </div>

                    <?php unset($_SESSION['success_delete_message']); ?>
            <?php endif; ?> 
            

            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <h3><?= $error_message ?></h3>
                </div>
            <?php endif; ?>

            <h2>Ajouter un etudient</h2>

            <form method="POST" >

                <input type="hidden" name="action" value="add">

                <label>Nom Complete</label>
                <input 
                    type="text"
                    name="nom"
                    maxlength="100"
                    required
                >

                <label>Email</label>
                <input 
                    type="email"
                    name="email"
                    maxlength="150"
                    required
                >

                <label>Filiere</label>
                <input 
                    type="text"
                    name="filiere"
                    maxlength="100"
                    required
                >

                <button class="add" type="submit">
                    Ajouter l'etudient
                </button>
            </form>
        </div>

        <div class="card">
            <h2>List des etudients</h2>

            <form method="GET" class="search-form">
                <input 
                    type="text"
                    name="search"
                    placeholder="Rechercher par nom..."
                    value="<?= e($search) ?>"
                >

                <button class="search" type="submit">
                    Rechercher
                </button>
            </form>

            <table>
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Nom</td>
                        <td>Email</td>
                        <td>Filiere</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                         <tr>
                            <td colspan="5">
                                Acucun etudient trouve
                            </td>
                         </tr> 
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?=  (int) $student['id'] ?></td>
                                <td><?= e($student['nom']) ?></td>
                                <td><?=  e($student['email']) ?></td>
                                <td><?=  e($student['filiere']) ?></td>

                                <td>
                                    <div class="student-actions">
                                    <form
                                        method="POST"
                                        onsubmit="return confirm('Supprimer cet studient ?')"
                                    >
                                        <input 
                                            type="hidden"
                                            name="action"
                                            value="delete"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $student['id'] ?>" 
                                        >

                                        <button
                                            type="submit"
                                            class="delete"
                                            aria-label="Delete student"
                                            title="Delete student"
                                        >
                                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18" />
                                                <path d="M8 6V4h8v2" />
                                                <path d="m19 6-1 14H6L5 6" />
                                                <path d="M10 11v5M14 11v5" />
                                            </svg>
                                        </button>    

                                    </form>

                                    <form 
                                        action="edit.php"
                                        method="POST"
                                        
                                    >
                                        <input 
                                            type="hidden"
                                            name="action"
                                            value="edit"
                                        >

                                        <input 
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $student['id']  ?>"
                                        >

                                        <input 
                                            type="hidden"
                                            name="nom"
                                            value="<?= htmlspecialchars($student['nom']) ?>"
                                        >

                                        <input 
                                            type="hidden" 
                                            name="email"    
                                            value="<?= htmlspecialchars($student['email']) ?>"
                                        >

                                        <input 
                                            type="hidden"
                                            name="filiere"
                                            value="<?= htmlspecialchars($student['filiere']) ?>"    
                                        >

                                        

                                        <button
                                            type="submit"
                                            class="update"
                                            aria-label="Edit student"
                                            title="Edit student"
                                        >
                                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 20h9" />
                                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" />
                                            </svg>
                                        </button>
                                    </form>
                                    </div>
                                </td>
                            </tr>  
                        <?php endforeach; ?>
                    <?php endif; ?>           
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>