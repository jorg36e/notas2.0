<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.admin-login';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('identification')
                    ->label('Número de identificación')
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->placeholder('Ingrese su número de identificación')
                    ->extraInputAttributes(['class' => 'text-lg']),

                $this->getPasswordFormComponent()
                    ->placeholder('Ingrese su contraseña')
                    ->extraInputAttributes(['class' => 'text-lg']),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'identification' => $data['identification'],
            'password' => $data['password'],
        ];
    }
}
