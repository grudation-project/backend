<?php

namespace Modules\Manager\Services;

use App\Exceptions\ValidationErrorsException;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Actions\Register\BaseRegisterAction;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Services\UserService;
use Modules\Auth\Strategies\Verifiable;
use Modules\Manager\Models\Builders\ManagerBuilder;
use Modules\Manager\Models\Manager;
use Modules\Service\Services\AdminServiceLogic;

class AdminManagerService
{
    public function index()
    {
        return Manager::query()
            ->latest()
            ->when(true, fn(ManagerBuilder $b) => $b->withMinimalDetailsForAdmin())
            ->searchByRelation('user', ['name', 'email'])
            ->paginatedCollection();
    }

    public function show($id)
    {
        return Manager::query()
            ->when(true, fn(ManagerBuilder $b) => $b->withMinimalDetailsForAdmin())
            ->findOrFail($id);
    }

    public function store(array $data)
    {
        UserService::columnExists($data['user']['email'], columnName: 'email', errorKey: 'email');
        AdminServiceLogic::assertNotAssociated($data['service_id']);

        $managerId = null;
        $userPayload = [
            'type' => UserTypeEnum::MANAGER,
            ...$data['user']
        ];

        (new BaseRegisterAction)->handle($userPayload, app(Verifiable::class), function ($user) use (&$managerId, $data) {
            $manager = Manager::query()->create([
                'user_id' => $user->id,
                'service_id' => $data['service_id'],
                'automatic_assignment' => $data['automatic_assignment'] ?? false,
            ]);

            $managerId = $manager->id;
        }, true);

        return $this->show($managerId);
    }

    /**
     * @throws ValidationErrorsException
     */
    public function update(array $data, $id)
    {
        $manager = Manager::query()->findOrFail($id);

        if (isset($data['user']['email'])) {
            UserService::columnExists($data['user']['email'], $manager->user_id, 'email', 'email');
        }

        if (isset($data['service_id'])) {
            AdminServiceLogic::assertNotAssociated($data['service_id'], $manager->id);
        }

        DB::transaction(function () use ($manager, $data) {
            $manager->update($data);

            if (isset($data['user'])) {
                $manager->user->update($data['user']);
            }
        });

        return $this->show($manager->id);
    }

    public function destroy($id)
    {
        Manager::query()->findOrFail($id)->user->delete();
    }
}
