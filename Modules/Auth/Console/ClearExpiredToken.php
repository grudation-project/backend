<?php

namespace Modules\Auth\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\VerifyToken;

class ClearExpiredToken extends Command
{
    protected $name = 'auth:clear-resets';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        VerifyToken::where('expires_at', '>=', now())->delete();
    }
}
