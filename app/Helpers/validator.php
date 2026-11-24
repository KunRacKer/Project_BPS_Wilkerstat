<?php

if (!function_exists('validate')) {
    /**
     * Validasi sederhana. Return array error (kosong jika valid).
     *
     * $rules contoh:
     *   ['username' => 'required|min:3|max:50', 'password' => 'required|min:6']
     */
    function validate(array $data, array $rules, array $labels = []): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleStr) {
            $value = $data[$field] ?? null;
            $label = $labels[$field] ?? $field;

            foreach (explode('|', $ruleStr) as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $name = trim($name);

                switch ($name) {
                    case 'required':
                        if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
                            $errors[$field] = "$label wajib diisi.";
                        }
                        break;

                    case 'min':
                        if ($value !== null && mb_strlen((string)$value) < (int)$param) {
                            $errors[$field] = "$label minimal $param karakter.";
                        }
                        break;

                    case 'max':
                        if ($value !== null && mb_strlen((string)$value) > (int)$param) {
                            $errors[$field] = "$label maksimal $param karakter.";
                        }
                        break;

                    case 'email':
                        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "$label harus berupa email valid.";
                        }
                        break;

                    case 'in':
                        if ($value !== null && $value !== '' && !in_array((string)$value, explode(',', (string)$param), true)) {
                            $errors[$field] = "$label tidak valid.";
                        }
                        break;
                }

                if (isset($errors[$field])) {
                    break; // satu error per field cukup
                }
            }
        }
        return $errors;
    }
}