<?php

/**
 * Service de réputation très simple.
 * Tu pourras l'enrichir plus tard avec des vraies stats.
 */
class ReputationService
{
    /**
     * Calcule un score de réputation fictif pour un utilisateur.
     * Pour l’instant, c’est juste un exemple.
     */
    public function computeUserScore(int $userId): int
    {
        // Exemple : un score de base qui diminue avec l'ID
        $score = 100 - ($userId * 2);

        return max(0, min(100, $score));
    }

    /**
     * Retourne un label textuel selon le score.
     */
    public function getReputationLabel(int $score): string
    {
        if ($score >= 80) {
            return "Légende de la communauté";
        }
        if ($score >= 50) {
            return "Membre actif";
        }
        if ($score > 0) {
            return "Nouveau membre";
        }
        return "Aucune activité";
    }
}
