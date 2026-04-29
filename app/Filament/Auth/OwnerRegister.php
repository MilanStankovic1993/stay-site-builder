<?php

namespace App\Filament\Auth;

use App\Http\Responses\Auth\OwnerRegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Events\Registered;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class OwnerRegister extends Register
{
    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        if ($this->isRegisterRateLimited($this->data['email'] ?? '')) {
            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function (): Model {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Notification::make()
            ->title(app()->getLocale() === 'en' ? 'Your account has been recorded' : 'Vas nalog je evidentiran')
            ->body(app()->getLocale() === 'en'
                ? 'If you want the account to become active, please contact support for the next activation steps.'
                : 'Ako zelite da nalog bude aktivan, obratite se korisnickom servisu za dalje korake i aktivaciju.')
            ->success()
            ->send();

        return app(OwnerRegistrationResponse::class);
    }

    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration([
            ...$data,
            'is_active' => false,
            'can_publish_sites' => false,
        ]);

        $user->syncRoles([Role::findOrCreate('owner')]);

        return $user;
    }
}
