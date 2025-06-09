<?php

namespace Modules\Manager\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HttpResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Modules\Auth\Http\Requests\Register\BaseRegisterRequest;

class ManagerRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        $inUpdate = $this->method() == 'PUT';

        $rules = [
            'service_id' => ValidationRuleHelper::foreignKeyRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
            'automatic_assignment' => ValidationRuleHelper::booleanRules([
                'required' => 'sometimes',
            ]),
        ];

        foreach (BaseRegisterRequest::baseRules($inUpdate) as $key => $value) {
            $rules["user.$key"] = $value;
        }

        return $rules;
    }

    /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
