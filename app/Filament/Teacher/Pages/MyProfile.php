<?php

namespace App\Filament\Teacher\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class MyProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Mi Perfil';

    protected static ?string $title = 'Mi Perfil';

    protected static string $view = 'filament.teacher.pages.my-profile';

    protected static ?int $navigationSort = 100;

    public ?array $photoData = [];
    public ?array $profileData = [];
    public ?array $passwordData = [];
    public ?array $signatureData = [];

    public function mount(): void
    {
        $user = auth()->user();
        
        // Inicializar photoData vacío - el FileUpload maneja nuevas subidas
        $this->photoData = [
            'profile_photo' => null,
        ];
        
        $this->profileData = [
            'name' => $user->name,
            'identification' => $user->identification,
            'phone' => $user->phone,
            'address' => $user->address,
        ];

        $this->signatureData = [
            'signature' => null,
        ];
    }

    public function photoForm(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('profile_photo')
                    ->label('Seleccionar imagen')
                    ->image()
                    ->imageEditor()
                    ->directory('profile-photos')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Formatos: JPG, PNG, WEBP. Máximo 2MB.')
                    ->columnSpanFull(),
            ])
            ->statePath('photoData');
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Personal')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->disabled(),

                        TextInput::make('identification')
                            ->label('Identificación')
                            ->disabled(),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(500),
                    ])
                    ->columns(2),
            ])
            ->statePath('profileData');
    }

    public function signatureForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Mi Firma Digital')
                    ->description('Sube una imagen de tu firma para que aparezca en boletines y documentos oficiales')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        FileUpload::make('signature')
                            ->label('Imagen de Firma')
                            ->image()
                            ->imageEditor()
                            ->directory('signatures')
                            ->visibility('public')
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Recomendado: imagen con fondo transparente (PNG), tamaño máximo 400x200px')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('signatureData');
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cambiar Contraseña')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Contraseña Actual')
                            ->password()
                            ->revealable()
                            ->required()
                            ->currentPassword(),

                        TextInput::make('password')
                            ->label('Nueva Contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::default())
                            ->different('current_password'),

                        TextInput::make('password_confirmation')
                            ->label('Confirmar Nueva Contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password'),
                    ])
                    ->columns(1),
            ])
            ->statePath('passwordData');
    }

    protected function getForms(): array
    {
        return [
            'photoForm',
            'profileForm',
            'signatureForm',
            'passwordForm',
        ];
    }

    public function updatePhoto(): void
    {
        $data = $this->photoForm->getState();
        $user = auth()->user();
        
        // El FileUpload puede devolver un string o un array con el path
        $newPhoto = $data['profile_photo'] ?? null;
        
        // Si no hay nueva foto, no hacer nada
        if (!$newPhoto) {
            Notification::make()
                ->title('Selecciona una imagen')
                ->warning()
                ->send();
            return;
        }
        
        if (is_array($newPhoto)) {
            $newPhoto = $newPhoto[0] ?? null;
        }
        
        // Si hay una foto antigua y es diferente a la nueva, eliminarla
        if ($user->profile_photo && $user->profile_photo !== $newPhoto) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->update([
            'profile_photo' => $newPhoto,
        ]);
        
        // Limpiar el formulario
        $this->photoData = ['profile_photo' => null];

        Notification::make()
            ->title('Foto de perfil actualizada')
            ->success()
            ->send();
    }

    public function deletePhoto(): void
    {
        $user = auth()->user();
        
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
            
            Notification::make()
                ->title('Foto eliminada')
                ->body('Tu foto de perfil ha sido eliminada.')
                ->success()
                ->send();
        }
    }

    public function updateProfile(): void
    {
        $data = $this->profileForm->getState();

        auth()->user()->update([
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        Notification::make()
            ->title('Perfil actualizado')
            ->success()
            ->send();
    }

    public function updateSignature(): void
    {
        $data = $this->signatureForm->getState();
        $user = auth()->user();
        
        $newSignature = $data['signature'] ?? null;
        
        // Si no hay nueva firma, no hacer nada
        if (!$newSignature) {
            Notification::make()
                ->title('Selecciona una imagen de firma')
                ->warning()
                ->send();
            return;
        }
        
        if (is_array($newSignature)) {
            $newSignature = $newSignature[0] ?? null;
        }
        
        // Si hay una firma antigua y es diferente a la nueva, eliminarla
        if ($user->signature && $user->signature !== $newSignature) {
            Storage::disk('public')->delete($user->signature);
        }

        $user->update([
            'signature' => $newSignature,
        ]);
        
        // Limpiar el formulario
        $this->signatureData = ['signature' => null];

        Notification::make()
            ->title('Firma actualizada')
            ->body('Tu firma aparecerá en los boletines y documentos que generes')
            ->success()
            ->send();
    }

    public function deleteSignature(): void
    {
        $user = auth()->user();
        
        if ($user->signature) {
            Storage::disk('public')->delete($user->signature);
            $user->update(['signature' => null]);
            
            Notification::make()
                ->title('Firma eliminada')
                ->body('Tu firma ha sido eliminada.')
                ->success()
                ->send();
        }
    }

    public function updatePassword(): void
    {
        $data = $this->passwordForm->getState();

        auth()->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        $this->passwordData = [];

        Notification::make()
            ->title('Contraseña actualizada')
            ->success()
            ->send();
    }
}
