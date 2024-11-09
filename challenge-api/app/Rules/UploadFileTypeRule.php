<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UploadFileTypeRule implements ValidationRule
{
    /**
     * Check if type of file is available
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowedExtensions = ['xlsx', 'csv', 'zip'];
        $extension = strtolower($value->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) $fail('The file field must be a file of type: ' . implode(', ', $allowedExtensions));
    }
}
