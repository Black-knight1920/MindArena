<?php
/**
 * LanguageHelper - Gestion multilingue et multi-devises SANS base de données
 */
class LanguageHelper {
    private static $instance = null;
    private $currentLang = 'fr';
    private $translations = [];
    private $currencyConfig = [];
    
    // Taux de change (à mettre à jour périodiquement)
    private $exchangeRates = [
        'EUR' => 1.00,   // Euro (référence)
        'USD' => 1.10,   // Dollar US
        'GBP' => 0.85,   // Livre Sterling
        'CAD' => 1.45,   // Dollar Canadien
        'CHF' => 0.95,   // Franc Suisse
        'JPY' => 160.00, // Yen Japonais
        'AUD' => 1.65,   // Dollar Australien
    ];
    
    private function __construct() {
        session_start();
        $this->detectLanguage();
        $this->loadLanguageFiles();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Détecte et définit la langue active
     */
    private function detectLanguage() {
        // 1. Priorité au paramètre URL
        if (isset($_GET['lang']) && $this->isValidLanguage($_GET['lang'])) {
            $this->currentLang = $_GET['lang'];
            $_SESSION['user_lang'] = $this->currentLang;
        }
        // 2. Priorité à la session
        elseif (isset($_SESSION['user_lang']) && $this->isValidLanguage($_SESSION['user_lang'])) {
            $this->currentLang = $_SESSION['user_lang'];
        }
        // 3. Détection du navigateur
        else {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr', 0, 2);
            $this->currentLang = $this->isValidLanguage($browserLang) ? $browserLang : 'fr';
        }
    }
    
    /**
     * Vérifie si la langue est supportée
     */
    private function isValidLanguage($lang) {
        $supported = ['fr', 'en', 'es', 'de', 'it', 'pt'];
        return in_array($lang, $supported);
    }
    
    /**
     * Charge les fichiers de langue
     */
    private function loadLanguageFiles() {
        $langDir = __DIR__ . '/lang/';
        
        // Fichier principal de la langue
        $mainFile = $langDir . $this->currentLang . '.php';
        if (file_exists($mainFile)) {
            $this->translations = require $mainFile;
        } else {
            // Fallback en français
            $fallbackFile = $langDir . 'fr.php';
            $this->translations = require $fallbackFile;
        }
        
        // Configuration devise
        $this->currencyConfig = [
            'symbol' => $this->translations['currency_symbol'] ?? '€',
            'code' => $this->translations['currency_code'] ?? 'EUR',
            'decimal' => $this->translations['decimal_separator'] ?? ',',
            'thousands' => $this->translations['thousands_separator'] ?? ' ',
        ];
    }
    
    /**
     * Traduction simple
     */
    public function translate($key, $params = []) {
        $text = $this->translations[$key] ?? $key;
        
        // Remplacer les paramètres dynamiques
        foreach ($params as $param => $value) {
            $text = str_replace(':' . $param, $value, $text);
        }
        
        return $text;
    }
    
    /**
     * Alias pour translate()
     */
    public function t($key, $params = []) {
        return $this->translate($key, $params);
    }
    
    /**
     * Formatage de devise
     */
    public function formatMoney($amount, $fromCurrency = 'EUR') {
        $toCurrency = $this->currencyConfig['code'];
        $converted = $this->convertCurrency($amount, $fromCurrency, $toCurrency);
        
        // Format selon la devise
        switch ($toCurrency) {
            case 'USD':
            case 'CAD':
            case 'AUD':
            case 'GBP':
                return $this->currencyConfig['symbol'] . number_format(
                    $converted, 
                    2, 
                    '.', 
                    ','
                );
                
            case 'JPY':
                return $this->currencyConfig['symbol'] . number_format(
                    $converted, 
                    0, 
                    '', 
                    ','
                );
                
            case 'CHF':
                return number_format(
                    $converted, 
                    2, 
                    '.', 
                    "'"
                ) . ' ' . $this->currencyConfig['symbol'];
                
            default: // EUR et autres
                return number_format(
                    $converted, 
                    2, 
                    $this->currencyConfig['decimal'], 
                    $this->currencyConfig['thousands']
                ) . ' ' . $this->currencyConfig['symbol'];
        }
    }
    
    /**
     * Conversion de devise
     */
    private function convertCurrency($amount, $from, $to) {
        if ($from === $to || !isset($this->exchangeRates[$from]) || !isset($this->exchangeRates[$to])) {
            return $amount;
        }
        
        // Conversion via EUR comme devise pivot
        $amountInEur = $amount / $this->exchangeRates[$from];
        return $amountInEur * $this->exchangeRates[$to];
    }
    
    /**
     * Formatage de date selon la locale
     */
    public function formatDate($dateString, $format = 'medium') {
        $date = new DateTime($dateString);
        
        $formats = [
            'fr' => [
                'short' => 'd/m/Y',
                'medium' => 'd M Y',
                'long' => 'd F Y',
                'full' => 'l d F Y',
            ],
            'en' => [
                'short' => 'm/d/Y',
                'medium' => 'M d, Y',
                'long' => 'F d, Y',
                'full' => 'l, F d, Y',
            ],
            'es' => [
                'short' => 'd/m/Y',
                'medium' => 'd M Y',
                'long' => 'd de F de Y',
                'full' => 'l d de F de Y',
            ],
        ];
        
        $dateFormat = $formats[$this->currentLang][$format] ?? $formats['en'][$format];
        
        // Traduction des mois/jours si nécessaire
        $formatted = $date->format($dateFormat);
        
        if ($this->currentLang !== 'en') {
            $englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 
                             'July', 'August', 'September', 'October', 'November', 'December'];
            $englishDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            
            $localMonths = $this->translations['months'] ?? $englishMonths;
            $localDays = $this->translations['days'] ?? $englishDays;
            
            $formatted = str_replace($englishMonths, $localMonths, $formatted);
            $formatted = str_replace($englishDays, $localDays, $formatted);
        }
        
        return $formatted;
    }
    
    /**
     * Récupère la langue courante
     */
    public function getCurrentLang() {
        return $this->currentLang;
    }
    
    /**
     * Liste des langues supportées
     */
    public function getSupportedLanguages() {
        return [
            'fr' => ['name' => 'Français', 'flag' => '🇫🇷', 'native' => 'Français'],
            'en' => ['name' => 'English', 'flag' => '🇺🇸', 'native' => 'English'],
            'es' => ['name' => 'Spanish', 'flag' => '🇪🇸', 'native' => 'Español'],
            'de' => ['name' => 'German', 'flag' => '🇩🇪', 'native' => 'Deutsch'],
            'it' => ['name' => 'Italian', 'flag' => '🇮🇹', 'native' => 'Italiano'],
            'pt' => ['name' => 'Portuguese', 'flag' => '🇵🇹', 'native' => 'Português'],
        ];
    }
    
    /**
     * Change la langue
     */
    public function setLanguage($lang) {
        if ($this->isValidLanguage($lang)) {
            $this->currentLang = $lang;
            $_SESSION['user_lang'] = $lang;
            $this->loadLanguageFiles();
            return true;
        }
        return false;
    }
    
    /**
     * Récupère la configuration devise
     */
    public function getCurrencyInfo() {
        return $this->currencyConfig;
    }
    
    /**
     * Traduction plurielle
     */
    public function plural($key, $count, $params = []) {
        $baseKey = $key . '_';
        
        if ($count == 0) {
            $key .= '_zero';
        } elseif ($count == 1) {
            $key .= '_one';
        } elseif ($count > 1) {
            $key .= '_many';
        }
        
        $text = $this->translate($key, array_merge(['count' => $count], $params));
        
        // Fallback si la clé spécifique n'existe pas
        if ($text === $key) {
            $text = $this->translate($baseKey . 'other', array_merge(['count' => $count], $params));
        }
        
        return $text;
    }
}
?>