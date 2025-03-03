<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Helpers\UserTypeHelper;
use Modules\Auth\Traits\VerificationBuilderTrait;

class UserBuilder extends Builder
{
    use VerificationBuilderTrait;

    public function loginByType(array $data)
    {
        return $this->whereNotNull('email')->where('email', $data['email']);
    }

    public function withMinimalSelectedColumns(array $excludeColumns = [], array $additionalColumns = []): UserBuilder
    {
        $columns = array_diff([
            'users.id',
            'users.name',
            'users.phone',
            'users.email',
            'users.status',
        ], array_map(fn ($column) => "users.$column", $excludeColumns));

        return $this->select([...$columns, ...$additionalColumns]);
    }

    public function withConditionalAvatar(bool $withAvatar = true)
    {
        return $this->when($withAvatar, fn (self $q) => $q->with('avatar'));
    }

    public function withMinimalDetails(bool $withAvatar = true, array $excludeColumns = [], array $additionalColumns = [])
    {
        return $this
            ->withMinimalSelectedColumns($excludeColumns, $additionalColumns)
            ->withConditionalAvatar($withAvatar);
    }

    public function whereIsAdmin(): UserBuilder
    {
        return $this->where('type', UserTypeEnum::ADMIN);
    }

    public function whereActive(): UserBuilder
    {
        return $this->where('status', true);
    }
}
