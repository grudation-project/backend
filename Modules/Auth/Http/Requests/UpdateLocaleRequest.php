<?php

namespace Modules\Auth\Http\Requests;

use App\Helpers\TranslationHelper;
use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLocaleRequest extends FormRequest
{
    use HttpResponse;

    public function prepareForValidation()
    {
        $inputs = $this->all();

        if (isset($inputs['Locale'])) {
            $inputs['locale'] = $inputs['Locale'];
            unset($inputs['Locale']);
            $this->replace($inputs);
        }
    }

    public function rules()
    {
        return [
            'locale' => ValidationRuleHelper::enumRules(TranslationHelper::$availableLocales),
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
