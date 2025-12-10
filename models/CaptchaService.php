<?php
// Completely disable error display
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

/**
 * CAPTCHA Service for Google reCAPTCHA v2
 * 
 * To use this service, you need to:
 * 1. Get reCAPTCHA keys from https://www.google.com/recaptcha/admin
 * 2. Set RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY in config or environment
 * 
 * For PHP 5.3 compatibility, we use file_get_contents with stream context
 */
class CaptchaService {
    
    // reCAPTCHA API endpoint
    private $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    
    // Site key (public key) - to be set in config
    private $siteKey;
    
    // Secret key (private key) - to be set in config
    private $secretKey;
    
    public function __construct() {
        // Get keys from config file or set defaults
        // In production, these should be in a config file outside web root
        $this->siteKey = defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '';
        $this->secretKey = defined('RECAPTCHA_SECRET_KEY') ? RECAPTCHA_SECRET_KEY : '';
        
        // If not defined, try to get from a config file
        $configFile = __DIR__ . '/../config/recaptcha_config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
            if (isset($RECAPTCHA_SITE_KEY)) {
                $this->siteKey = $RECAPTCHA_SITE_KEY;
            }
            if (isset($RECAPTCHA_SECRET_KEY)) {
                $this->secretKey = $RECAPTCHA_SECRET_KEY;
            }
        }
    }
    
    /**
     * Get the site key for frontend use
     */
    public function getSiteKey() {
        return $this->siteKey;
    }
    
    /**
     * Verify reCAPTCHA response
     * 
     * @param string $response The g-recaptcha-response from the form
     * @param string $remoteIp Optional: User's IP address
     * @return array Array with 'success' (bool) and 'message' (string)
     */
    public function verify($response, $remoteIp = null) {
        if (empty($this->secretKey)) {
            error_log("reCAPTCHA secret key not configured");
            return array(
                'success' => false,
                'message' => 'CAPTCHA verification is not properly configured. Please contact administrator.'
            );
        }
        
        if (empty($response)) {
            return array(
                'success' => false,
                'message' => 'Please complete the CAPTCHA to verify you are human.'
            );
        }
        
        // Get user IP if not provided
        if ($remoteIp === null) {
            $remoteIp = $this->getClientIp();
        }
        
        // Prepare POST data
        $postData = array(
            'secret' => $this->secretKey,
            'response' => $response,
            'remoteip' => $remoteIp
        );
        
        // Build query string (PHP 5.3 compatible)
        $postString = http_build_query($postData);
        
        // Create stream context for POST request
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . "\r\n" .
                           'Content-Length: ' . strlen($postString) . "\r\n",
                'content' => $postString,
                'timeout' => 10
            )
        ));
        
        // Make request to Google reCAPTCHA API
        $result = @file_get_contents($this->verifyUrl, false, $context);
        
        if ($result === false) {
            error_log("Failed to connect to reCAPTCHA API");
            return array(
                'success' => false,
                'message' => 'Unable to verify CAPTCHA. Please try again.'
            );
        }
        
        // Decode JSON response (PHP 5.3 compatible)
        $resultData = json_decode($result, true);
        
        if ($resultData === null) {
            error_log("Invalid JSON response from reCAPTCHA API: " . $result);
            return array(
                'success' => false,
                'message' => 'Invalid response from CAPTCHA service. Please try again.'
            );
        }
        
        // Check if verification was successful
        if (isset($resultData['success']) && $resultData['success'] === true) {
            return array(
                'success' => true,
                'message' => 'CAPTCHA verified successfully'
            );
        } else {
            $errorCodes = isset($resultData['error-codes']) ? $resultData['error-codes'] : array();
            error_log("reCAPTCHA verification failed. Error codes: " . implode(', ', $errorCodes));
            
            return array(
                'success' => false,
                'message' => 'CAPTCHA verification failed. Please try again.',
                'error_codes' => $errorCodes
            );
        }
    }
    
    /**
     * Get client IP address
     */
    private function getClientIp() {
        $ipKeys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
    
    /**
     * Check if CAPTCHA is required based on failed attempts
     * 
     * @param int $failedAttempts Number of failed attempts
     * @param int $threshold Threshold for requiring CAPTCHA (default: 5)
     * @return bool
     */
    public function isRequired($failedAttempts, $threshold = 5) {
        return $failedAttempts >= $threshold;
    }
}

?>

