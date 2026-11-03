<?php

namespace App\Validation;

use App\Validation\StateRules\StateRule;
use App\Exceptions\ValidationException;

class StateValidator
{

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ValidationException
     * @param StateRule[] $rules
     */
    public function validate(array $rules): void {
        foreach ($rules as $rule) {
            $this->validateRule($rule);
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ValidationException
     */
    private function validateRule(StateRule $rule): void {
        if ( ! $rule->isValid()) {
            throw new ValidationException($rule->getMessage());
        }
    }

}