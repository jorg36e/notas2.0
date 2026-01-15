<?php

namespace App\Filament\Teacher\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament.teacher.pages.auth.login';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('identification')
                    ->label('Identificación (Cédula)')
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->placeholder('Ingrese su número de identificación')
                    ->extraInputAttributes(['class' => 'text-lg']),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->required()
                    ->placeholder('Ingrese su contraseña')
                    ->extraInputAttributes(['class' => 'text-lg']),
            ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $user = User::where('identification', $data['identification'])
            ->where('role', 'teacher')
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.identification' => 'Las credenciales proporcionadas son incorrectas.',
            ]);
        }

        auth()->login($user);

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
