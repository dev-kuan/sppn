<?php

namespace App\Services\Assessment;

class ImportResult
{
    public bool $success;
    public int $successCount = 0;
    public array $errors = [];

    private function __construct() {}

    public static function success(int $count): self
    {
        $result = new self();
        $result->success = true;
        $result->successCount = $count;

        return $result;
    }

    public static function withErrors(array $errors): self
    {
        $result = new self();
        $result->success = false;
        $result->errors = $errors;

        return $result;
    }
}
