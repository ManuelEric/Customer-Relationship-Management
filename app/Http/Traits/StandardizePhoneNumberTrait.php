<?php

namespace App\Http\Traits;

trait StandardizePhoneNumberTrait 
{
    /**
     * Normalize Indonesian phone numbers to international format (+62)
     * 
     * @param string|null $phoneNumber Raw phone number input
     * @return string|null Normalized phone number or null if invalid
     */
    public function tnNormalizePhoneNumber($phoneNumber)
    {
        // Return null for empty input
        if (!$phoneNumber || trim($phoneNumber) === '') {
            return null;
        }
        
        // Convert to string and trim whitespace
        $phoneNumber = trim((string)$phoneNumber);
        
        // Check for invalid patterns before processing
        if ($this->isInvalidPhoneNumber($phoneNumber)) {
            return null;
        }
        
        // Remove common separators and formatting characters
        $phoneNumber = str_replace([',', '-', ' ', '.', '(', ')', '_'], '', $phoneNumber);
        
        // Remove any remaining non-digit characters except + at the beginning
        $phoneNumber = preg_replace('/[^\d+]/', '', $phoneNumber);
        
        // Handle empty result after cleaning
        if (empty($phoneNumber)) {
            return null;
        }
        
        // Check for double plus signs or invalid patterns after cleaning
        if (substr_count($phoneNumber, '+') > 1 || 
            (strpos($phoneNumber, '+') !== false && strpos($phoneNumber, '+') !== 0)) {
            return null;
        }
        
        // Basic length validation before processing
        if (strlen($phoneNumber) < 3 || strlen($phoneNumber) > 16) {
            return null;
        }
        
        // Normalize Indonesian numbers
        if (substr($phoneNumber, 0, 1) === '+') {
            // Already has country code
            if (substr($phoneNumber, 0, 3) === '+62') {
                return $this->validateIndonesianNumber($phoneNumber);
            }
            // Other country codes - basic validation
            if (strlen($phoneNumber) >= 8 && strlen($phoneNumber) <= 15) {
                return $phoneNumber;
            }
            return null;
            
        } elseif (substr($phoneNumber, 0, 2) === '62') {
            // Starts with 62 (country code without +)
            $phoneNumber = '+' . $phoneNumber;
            return $this->validateIndonesianNumber($phoneNumber);
            
        } elseif (substr($phoneNumber, 0, 1) === '0') {
            // Indonesian local format starting with 0
            $phoneNumber = '+62' . substr($phoneNumber, 1);
            return $this->validateIndonesianNumber($phoneNumber);
            
        } else {
            // No country code, assume Indonesian local number
            $phoneNumber = '+62' . $phoneNumber;
            return $this->validateIndonesianNumber($phoneNumber);
        }
    }

    /**
     * Check if phone number has invalid patterns
     */
    private function isInvalidPhoneNumber($phoneNumber)
    {
        // Check for double plus signs
        if (substr_count($phoneNumber, '+') > 1) {
            return true;
        }
        
        // Check for plus sign not at the beginning
        if (strpos($phoneNumber, '+') !== false && strpos($phoneNumber, '+') !== 0) {
            return true;
        }
        
        // Check if it's mostly non-digit characters
        $digitsOnly = preg_replace('/[^\d]/', '', $phoneNumber);
        if (strlen($digitsOnly) < 3) {
            return true;
        }
        
        // Check for invalid patterns (all same digits, etc.)
        if (preg_match('/^[\+\-\s\(\)\.\_\,]*$/', $phoneNumber)) {
            return true;
        }
        
        return false;
    }

    /**
     * Validate Indonesian phone number format
     */
    private function validateIndonesianNumber($phoneNumber)
    {
        // Must start with +62
        if (substr($phoneNumber, 0, 3) !== '+62') {
            return null;
        }
        
        // Length validation for Indonesian numbers
        if (strlen($phoneNumber) < 11 || strlen($phoneNumber) > 15) {
            return null;
        }
        
        $localPart = substr($phoneNumber, 3); // Remove +62
        
        // Check if it starts with valid Indonesian mobile prefixes
        $validMobilePrefixes = ['8']; // Mobile numbers start with 8
        $firstDigit = substr($localPart, 0, 1);
        
        // Reject landline prefixes (2, 3, 4, 5, 6)
        $landlinePrefixes = ['2', '3', '4', '5', '6'];
        if (in_array($firstDigit, $landlinePrefixes)) {
            return null;
        }
        
        // Accept mobile prefixes
        if (in_array($firstDigit, $validMobilePrefixes)) {
            return $phoneNumber;
        }
        
        // Also accept 7 prefix for some providers
        if ($firstDigit === '7') {
            return $phoneNumber;
        }
        
        // Invalid prefix
        return null;
    }
}