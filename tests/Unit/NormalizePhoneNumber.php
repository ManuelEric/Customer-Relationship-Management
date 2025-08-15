<?php

// Test class that uses the trait
use App\Http\Traits\StandardizePhoneNumberTrait;

// Use the trait in tests by creating an anonymous class
beforeEach(function () {
    $this->phoneNormalizer = new class {
        use StandardizePhoneNumberTrait;
        
        public function normalize($phoneNumber) {
            return $this->tnNormalizePhoneNumber($phoneNumber);
        }
    };
});

describe('Phone Number Normalization Trait', function () {
    
    describe('Indonesian Local Formats', function () {
        it('normalizes basic local format', function () {
            expect($this->phoneNormalizer->normalize('08123456789'))->toBe('+628123456789');
        });
        
        it('normalizes format with dashes', function () {
            expect($this->phoneNormalizer->normalize('0812-3456-789'))->toBe('+628123456789');
        });
        
        it('normalizes format with spaces', function () {
            expect($this->phoneNormalizer->normalize('0812 3456 789'))->toBe('+628123456789');
        });
        
        it('normalizes format with parentheses', function () {
            expect($this->phoneNormalizer->normalize('(0812) 3456-789'))->toBe('+628123456789');
        });
        
        it('normalizes format with dots', function () {
            expect($this->phoneNormalizer->normalize('0812.3456.789'))->toBe('+628123456789');
        });
        
        it('normalizes format with underscores', function () {
            expect($this->phoneNormalizer->normalize('0812_3456_789'))->toBe('+628123456789');
        });
        
        it('normalizes format with commas', function () {
            expect($this->phoneNormalizer->normalize('0812,3456,789'))->toBe('+628123456789');
        });
    });

    describe('Indonesian Country Code Formats', function () {
        it('normalizes number with 62 prefix', function () {
            expect($this->phoneNormalizer->normalize('628123456789'))->toBe('+628123456789');
        });
        
        it('normalizes number with +62 prefix', function () {
            expect($this->phoneNormalizer->normalize('+628123456789'))->toBe('+628123456789');
        });
        
        it('normalizes formatted number with 62 prefix and dashes', function () {
            expect($this->phoneNormalizer->normalize('62-812-3456-789'))->toBe('+628123456789');
        });
        
        it('normalizes formatted number with 62 prefix and spaces', function () {
            expect($this->phoneNormalizer->normalize('62 812 3456 789'))->toBe('+628123456789');
        });
    });

    describe('Indonesian Mobile Without Leading Zero', function () {
        it('normalizes mobile number without zero', function () {
            expect($this->phoneNormalizer->normalize('8123456789'))->toBe('+628123456789');
        });
        
        it('normalizes formatted mobile number without zero', function () {
            expect($this->phoneNormalizer->normalize('812-3456-789'))->toBe('+628123456789');
        });
        
        it('normalizes spaced mobile number without zero', function () {
            expect($this->phoneNormalizer->normalize('812 3456 789'))->toBe('+628123456789');
        });
    });

    describe('International Numbers', function () {
        it('handles US number', function () {
            expect($this->phoneNormalizer->normalize('+1234567890'))->toBe('+1234567890');
        });
        
        it('handles UK number', function () {
            expect($this->phoneNormalizer->normalize('+447123456789'))->toBe('+447123456789');
        });
        
        it('handles China number', function () {
            expect($this->phoneNormalizer->normalize('+8613800138000'))->toBe('+8613800138000');
        });
        
        it('handles France number', function () {
            expect($this->phoneNormalizer->normalize('+33123456789'))->toBe('+33123456789');
        });
    });

    describe('Null and Empty Inputs', function () {
        it('returns null for null input', function () {
            expect($this->phoneNormalizer->normalize(null))->toBeNull();
        });
        
        it('returns null for empty string', function () {
            expect($this->phoneNormalizer->normalize(''))->toBeNull();
        });
        
        it('returns null for whitespace only', function () {
            expect($this->phoneNormalizer->normalize('   '))->toBeNull();
        });
        
        it('returns null for tabs and newlines', function () {
            expect($this->phoneNormalizer->normalize("\t\n "))->toBeNull();
        });
    });

    describe('Invalid Phone Numbers', function () {
        it('returns null for alphabetic characters', function () {
            expect($this->phoneNormalizer->normalize('abc123'))->toBeNull();
        });
        
        it('returns null for too short number', function () {
            expect($this->phoneNormalizer->normalize('123'))->toBeNull();
        });
        
        it('returns null for too long number', function () {
            expect($this->phoneNormalizer->normalize('+1234567890123456'))->toBeNull();
        });
        
        it('returns null for invalid Indonesian prefix', function () {
            expect($this->phoneNormalizer->normalize('06123456789'))->toBeNull();
        });
        
        it('returns null for too short Indonesian number', function () {
            expect($this->phoneNormalizer->normalize('+6212345'))->toBeNull();
        });
        
        it('returns null for special characters only', function () {
            expect($this->phoneNormalizer->normalize('!!!@@@###'))->toBeNull();
        });
        
        it('returns null for mixed invalid characters', function () {
            expect($this->phoneNormalizer->normalize('phone123number'))->toBeNull();
        });
        
        it('returns null for double plus', function () {
            expect($this->phoneNormalizer->normalize('++628123456789'))->toBeNull();
        });
    });

    describe('Special Characters Removal', function () {
        it('removes special characters and normalizes', function () {
            expect($this->phoneNormalizer->normalize('0812@#$%3456789'))->toBe('+628123456789');
        });
        
        it('removes complex formatting', function () {
            expect($this->phoneNormalizer->normalize('(0812)&*()3456789'))->toBe('+628123456789');
        });
        
        it('handles international with special chars', function () {
            expect($this->phoneNormalizer->normalize('+62-812-345-6789'))->toBe('+628123456789');
        });
        
        it('normalizes with mixed separators', function () {
            expect($this->phoneNormalizer->normalize('0812 - 345 - 6789'))->toBe('+628123456789');
        });
    });

    describe('Indonesian Landline Rejection', function () {
        it('rejects Jakarta landline', function () {
            expect($this->phoneNormalizer->normalize('02112345678'))->toBeNull();
        });
        
        it('rejects Jakarta landline with country code', function () {
            expect($this->phoneNormalizer->normalize('+622112345678'))->toBeNull();
        });
        
        it('rejects Surabaya landline', function () {
            expect($this->phoneNormalizer->normalize('0311234567'))->toBeNull();
        });
    });

    describe('Indonesian Carrier Prefixes', function () {
        test('Telkomsel prefixes', function () {
            $telkomselPrefixes = ['0811', '0812', '0813', '0821', '0822'];
            
            foreach ($telkomselPrefixes as $prefix) {
                $phoneNumber = $prefix . '1234567';
                $expected = '+62' . substr($prefix, 1) . '1234567';
                
                expect($this->phoneNormalizer->normalize($phoneNumber))->toBe($expected);
            }
        });
        
        test('Indosat prefixes', function () {
            $indosatPrefixes = ['0814', '0815', '0816', '0855', '0856', '0857', '0858'];
            
            foreach ($indosatPrefixes as $prefix) {
                $phoneNumber = $prefix . '1234567';
                $expected = '+62' . substr($prefix, 1) . '1234567';
                
                expect($this->phoneNormalizer->normalize($phoneNumber))->toBe($expected);
            }
        });
        
        test('XL Axiata prefixes', function () {
            $xlPrefixes = ['0817', '0818', '0819', '0859', '0877', '0878'];
            
            foreach ($xlPrefixes as $prefix) {
                $phoneNumber = $prefix . '1234567';
                $expected = '+62' . substr($prefix, 1) . '1234567';
                
                expect($this->phoneNormalizer->normalize($phoneNumber))->toBe($expected);
            }
        });
    });

    describe('Performance Tests', function () {
        it('processes large dataset efficiently', function () {
            $startTime = microtime(true);
            
            // Test with 1000 phone numbers
            for ($i = 0; $i < 1000; $i++) {
                $phoneNumber = '0812345678' . sprintf('%02d', $i % 100);
                $result = $this->phoneNormalizer->normalize($phoneNumber);
                expect($result)->not->toBeNull();
            }
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            expect($executionTime)->toBeLessThan(1.0);
        });
    });
});

// Dataset testing with trait
describe('Dataset Testing with Trait', function () {
    it('normalizes valid phone numbers correctly', function ($input, $expected) {
        expect($this->phoneNormalizer->normalize($input))->toBe($expected);
    })->with([
        'Indonesian with leading zero' => ['08123456789', '+628123456789'],
        'Indonesian with dashes' => ['0812-3456-789', '+628123456789'],
        'Indonesian with spaces' => ['0812 3456 789', '+628123456789'],
        'Indonesian with parentheses' => ['(0812) 3456-789', '+628123456789'],
        'Indonesian country code' => ['628123456789', '+628123456789'],
        'Indonesian with plus' => ['+628123456789', '+628123456789'],
        'Indonesian mobile only' => ['8123456789', '+628123456789'],
        'US number' => ['+1234567890', '+1234567890'],
        'UK number' => ['+447123456789', '+447123456789'],
    ]);
    
    it('returns null for invalid inputs', function ($input) {
        expect($this->phoneNormalizer->normalize($input))->toBeNull();
    })->with([
        'null' => [null],
        'empty string' => [''],
        'whitespace' => ['   '],
        'alphabetic' => ['abc123'],
        'too short' => ['123'],
        'too long' => ['+1234567890123456'],
        'invalid prefix' => ['06123456789'],
        'special chars only' => ['!!!@@@###'],
    ]);
});

// Alternative approach: Testing a class that uses the trait
class PhoneService
{
    use StandardizePhoneNumberTrait;
    
    public function normalize($phoneNumber)
    {
        return $this->tnNormalizePhoneNumber($phoneNumber);
    }
}

describe('Phone Service Class Using Trait', function () {
    beforeEach(function () {
        $this->phoneService = new PhoneService();
    });
    
    it('can normalize phone through service class', function () {
        expect($this->phoneService->normalize('08123456789'))->toBe('+628123456789');
    });
    
    it('handles invalid input through service class', function () {
        expect($this->phoneService->normalize('invalid'))->toBeNull();
    });
});