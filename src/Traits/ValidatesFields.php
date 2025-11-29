<?php

namespace Sds\Workshop\Traits;

use Sds\Workshop\Exceptions\ValidationException;

trait ValidatesFields
{
    protected function validateFields(array $data, array $rules): void
    {
        $errors = [];

        foreach ($rules as $field => $validators) {
            $valueExists = array_key_exists($field, $data);
            $value = $valueExists ? $data[$field] : null;

            foreach ($validators as $rule) {

                // required
                if ($rule === 'required' && !$valueExists) {
                    $errors[$field][] = "The field '{$field}' is required.";
                }

                // string
                if ($rule === 'string' && $valueExists && !is_string($value)) {
                    $errors[$field][] = "The field '{$field}' must be a string.";
                }

                // array
                if ($rule === 'array' && $valueExists && !is_array($value)) {
                    $errors[$field][] = "The field '{$field}' must be an array.";
                }

                // email
                if ($rule === 'email' && $valueExists && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "The field '{$field}' must be a valid email address.";
                }

                // number
                if ($rule === 'numeric' && $valueExists && !is_numeric($value)) {
                    $errors[$field][] = "The field '{$field}' must be numeric.";
                }

                // boolean
                if ($rule === 'boolean' && $valueExists && !is_bool($value)) {
                    $errors[$field][] = "The field '{$field}' must be true or false.";
                }
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
