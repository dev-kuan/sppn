<?php

if (!function_exists('institution')) {
    /**
     * Get institution configuration
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function institution($key = null, $default = null)
    {
        if (is_null($key)) {
            return config('institution');
        }

        return config("institution.{$key}", $default);
    }
}

if (!function_exists('institution_name')) {
    /**
     * Get institution name
     *
     * @return string
     */
    function institution_name()
    {
        return config('institution.name', 'Lembaga Pemasyarakatan');
    }
}

if (!function_exists('officer')) {
    /**
     * Get officer information
     *
     * @param string $officer 'officer1' or 'officer2'
     * @param string|null $field
     * @return mixed
     */
    function officer($officer, $field = null)
    {
        if (is_null($field)) {
            return config("institution.officers.{$officer}");
        }

        return config("institution.officers.{$officer}.{$field}");
    }
}

if (!function_exists('officer_signature')) {
    /**
     * Get officer signature path
     *
     * @param string $officer 'officer1' or 'officer2'
     * @return string|null
     */
    function officer_signature($officer)
    {
        $signature = config("institution.officers.{$officer}.signature");

        if ($signature) {
            return asset('storage/' . $signature);
        }

        return null;
    }
}
