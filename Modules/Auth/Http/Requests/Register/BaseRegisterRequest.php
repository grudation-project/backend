<?php

namespace Modules\Auth\Http\Requests\Register;

use App\Helpers\ValidationRuleHelper;
use Illuminate\Foundation\Http\FormRequest;

class BaseRegisterRequest extends FormRequest
{
    public static function baseRules(bool $inUpdate = false): array
    {
        return [
            'name' => ValidationRuleHelper::stringRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
            'phone' => ValidationRuleHelper::phoneRules([
                'required' => 'sometimes',
            ]),
            'email' => ValidationRuleHelper::emailRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
            'password' => ValidationRuleHelper::defaultPasswordRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
        ];
    }
}
