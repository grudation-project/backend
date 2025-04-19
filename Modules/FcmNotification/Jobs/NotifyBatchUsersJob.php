<?php

namespace Modules\FcmNotification\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\FcmNotification\Notifications\RealtimeNotification;

class NotifyBatchUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->data['target'] == UserTypeEnum::CLIENT) {
            $this->notifyUsers();
        } elseif ($this->data['target'] == UserTypeEnum::VENDOR) {
            $this->notifyVendors();
        } else {
            $this->notifyUsers();
            $this->notifyVendors();
        }
    }

    private function notifyUsers()
    {
        User::query()->whereType(UserTypeEnum::CLIENT)->cursor()->each(fn (User $user) => $this->dispatchNotification($user));
    }

    private function notifyVendors()
    {
        User::query()->whereType(UserTypeEnum::VENDOR)->cursor()->each(fn (User $user) => $this->dispatchNotification($user));
    }

    private function dispatchNotification($user)
    {
        $user->notify(new RealtimeNotification(
            $this->data['title'],
            $this->data['body'],
            $this->data['image'],
        ));
    }
}
