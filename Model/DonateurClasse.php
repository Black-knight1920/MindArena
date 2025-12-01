<?php
// Model/DonateurClasse.php
class DonateurClasse {
    
    const CLASSES = [
        'Novice' => ['seuil' => 0, 'badge' => '🟢', 'couleur' => '#00FF00'],
        'Bénévole' => ['seuil' => 500, 'badge' => '🔵', 'couleur' => '#0066FF'],
        'Partenaire' => ['seuil' => 2000, 'badge' => '🟣', 'couleur' => '#9900FF'],
        'Mécène' => ['seuil' => 10000, 'badge' => '🟠', 'couleur' => '#FF6600'],
        'Légende' => ['seuil' => 50000, 'badge' => '⚡', 'couleur' => '#FF0000']
    ];
    
    public static function getClasse($montantTotal) {
        $classeTrouvee = 'Novice';
        foreach (self::CLASSES as $nom => $infos) {
            if ($montantTotal >= $infos['seuil']) {
                $classeTrouvee = $nom;
            }
        }
        return $classeTrouvee;
    }
    
    public static function getInfosClasse($classe) {
        return self::CLASSES[$classe] ?? self::CLASSES['Novice'];
    }
    
    public static function getProchainPalier($montantTotal) {
        $classeActuelle = self::getClasse($montantTotal);
        
        $classes = array_keys(self::CLASSES);
        $indexActuel = array_search($classeActuelle, $classes);
        
        if ($indexActuel < count($classes) - 1) {
            return $classes[$indexActuel + 1];
        }
        return null; // Déjà au plus haut niveau
    }
    
    public static function getProgression($montantTotal) {
        $classeActuelle = self::getClasse($montantTotal);
        $prochaineClasse = self::getProchainPalier($montantTotal);
        
        if (!$prochaineClasse) return 100; // Déjà Légende
        
        $seuilActuel = self::CLASSES[$classeActuelle]['seuil'];
        $seuilProchain = self::CLASSES[$prochaineClasse]['seuil'];
        
        $progression = (($montantTotal - $seuilActuel) / ($seuilProchain - $seuilActuel)) * 100;
        return min(100, max(0, $progression));
    }
}
?>