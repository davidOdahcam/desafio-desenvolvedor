<?php

namespace App\Rules;

use App\Models\File;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueFileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filename = pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME);

        if (File::where('name', $filename)->exists()) {
            $fail('This file has already been uploaded.');
        }
    }
}
