<?php
// Fichier de test pour vérifier la connexion à la base de données
include(__DIR__ . '/../../config.php');

echo "<h2>Test de connexion à la base de données</h2>";

try {
    $db = config::getConnexion();
    echo "<p style='color: green;'>✅ Connexion réussie à la base de données !</p>";
    
    // Vérifier si la table cours existe
    $sql = "SHOW TABLES LIKE 'cours'";
    $result = $db->query($sql);
    
    if ($result->rowCount() > 0) {
        echo "<p style='color: green;'>✅ La table 'cours' existe</p>";
        
        // Compter les cours
        $sql = "SELECT COUNT(*) as total FROM cours";
        $result = $db->query($sql);
        $count = $result->fetch();
        echo "<p><strong>Nombre de cours :</strong> " . $count['total'] . "</p>";
        
        // Afficher tous les cours
        $sql = "SELECT * FROM cours";
        $result = $db->query($sql);
        $cours = $result->fetchAll();
        
        echo "<h3>Liste des cours :</h3>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #8b5cf6; color: white;'>";
        echo "<th>ID</th><th>Titre</th><th>Description</th><th>Durée</th><th>Niveau</th><th>Formateur</th>";
        echo "</tr>";
        
        foreach ($cours as $c) {
            echo "<tr>";
            echo "<td>" . $c['id'] . "</td>";
            echo "<td>" . htmlspecialchars($c['titre']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($c['description'], 0, 50)) . "...</td>";
            echo "<td>" . $c['duree'] . "h</td>";
            echo "<td>" . htmlspecialchars($c['niveau']) . "</td>";
            echo "<td>" . htmlspecialchars($c['formateur']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ La table 'cours' n'existe pas</p>";
        echo "<p>Vous devez exécuter le fichier database.sql dans phpMyAdmin</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur de connexion : " . $e->getMessage() . "</p>";
    echo "<p><strong>Vérifiez :</strong></p>";
    echo "<ul>";
    echo "<li>Que MySQL est démarré dans XAMPP</li>";
    echo "<li>Que la base de données 'formation_apprentissage' existe</li>";
    echo "<li>Que les identifiants dans config.php sont corrects</li>";
    echo "</ul>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #0a0a0a;
    color: #ffffff;
}
table {
    background: rgba(30, 33, 57, 0.5);
    border: 1px solid rgba(139, 92, 246, 0.2);
}
td {
    padding: 10px;
    border: 1px solid rgba(139, 92, 246, 0.2);
}
</style>


