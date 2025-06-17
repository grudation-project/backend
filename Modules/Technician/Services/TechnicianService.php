<?php

namespace Modules\Technician\Services;

use App\Exceptions\ValidationErrorsException;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Actions\Register\BaseRegisterAction;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Services\UserService;
use Modules\Auth\Strategies\Verifiable;
use Modules\Manager\Traits\ManagerSetter;
use Modules\Service\Services\AdminSectionService;
use Modules\Technician\Models\Builders\TechnicianBuilder;
use Modules\Technician\Models\Technician;

class TechnicianService
{
    use ManagerSetter;

    public function index()
    {
        return Technician::query()
            ->latest()
            ->where('manager_id', $this->getManager()->id)
            ->searchByRelation('user', ['name', 'email'])
            ->when(true, fn(TechnicianBuilder $b) => $b->withMinimalDetailsForManager())
            ->get();
    }

    public function show($id)
    {
        return Technician::query()
            ->where('manager_id', $this->getManager()->id)
            ->when(true, fn(TechnicianBuilder $b) => $b->withMinimalDetailsForManager())
            ->findOrFail($id);
    }

    /**
     * @throws ValidationErrorsException
     */
    public function store(array $data)
    {
        $this->assertValidSection($data['section_id']);
        UserService::columnExists($data['email'], columnName: 'email', errorKey: 'email');

        $technicianId = null;
        $data['type'] = UserTypeEnum::TECHNICIAN;

        (new BaseRegisterAction)->handle($data, app(Verifiable::class), function ($user) use (&$technicianId, $data) {
            $technician = Technician::query()->create([
                'user_id' => $user->id,
                'manager_id' => $this->getManager()->id,
                'section_id' => $data['section_id'],
            ]);

            $technicianId = $technician->id;
        }, true);

        return $this->show($technicianId);
    }

    public function update(array $data, $id)
    {
        if (isset($data['section_id'])) {
            $this->assertValidSection($data['section_id']);
        }

        $technician = Technician::query()->findOrFail($id);

        if (isset($data['email'])) {
            UserService::columnExists($data['email'], $technician->user_id, 'email', 'email');
        }

        DB::transaction(function () use ($technician, $data) {
            $technician->update([
                'section_id' => $data['section_id'] ?? $technician->section_id,
            ]);
            $technician->user->update($data);
        });

        return $this->show($technician->id);
    }

    public function destroy($id)
    {
        Technician::query()->findOrFail($id)->user->delete();
    }

    public static function exists($managerId, $id, $sectionId, string $errorKey = 'technician_id')
    {
        $tech = Technician::query()
            ->where('manager_id', $managerId)
            ->where('section_id', $sectionId)
            ->find($id);

        if (! $tech) {
            throw new ValidationErrorsException([
                $errorKey => translate_error_message('technician', 'not_exists'),
            ]);
        }

        return $tech;
    }

    private function assertValidSection($id)
    {
        $section = AdminSectionService::exists($id);

        if ($section->service_id != $this->getManager()->service_id) {
            throw new ValidationErrorsException([
                'section_id' => translate_error_message('section', 'not_exists'),
            ]);
        }
    }
}
