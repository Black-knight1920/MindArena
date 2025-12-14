<?php
class EmailNotification {
    private $from = 'noreply@espritdon.com';
    private $fromName = 'Esprit Don';
    
    /**
     * Envoyer une confirmation de don par email
     * 
     * @param string $emailDonateur Email du donateur
     * @param string $nomDonateur Nom du donateur
     * @param string $prenomDonateur Prénom du donateur
     * @param float $montant Montant du don
     * @param string $organisationNom Nom de l'organisation
     * @param DateTime $dateDon Date du don
     * @param string $langue Code de la langue (fr, en, es, etc.)
     * @return bool True si l'email est envoyé avec succès
     */
    public function sendDonConfirmation(
        $emailDonateur,
        $nomDonateur,
        $prenomDonateur,
        $montant,
        $organisationNom,
        $dateDon,
        $langue = 'fr'
    ) {
        if (empty($emailDonateur) || !filter_var($emailDonateur, FILTER_VALIDATE_EMAIL)) {
            error_log("Email invalide: " . $emailDonateur);
            return false;
        }
        
        $sujet = $this->getSujet($langue);
        $corps = $this->getCorpsEmail($nomDonateur, $prenomDonateur, $montant, $organisationNom, $dateDon, $langue);
        
        return $this->sendEmail($emailDonateur, $sujet, $corps);
    }
    
    /**
     * Envoyer une notification à l'organisation
     * 
     * @param string $emailOrganisation Email de l'organisation
     * @param string $organisationNom Nom de l'organisation
     * @param string $nomDonateur Nom du donateur
     * @param string $prenomDonateur Prénom du donateur
     * @param float $montant Montant du don
     * @param DateTime $dateDon Date du don
     * @param string $langue Code de la langue
     * @return bool True si l'email est envoyé avec succès
     */
    public function sendOrganisationNotification(
        $emailOrganisation,
        $organisationNom,
        $nomDonateur,
        $prenomDonateur,
        $montant,
        $dateDon,
        $langue = 'fr'
    ) {
        if (empty($emailOrganisation) || !filter_var($emailOrganisation, FILTER_VALIDATE_EMAIL)) {
            error_log("Email organisation invalide: " . $emailOrganisation);
            return false;
        }
        
        $sujet = $this->getSujetOrganisation($langue);
        $corps = $this->getCorpsEmailOrganisation($organisationNom, $nomDonateur, $prenomDonateur, $montant, $dateDon, $langue);
        
        return $this->sendEmail($emailOrganisation, $sujet, $corps);
    }
    
    /**
     * Envoyer un email générique
     * 
     * @param string $destinataire Email du destinataire
     * @param string $sujet Sujet de l'email
     * @param string $corps Corps de l'email (HTML)
     * @return bool True si l'email est envoyé
     */
    private function sendEmail($destinataire, $sujet, $corps) {
        try {
            // En-têtes de l'email
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: {$this->fromName} <{$this->from}>\r\n";
            $headers .= "Reply-To: {$this->from}\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            
            // Envoi de l'email
            $result = mail($destinataire, $sujet, $corps, $headers);
            
            if ($result) {
                error_log("Email envoyé avec succès à: " . $destinataire);
            } else {
                error_log("Erreur lors de l'envoi de l'email à: " . $destinataire);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Exception lors de l'envoi d'email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir le sujet de l'email de confirmation du don
     */
    private function getSujet($langue) {
        $sujets = [
            'fr' => 'Confirmation de votre don - Esprit Don',
            'en' => 'Donation Confirmation - Esprit Don',
            'es' => 'Confirmación de su donación - Esprit Don',
            'de' => 'Spendenbestätigung - Esprit Don',
            'it' => 'Conferma della donazione - Esprit Don',
            'pt' => 'Confirmação da sua doação - Esprit Don'
        ];
        
        return $sujets[$langue] ?? $sujets['fr'];
    }
    
    /**
     * Obtenir le sujet de l'email de notification à l'organisation
     */
    private function getSujetOrganisation($langue) {
        $sujets = [
            'fr' => 'Nouvelle donation reçue',
            'en' => 'New donation received',
            'es' => 'Nueva donación recibida',
            'de' => 'Neue Spende erhalten',
            'it' => 'Nuova donazione ricevuta',
            'pt' => 'Nova doação recebida'
        ];
        
        return $sujets[$langue] ?? $sujets['fr'];
    }
    
    /**
     * Obtenir le corps de l'email de confirmation du don
     */
    private function getCorpsEmail($nomDonateur, $prenomDonateur, $montant, $organisationNom, $dateDon, $langue) {
        $titre = match($langue) {
            'en' => 'Thank you for your donation!',
            'es' => '¡Gracias por tu donación!',
            'de' => 'Vielen Dank für Ihre Spende!',
            'it' => 'Grazie per la tua donazione!',
            'pt' => 'Obrigado pela sua doação!',
            default => 'Merci pour votre don!'
        };
        
        $message1 = match($langue) {
            'en' => 'We confirm the receipt of your donation of',
            'es' => 'Confirmamos la recepción de su donación de',
            'de' => 'Wir bestätigen den Erhalt Ihrer Spende von',
            'it' => 'Confirmiamo la ricezione della tua donazione di',
            'pt' => 'Confirmamos o recebimento de sua doação de',
            default => 'Nous confirmons la réception de votre don de'
        };
        
        $message2 = match($langue) {
            'en' => 'to the benefit of',
            'es' => 'en beneficio de',
            'de' => 'zugunsten von',
            'it' => 'a beneficio di',
            'pt' => 'em benefício de',
            default => 'au bénéfice de'
        };
        
        $donateur = trim($prenomDonateur . ' ' . $nomDonateur);
        $dateFormatee = $dateDon->format('d/m/Y');
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-content {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
        }
        .content {
            margin: 20px 0;
        }
        .confirmation-box {
            background-color: #f0f8f0;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            text-align: right;
            color: #4CAF50;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
        }
        .thank-you {
            text-align: center;
            margin-top: 20px;
            color: #4CAF50;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-content">
            <div class="header">
                <h1>🎁 {$titre}</h1>
            </div>
            
            <div class="content">
                <p>Chère/Cher {$donateur},</p>
                
                <div class="confirmation-box">
                    <p><strong>{$message1}</strong></p>
                    
                    <div class="detail-row">
                        <span class="detail-label">Montant:</span>
                        <span class="detail-value">{$montant} €</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">{$message2}:</span>
                        <span class="detail-value">{$organisationNom}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Date du don:</span>
                        <span class="detail-value">{$dateFormatee}</span>
                    </div>
                </div>
            </div>
            
            <div class="thank-you">
                <p>Votre contribution aide à faire une différence. Merci!</p>
            </div>
            
            <div class="footer">
                <p>Esprit Don - Association de charité</p>
                <p>Pour toute question, contactez-nous: contact@espritdon.com</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $html;
    }
    
    /**
     * Obtenir le corps de l'email de notification à l'organisation
     */
    private function getCorpsEmailOrganisation($organisationNom, $nomDonateur, $prenomDonateur, $montant, $dateDon, $langue) {
        $titre = match($langue) {
            'en' => 'New Donation Received',
            'es' => 'Nueva Donación Recibida',
            'de' => 'Neue Spende Erhalten',
            'it' => 'Nuova Donazione Ricevuta',
            'pt' => 'Nova Doação Recebida',
            default => 'Nouvelle Donation Reçue'
        };
        
        $donateur = trim($prenomDonateur . ' ' . $nomDonateur);
        $dateFormatee = $dateDon->format('d/m/Y');
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-content {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2196F3;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2196F3;
            margin: 0;
        }
        .donation-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            text-align: right;
            color: #2196F3;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-content">
            <div class="header">
                <h1>{$titre}</h1>
            </div>
            
            <div class="content">
                <p>Chère organisation {$organisationNom},</p>
                
                <p>Vous avez reçu une nouvelle donation. Veuillez trouver les détails ci-dessous:</p>
                
                <div class="donation-box">
                    <div class="detail-row">
                        <span class="detail-label">Donateur:</span>
                        <span class="detail-value">{$donateur}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Montant:</span>
                        <span class="detail-value">{$montant} €</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">{$dateFormatee}</span>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                <p>Esprit Don - Gestion des donations</p>
                <p>Message automatisé</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $html;
    }
}
?>
