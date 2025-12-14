<?php
// LanguageHelper.php
class LanguageHelper {
    private static $instance = null;
    private $currentLang = 'fr';
    private $translations = [];
    private $supportedLanguages = [
        'fr' => [
            'name' => 'Français',
            'native' => 'Français',
            'flag' => '🇫🇷',
            'direction' => 'ltr'
        ],
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇬🇧',
            'direction' => 'ltr'
        ],
        'es' => [
            'name' => 'Español',
            'native' => 'Español',
            'flag' => '🇪🇸',
            'direction' => 'ltr'
        ],
        'de' => [
            'name' => 'Deutsch',
            'native' => 'Deutsch',
            'flag' => '🇩🇪',
            'direction' => 'ltr'
        ],
        'it' => [
            'name' => 'Italiano',
            'native' => 'Italiano',
            'flag' => '🇮🇹',
            'direction' => 'ltr'
        ],
        'pt' => [
            'name' => 'Português',
            'native' => 'Português',
            'flag' => '🇵🇹',
            'direction' => 'ltr'
        ]
    ];

    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->loadLanguage();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadLanguage() {
        // Vérifier le paramètre GET
        if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $this->supportedLanguages)) {
            $this->currentLang = $_GET['lang'];
            $_SESSION['lang'] = $this->currentLang;
        }
        // Vérifier la session
        elseif (isset($_SESSION['lang']) && array_key_exists($_SESSION['lang'], $this->supportedLanguages)) {
            $this->currentLang = $_SESSION['lang'];
        }
        // Vérifier le navigateur
        else {
            $this->currentLang = $this->getBrowserLanguage();
        }

        // Charger les fichiers de traduction
        $this->loadTranslations();
    }

    private function getBrowserLanguage() {
        $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr';
        $lang = substr($acceptLang, 0, 2);
        return array_key_exists($lang, $this->supportedLanguages) ? $lang : 'fr';
    }

    private function loadTranslations() {
        $langDir = __DIR__ . '/lang';
        $langFile = $langDir . '/' . $this->currentLang . '.php';
        $fallbackFile = $langDir . '/fr.php';

        if (file_exists($langFile)) {
            $this->translations = require $langFile;
        } elseif (file_exists($fallbackFile)) {
            $this->translations = require $fallbackFile;
            $this->currentLang = 'fr';
        } else {
            $this->translations = [];
        }
    }

    public function translate($key, $params = []) {
        $translation = $this->translations[$key] ?? $key;
        
        // Remplacer les paramètres
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $translation = str_replace(':' . $param, $value, $translation);
            }
        }
        
        return $translation;
    }

    public function plural($key, $count, $params = []) {
        // Implémentation simple du pluriel
        $translation = $this->translate($key, $params);
        return $translation;
    }

    public function getCurrentLang() {
        return $this->currentLang;
    }

    public function getSupportedLanguages() {
        return $this->supportedLanguages;
    }

    // Informations de devise selon la langue (EN => USD, autres => EUR)
    public function getCurrencyInfo() {
        if ($this->currentLang === 'en') {
            return [
                'code' => 'USD',
                'symbol' => '$',
                'name' => 'US Dollar',
                'locale' => 'en_US'
            ];
        }

        return [
            'code' => 'EUR',
            'symbol' => '€',
            'name' => 'Euro',
            'locale' => 'fr_FR'
        ];
    }

    /**
     * Convertit un montant stocké en EUR vers la devise d'affichage (USD si EN)
     */
    public function convertFromEUR($amount) {
        if ($this->currentLang === 'en') {
            return $amount * 1.16; // EUR -> USD
        }
        return $amount; // EUR -> EUR
    }

    /**
     * Convertit un montant saisi en devise d'affichage vers EUR (stockage BD)
     */
    public function convertToEUR($amount) {
        if ($this->currentLang === 'en') {
            return $amount * 0.86; // USD -> EUR
        }
        return $amount; // EUR -> EUR
    }

    /**
     * Formate un montant déjà exprimé dans la devise d'affichage
     */
    public function formatMoneyDisplay($amount) {
        $currency = $this->getCurrencyInfo();
        if ($currency['code'] === 'USD') {
            return $currency['symbol'] . number_format($amount, 2, '.', ',');
        }
        return number_format($amount, 2, ',', ' ') . ' ' . $currency['symbol'];
    }

    /**
     * Formate un montant stocké en EUR pour l'affichage (avec conversion éventuelle)
     */
    public function formatMoneyFromEUR($amount) {
        $converted = $this->convertFromEUR($amount);
        return $this->formatMoneyDisplay($converted);
    }

    /**
     * Formate un montant déjà dans la devise courante (alias compatibilité)
     */
    public function formatMoney($amount) {
        return $this->formatMoneyDisplay($amount);
    }
}