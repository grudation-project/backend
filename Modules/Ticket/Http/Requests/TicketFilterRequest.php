<?php

namespace Modules\Ticket\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HttpResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class TicketFilterRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'service_id' => ValidationRuleHelper::foreignKeyRules([
                'required' => 'nullable',
            ]),
            'from' => ValidationRuleHelper::dateRules([
                'required' => 'nullable',
            ]),
            'to' => ValidationRuleHelper::dateRules([
                'required' => 'nullable',
                'after' => 'after:from',
            ]),
        ];
    }

     /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
