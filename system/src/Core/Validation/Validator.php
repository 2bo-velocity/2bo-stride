<?php

namespace Stride\Core\Validation;

class Validator
{
    private array $errors = [];

    /**
     * Validate data against rules
     *
     * @param array $data Data to validate
     * @param array $rules Rules array ['field' => 'required|email']
     * @return bool True if valid
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesList = explode('|', $ruleString);
            
            foreach ($rulesList as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    $this->errors[$field][] = "{$field} is required";
                }
                
                // Simplified email check
                if ($rule === 'email' && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "{$field} must be a valid email";
                }
                
                // Add more rules as needed...
            }
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
