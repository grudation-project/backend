<?php

namespace Modules\Auth\Actions\Register;

use App\Exceptions\ValidationErrorsException;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Enums\AuthApprovalEnum;
use Modules\Auth\Enums\AuthEnum;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Services\UserService;
use Modules\Auth\Strategies\Verifiable;

class BaseRegisterAction
{
    /**
     * @throws ValidationErrorsException
     */
    public function handle(array $data, Verifiable $verifiable, ?Closure $closure = null, bool $byAdmin = false)
    {
        $errors = [];
        $user = null;

        try {
            DB::transaction(function () use ($data, $closure, &$errors, $verifiable, $byAdmin, &$user) {

//                if (isset($data['phone'])) {
//                    UserService::columnExists($data['phone']);
//                }

                if (isset($data['email'])) {
                    UserService::columnExists($data['email'], columnName: 'email', errorKey: 'email');
                }

                $user = User::create($data + [
                    'status' => false,
                    'approval' => $data['type'] == UserTypeEnum::USER,
                    ...$this->mergeByAdminFields($byAdmin),
                ]);

                if ($closure) {
                    $closure($user, $errors, $data);
                }

                if (! $byAdmin) {
                    $verifiable->sendCode($user);
                }

                return $user;
            });
        } catch (ValidationErrorsException $e) {
            throw $e;
        } catch (Exception $e) {
            $message = $e->getMessage();

            if (str_contains(strtolower($message), strtolower("[HTTP 400] Unable to create record: Invalid 'To' Phone Number"))) {
                $message = translate_word('invalid_phone_number');
            }

            $errors['operation_failed'] = $message;

            throw new ValidationErrorsException($errors);
        }

        return $user;
    }

    private function mergeByAdminFields(bool $byAdmin = false): array
    {
        return $byAdmin ? [
            AuthEnum::VERIFIED_AT => now(),
            'status' => true,
            'approval' => AuthApprovalEnum::ACCEPTED,
        ] : [];
    }
}
