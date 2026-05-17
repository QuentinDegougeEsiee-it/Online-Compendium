<?php
session_start();
require_once 'db.php';

// Sécurité : Si l'utilisateur n'est pas connecté, on bloque
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $monster_id = intval($_POST['monster_id'] ?? 0);

    // 1. CREATE (Ajouter une note)
    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        if (!empty($title) && !empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO hunter_notes (user_id, monster_id, title, content) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $monster_id, $title, $content]);
        }
    }
    
    // 2. UPDATE (Modifier une note)
    if ($action === 'update') {
        $note_id = intval($_POST['note_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        if (!empty($title) && !empty($content)) {
            // Sécurité : On vérifie que la note appartient bien à l'utilisateur connecté
            $stmt = $pdo->prepare("UPDATE hunter_notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $content, $note_id, $user_id]);
        }
    }

    // 3. DELETE (Supprimer une note)
    if ($action === 'delete') {
        $note_id = intval($_POST['note_id'] ?? 0);
        
        // Sécurité : On vérifie que la note appartient bien à l'utilisateur connecté
        $stmt = $pdo->prepare("DELETE FROM hunter_notes WHERE id = ? AND user_id = ?");
        $stmt->execute([$note_id, $user_id]);
    }
}

// Dans tous les cas, on réaffiche la page du monstre
header("Location: details.php?id=" . $monster_id . "#notes-tab");
exit;