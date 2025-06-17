<?php

namespace Modules\Auth\Services;

use App\Exceptions\ValidationErrorsException;
use App\Models\User;

class UserService
{
    /**
     * @throws ValidationErrorsException
     */
    public static function columnExists(
        string $value,
        $id = null,
        string $columnName = 'phone',
        string $errorKey = 'phone'
    ): void {
        $exists = User::query()->where($columnName, $value)->when(! is_null($id), fn($q) => $q->where('id', '<>', $id))->exists();

        if ($exists) {
            throw new ValidationErrorsException([
                $errorKey => translate_error_message($errorKey, 'exists'),
            ]);
        }
    }

    public static function assertValidCollegeEmail(string $email, string $errorKey = 'email')
    {
        if (! str_ends_with($email, '@dmu.edu.eg') && ! str_ends_with($email, '@cis.dmu.edu.eg')) {
            throw new ValidationErrorsException([
                $errorKey => translate_word('invalid_college_email'),
            ]);
        }
    }
}
