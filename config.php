<?php
class config {
    private static $pdo = null;
    
    public static function getConnexion() {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "espritdonmsv";
            
            try {
                self::$pdo = new PDO(
                    "mysql:host=$servername;dbname=$dbname",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                    ]
                );
            } catch (PDOException $e) {
                die('Erreur de connexion: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
    
    // ==================== CONFIGURATION DES DEVISES ====================
    private static $currencies = [
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'symbol_position' => 'after',
            'decimal_places' => 2,
            'thousands_separator' => ' ',
            'decimal_separator' => ',',
            'exchange_rate' => 1.0,
            'flag' => '🇪🇺',
            'default' => true
        ],
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'symbol_position' => 'before',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'exchange_rate' => 1.08,
            'flag' => '🇺🇸',
            'default' => false
        ],
        'TND' => [
            'name' => 'Dinar Tunisien',
            'symbol' => 'DT',
            'symbol_position' => 'after',
            'decimal_places' => 3,
            'thousands_separator' => ' ',
            'decimal_separator' => ',',
            'exchange_rate' => 3.35,
            'flag' => '🇹🇳',
            'default' => false
        ]
    ];
    
    /**
     * Obtenir toutes les devises
     */
    public static function getAllCurrencies() {
        return self::$currencies;
    }
    
    /**
     * Obtenir une devise spécifique
     */
    public static function getCurrency($code) {
        return self::$currencies[$code] ?? self::$currencies['EUR'];
    }
    
    /**
     * Convertir un montant entre devises
     */
    public static function convertCurrency($amount, $fromCurrency, $toCurrency) {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        
        $from = self::getCurrency($fromCurrency);
        $to = self::getCurrency($toCurrency);
        
        // Convertir en EUR d'abord
        $amountInEUR = $amount / $from['exchange_rate'];
        
        // Convertir vers la devise cible
        $convertedAmount = $amountInEUR * $to['exchange_rate'];
        
        return round($convertedAmount, $to['decimal_places']);
    }
    
    /**
     * Formater un montant selon la devise
     */
    public static function formatMoney($amount, $currencyCode, $includeSymbol = true) {
        $currency = self::getCurrency($currencyCode);
        
        $roundedAmount = round($amount, $currency['decimal_places']);
        
        $formatted = number_format(
            $roundedAmount,
            $currency['decimal_places'],
            $currency['decimal_separator'],
            $currency['thousands_separator']
        );
        
        if ($includeSymbol) {
            if ($currency['symbol_position'] === 'before') {
                $formatted = $currency['symbol'] . ' ' . $formatted;
            } else {
                $formatted = $formatted . ' ' . $currency['symbol'];
            }
        }
        
        return $formatted;
    }
    
    /**
     * Obtenir la devise par défaut
     */
    public static function getDefaultCurrency() {
        foreach (self::$currencies as $code => $currency) {
            if ($currency['default'] === true) {
                return $code;
            }
        }
        return 'EUR';
    }
}
?>