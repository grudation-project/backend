<?php

namespace Modules\Auth\Enums;

enum ForbiddenLoginEnum
{
    const NOT_VERIFIED = 0;

    const PENDING_APPROVAL = 1;

    const REJECTED_APPROVAL = 2;

    const ACCOUNT_SUSPENDED = 3;

    const NOT_APPROVED = 4;
}
