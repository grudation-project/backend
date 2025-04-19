<?php

namespace Modules\Ticket\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HttpResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class TicketAssignRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            'technician_id' => ValidationRuleHelper::foreignKeyRules(),
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
