<?php

namespace App\Filament\Student\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MyProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.student.pages.my-profile';

    protected static ?string $navigationLabel = 'Mi Perfil';

    protected static ?string $title = 'Mi Perfil';

    protected static ?int $navigationSort = 2;

    public ?array $photoData = [];
    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        
        // Inicializar photoData vacío - el FileUpload maneja nuevas subidas
        $this->photoData = [
            'profile_photo' => null,
        ];
        
        $this->data = [
            'name' => $user->name,
            'identification' => $user->identification,
            'birth_date' => $user->birth_date,
            'phone' => $user->phone,
            'address' => $user->address,
            'guardian_name' => $user->guardian_name,
            'guardian_phone' => $user->guardian_phone,
        ];
    }

    public function photoForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('profile_photo')
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->description('Estos son tus datos personales registrados en el sistema.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Para cambiar tu nombre, contacta a la administración.'),

                        Forms\Components\TextInput::make('identification')
                            ->label('Número de Identificación')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Tu documento de identidad no puede ser modificado.'),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Fecha de Nacimiento')
                            ->maxDate(now())
                            ->displayFormat('d/m/Y')
                            ->native(false),

                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Ej: 3001234567'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dirección')
                    ->description('Tu dirección de residencia actual.')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Dirección Completa')
                            ->maxLength(500)
                            ->rows(2)
                            ->placeholder('Ej: Calle 123 # 45-67, Barrio Centro')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Información del Acudiente')
                    ->description('Datos de contacto de tu acudiente o representante legal.')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Forms\Components\TextInput::make('guardian_name')
                            ->label('Nombre del Acudiente')
                            ->maxLength(255)
                            ->placeholder('Ej: Juan Pérez García'),

                        Forms\Components\TextInput::make('guardian_phone')
                            ->label('Teléfono del Acudiente')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Ej: 3009876543'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getForms(): array
    {
        return [
            'photoForm',
            'form',
        ];
    }

    public function updatePhoto(): void
    {
        $data = $this->photoForm->getState();
        $user = Auth::user();
        
        // El FileUpload puede devolver un string o un array con el path
        $newPhoto = $data['profile_photo'] ?? null;
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

        Notification::make()
            ->title('¡Foto actualizada!')
            ->body('Tu foto de perfil ha sido guardada correctamente.')
            ->success()
            ->send();
    }

    public function deletePhoto(): void
    {
        $user = Auth::user();
        
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

    public function save(): void
    {
        $data = $this->form->getState();
        
        $user = Auth::user();
        
        $user->update([
            'birth_date' => $data['birth_date'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'guardian_name' => $data['guardian_name'],
            'guardian_phone' => $data['guardian_phone'],
        ]);

        Notification::make()
            ->title('¡Perfil actualizado!')
            ->body('Tus datos han sido guardados correctamente.')
            ->success()
            ->send();
    }
}
