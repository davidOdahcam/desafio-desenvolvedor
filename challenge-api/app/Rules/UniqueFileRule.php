<?php

namespace App\Rules;

use App\Enums\FileStatusEnum;
use App\Models\File;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueFileRule implements ValidationRule
{
    /**
     * Check if file has already been uploaded
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filename = pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME);

        if (File::where('name', $filename)->where('status', '!=', FileStatusEnum::FAILED)->exists()) {
            $fail('This file has already been uploaded.');
        }
    }
}
