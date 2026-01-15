<?php

namespace App\Filament\Pages;

use App\Models\SchoolSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SchoolSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Configuración';
    protected static ?string $title = 'Configuración del Colegio';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 100;
    
    protected static string $view = 'filament.pages.school-settings';

    public ?array $generalData = [];
    public ?array $contactData = [];
    public ?array $appearanceData = [];
    public ?array $socialData = [];
    public ?array $loginData = [];
    public ?array $signaturesData = [];

    public function mount(): void
    {
        $settings = SchoolSetting::getAllSettings();
        
        $this->generalForm->fill([
            'school_name' => $settings['school_name'] ?? '',
            'school_slogan' => $settings['school_slogan'] ?? '',
            'school_logo' => null, // FileUpload maneja nuevas subidas
            'school_favicon' => null, // FileUpload maneja nuevas subidas
            'school_nit' => $settings['school_nit'] ?? '',
            'school_dane' => $settings['school_dane'] ?? '',
            'school_resolution' => $settings['school_resolution'] ?? '',
        ]);

        $this->contactForm->fill([
            'school_address' => $settings['school_address'] ?? '',
            'school_phone' => $settings['school_phone'] ?? '',
            'school_email' => $settings['school_email'] ?? '',
            'school_website' => $settings['school_website'] ?? '',
            'school_city' => $settings['school_city'] ?? '',
            'school_department' => $settings['school_department'] ?? '',
        ]);

        $this->appearanceForm->fill([
            'primary_color' => $settings['primary_color'] ?? '#3b82f6',
            'secondary_color' => $settings['secondary_color'] ?? '#10b981',
            'accent_color' => $settings['accent_color'] ?? '#f59e0b',
            'danger_color' => $settings['danger_color'] ?? '#ef4444',
            'dark_mode' => $settings['dark_mode'] ?? 'system',
        ]);

        $this->socialForm->fill([
            'social_facebook' => $settings['social_facebook'] ?? '',
            'social_instagram' => $settings['social_instagram'] ?? '',
            'social_twitter' => $settings['social_twitter'] ?? '',
            'social_youtube' => $settings['social_youtube'] ?? '',
        ]);

        $this->loginForm->fill([
            'login_background' => $settings['login_background'] ?? null,
            'login_welcome_title' => $settings['login_welcome_title'] ?? 'Bienvenido',
            'login_welcome_message' => $settings['login_welcome_message'] ?? 'Accede a tu portal educativo',
            'login_admin_title' => $settings['login_admin_title'] ?? 'Panel Administrativo',
            'login_admin_subtitle' => $settings['login_admin_subtitle'] ?? 'Gestión académica integral',
            'login_teacher_title' => $settings['login_teacher_title'] ?? 'Portal Docente',
            'login_teacher_subtitle' => $settings['login_teacher_subtitle'] ?? 'Gestiona tus clases y calificaciones',
            'login_student_title' => $settings['login_student_title'] ?? 'Portal Estudiante',
            'login_student_subtitle' => $settings['login_student_subtitle'] ?? 'Consulta tus notas y boletines',
            'login_show_logo' => $settings['login_show_logo'] ?? true,
            'login_show_slogan' => $settings['login_show_slogan'] ?? true,
            'login_footer_text' => $settings['login_footer_text'] ?? '',
        ]);

        $this->signaturesForm->fill([
            'rector_name' => $settings['rector_name'] ?? '',
            'rector_signature' => null,
            'secretary_name' => $settings['secretary_name'] ?? '',
            'secretary_signature' => null,
        ]);
    }

    protected function getForms(): array
    {
        return [
            'generalForm',
            'contactForm',
            'appearanceForm',
            'socialForm',
            'loginForm',
            'signaturesForm',
        ];
    }

    public function generalForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Configura los datos básicos de la institución educativa')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('school_logo')
                                    ->label('Logo del Colegio')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('settings')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(2048)
                                    ->columnSpan(1)
                                    ->helperText('Recomendado: 400x400px, formato PNG o JPG'),
                                    
                                Forms\Components\FileUpload::make('school_favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('settings')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/x-icon', 'image/vnd.microsoft.icon'])
                                    ->maxSize(1024)
                                    ->columnSpan(1)
                                    ->helperText('Recomendado: 64x64px, formato PNG o ICO'),
                            ]),
                            
                        Forms\Components\TextInput::make('school_name')
                            ->label('Nombre del Colegio')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Institución Educativa...')
                            ->prefixIcon('heroicon-o-academic-cap'),
                            
                        Forms\Components\Textarea::make('school_slogan')
                            ->label('Lema o Eslogan')
                            ->maxLength(500)
                            ->rows(2)
                            ->placeholder('Educación de calidad para todos...'),
                            
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('school_nit')
                                    ->label('NIT')
                                    ->maxLength(50)
                                    ->placeholder('123456789-0'),
                                    
                                Forms\Components\TextInput::make('school_dane')
                                    ->label('Código DANE')
                                    ->maxLength(50)
                                    ->placeholder('123456789012'),
                                    
                                Forms\Components\TextInput::make('school_resolution')
                                    ->label('Resolución de Aprobación')
                                    ->maxLength(100)
                                    ->placeholder('Res. 1234 del 2020'),
                            ]),
                    ]),
            ])
            ->statePath('generalData');
    }

    public function contactForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Contacto')
                    ->description('Datos de ubicación y contacto de la institución')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Textarea::make('school_address')
                            ->label('Dirección Principal')
                            ->maxLength(500)
                            ->rows(2)
                            ->placeholder('Calle 123 # 45-67, Barrio Centro'),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('school_city')
                                    ->label('Ciudad/Municipio')
                                    ->maxLength(100)
                                    ->placeholder('Bogotá'),
                                    
                                Forms\Components\TextInput::make('school_department')
                                    ->label('Departamento')
                                    ->maxLength(100)
                                    ->placeholder('Cundinamarca'),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('school_phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(50)
                                    ->placeholder('+57 1 234 5678')
                                    ->prefixIcon('heroicon-o-phone'),
                                    
                                Forms\Components\TextInput::make('school_email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('contacto@colegio.edu.co')
                                    ->prefixIcon('heroicon-o-envelope'),
                            ]),
                            
                        Forms\Components\TextInput::make('school_website')
                            ->label('Sitio Web')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://www.colegio.edu.co')
                            ->prefixIcon('heroicon-o-globe-alt'),
                    ]),
            ])
            ->statePath('contactData');
    }

    public function appearanceForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Apariencia y Colores')
                    ->description('Personaliza los colores y tema del sistema')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->label('Color Principal')
                                    ->default('#3b82f6'),
                                    
                                Forms\Components\ColorPicker::make('secondary_color')
                                    ->label('Color Secundario')
                                    ->default('#10b981'),
                                    
                                Forms\Components\ColorPicker::make('accent_color')
                                    ->label('Color de Acento')
                                    ->default('#f59e0b'),
                                    
                                Forms\Components\ColorPicker::make('danger_color')
                                    ->label('Color de Peligro')
                                    ->default('#ef4444'),
                            ]),
                            
                        Forms\Components\Select::make('dark_mode')
                            ->label('Modo de Tema')
                            ->options([
                                'light' => '☀️ Claro',
                                'dark' => '🌙 Oscuro',
                                'system' => '💻 Seguir sistema',
                            ])
                            ->default('system')
                            ->native(false),
                            
                        Forms\Components\Placeholder::make('color_preview')
                            ->label('Vista previa de colores')
                            ->content(fn ($get) => view('filament.components.color-preview', [
                                'primary' => $get('primary_color'),
                                'secondary' => $get('secondary_color'),
                                'accent' => $get('accent_color'),
                                'danger' => $get('danger_color'),
                            ])),
                    ]),
            ])
            ->statePath('appearanceData');
    }

    public function socialForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Redes Sociales')
                    ->description('Enlaces a las redes sociales de la institución')
                    ->icon('heroicon-o-share')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('social_facebook')
                                    ->label('Facebook')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://facebook.com/tucolegio')
                                    ->prefixIcon('heroicon-o-link'),
                                    
                                Forms\Components\TextInput::make('social_instagram')
                                    ->label('Instagram')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://instagram.com/tucolegio')
                                    ->prefixIcon('heroicon-o-camera'),
                                    
                                Forms\Components\TextInput::make('social_twitter')
                                    ->label('Twitter / X')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://twitter.com/tucolegio')
                                    ->prefixIcon('heroicon-o-chat-bubble-left'),
                                    
                                Forms\Components\TextInput::make('social_youtube')
                                    ->label('YouTube')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://youtube.com/@tucolegio')
                                    ->prefixIcon('heroicon-o-play'),
                            ]),
                    ]),
            ])
            ->statePath('socialData');
    }

    public function loginForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personalización de Login')
                    ->description('Configura la apariencia de las páginas de inicio de sesión')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Forms\Components\FileUpload::make('login_background')
                            ->label('Imagen de Fondo')
                            ->image()
                            ->imageEditor()
                            ->directory('settings')
                            ->disk('public')
                            ->visibility('public')
                            ->helperText('Recomendado: 1920x1080px, formato JPG. Se usará en todos los login.'),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('login_show_logo')
                                    ->label('Mostrar Logo')
                                    ->default(true)
                                    ->helperText('Muestra el logo del colegio en el login'),
                                    
                                Forms\Components\Toggle::make('login_show_slogan')
                                    ->label('Mostrar Eslogan')
                                    ->default(true)
                                    ->helperText('Muestra el eslogan debajo del logo'),
                            ]),
                            
                        Forms\Components\TextInput::make('login_welcome_title')
                            ->label('Título de Bienvenida')
                            ->maxLength(100)
                            ->placeholder('Bienvenido')
                            ->helperText('Título principal en la sección de bienvenida'),
                            
                        Forms\Components\Textarea::make('login_welcome_message')
                            ->label('Mensaje de Bienvenida')
                            ->maxLength(300)
                            ->rows(2)
                            ->placeholder('Accede a tu portal educativo'),
                    ]),
                    
                Forms\Components\Section::make('Textos por Rol')
                    ->description('Personaliza los textos de cada panel de login')
                    ->icon('heroicon-o-users')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Fieldset::make('Panel Administrador')
                            ->schema([
                                Forms\Components\TextInput::make('login_admin_title')
                                    ->label('Título')
                                    ->maxLength(100)
                                    ->placeholder('Panel Administrativo'),
                                Forms\Components\TextInput::make('login_admin_subtitle')
                                    ->label('Subtítulo')
                                    ->maxLength(200)
                                    ->placeholder('Gestión académica integral'),
                            ])->columns(2),
                            
                        Forms\Components\Fieldset::make('Portal Docente')
                            ->schema([
                                Forms\Components\TextInput::make('login_teacher_title')
                                    ->label('Título')
                                    ->maxLength(100)
                                    ->placeholder('Portal Docente'),
                                Forms\Components\TextInput::make('login_teacher_subtitle')
                                    ->label('Subtítulo')
                                    ->maxLength(200)
                                    ->placeholder('Gestiona tus clases y calificaciones'),
                            ])->columns(2),
                            
                        Forms\Components\Fieldset::make('Portal Estudiante')
                            ->schema([
                                Forms\Components\TextInput::make('login_student_title')
                                    ->label('Título')
                                    ->maxLength(100)
                                    ->placeholder('Portal Estudiante'),
                                Forms\Components\TextInput::make('login_student_subtitle')
                                    ->label('Subtítulo')
                                    ->maxLength(200)
                                    ->placeholder('Consulta tus notas y boletines'),
                            ])->columns(2),
                    ]),
                    
                Forms\Components\Section::make('Pie de Página')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('login_footer_text')
                            ->label('Texto del Pie de Página')
                            ->maxLength(500)
                            ->rows(2)
                            ->placeholder('© 2026 Institución Educativa - Todos los derechos reservados'),
                    ]),
            ])
            ->statePath('loginData');
    }

    public function signaturesForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Firmas para Documentos')
                    ->description('Configura las firmas del rector y secretaria que aparecerán en boletines y otros documentos oficiales')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Fieldset::make('Rector(a)')
                            ->schema([
                                Forms\Components\TextInput::make('rector_name')
                                    ->label('Nombre del Rector(a)')
                                    ->maxLength(255)
                                    ->placeholder('Nombre completo del rector')
                                    ->prefixIcon('heroicon-o-user'),
                                    
                                Forms\Components\FileUpload::make('rector_signature')
                                    ->label('Firma del Rector(a)')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('signatures')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(1024)
                                    ->helperText('Imagen de la firma. Recomendado: fondo transparente PNG, máximo 400x200px'),
                            ])->columns(1),
                            
                        Forms\Components\Fieldset::make('Secretario(a)')
                            ->schema([
                                Forms\Components\TextInput::make('secretary_name')
                                    ->label('Nombre del Secretario(a)')
                                    ->maxLength(255)
                                    ->placeholder('Nombre completo del secretario')
                                    ->prefixIcon('heroicon-o-user'),
                                    
                                Forms\Components\FileUpload::make('secretary_signature')
                                    ->label('Firma del Secretario(a)')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('signatures')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(1024)
                                    ->helperText('Imagen de la firma. Recomendado: fondo transparente PNG, máximo 400x200px'),
                            ])->columns(1),
                    ]),
            ])
            ->statePath('signaturesData');
    }

    public function saveGeneral(): void
    {
        $data = $this->generalForm->getState();
        
        foreach ($data as $key => $value) {
            // Manejar archivos de imagen
            if (in_array($key, ['school_logo', 'school_favicon'])) {
                if ($value) {
                    // Si es un array, tomar el primer elemento
                    if (is_array($value)) {
                        $value = $value[0] ?? null;
                    }
                    
                    // Solo actualizar si hay un valor nuevo
                    if ($value) {
                        // Eliminar la imagen antigua si existe
                        $oldValue = SchoolSetting::get($key);
                        if ($oldValue && $oldValue !== $value) {
                            Storage::disk('public')->delete($oldValue);
                        }
                        SchoolSetting::set($key, $value, 'image', 'general');
                    }
                }
            } else {
                // Campos de texto normales
                SchoolSetting::set($key, $value, 'text', 'general');
            }
        }
        
        // Limpiar los campos de archivo del formulario
        $this->generalData['school_logo'] = null;
        $this->generalData['school_favicon'] = null;
        
        Notification::make()
            ->title('Información general guardada')
            ->success()
            ->send();
    }

    public function saveContact(): void
    {
        $data = $this->contactForm->getState();
        
        foreach ($data as $key => $value) {
            SchoolSetting::set($key, $value, 'text', 'contact');
        }
        
        Notification::make()
            ->title('Información de contacto guardada')
            ->success()
            ->send();
    }

    public function saveAppearance(): void
    {
        $data = $this->appearanceForm->getState();
        
        foreach ($data as $key => $value) {
            $type = str_contains($key, 'color') ? 'color' : 'text';
            SchoolSetting::set($key, $value, $type, 'appearance');
        }
        
        Notification::make()
            ->title('Apariencia guardada')
            ->body('Recarga la página para ver los cambios de colores')
            ->success()
            ->send();
    }

    public function saveSocial(): void
    {
        $data = $this->socialForm->getState();
        
        foreach ($data as $key => $value) {
            SchoolSetting::set($key, $value, 'text', 'social');
        }
        
        Notification::make()
            ->title('Redes sociales guardadas')
            ->success()
            ->send();
    }

    public function saveLogin(): void
    {
        $data = $this->loginForm->getState();
        
        foreach ($data as $key => $value) {
            if ($key === 'login_background') {
                if ($value) {
                    // Si es un array, tomar el primer elemento
                    if (is_array($value)) {
                        $value = $value[0] ?? null;
                    }
                    
                    // Solo actualizar si hay un valor nuevo
                    if ($value) {
                        // Eliminar la imagen antigua si existe
                        $oldValue = SchoolSetting::get($key);
                        if ($oldValue && $oldValue !== $value) {
                            Storage::disk('public')->delete($oldValue);
                        }
                        SchoolSetting::set($key, $value, 'image', 'login');
                    }
                }
            } elseif (in_array($key, ['login_show_logo', 'login_show_slogan'])) {
                $type = 'boolean';
                $value = $value ? '1' : '0';
                SchoolSetting::set($key, $value, $type, 'login');
            } else {
                SchoolSetting::set($key, $value, 'text', 'login');
            }
        }
        
        // Limpiar el campo de archivo del formulario
        $this->loginData['login_background'] = null;
        
        Notification::make()
            ->title('Configuración de login guardada')
            ->success()
            ->send();
    }

    public function saveSignatures(): void
    {
        $data = $this->signaturesForm->getState();
        
        foreach ($data as $key => $value) {
            if (str_contains($key, 'signature')) {
                if ($value) {
                    // Si es un array, tomar el primer elemento
                    if (is_array($value)) {
                        $value = $value[0] ?? null;
                    }
                    
                    // Solo actualizar si hay un valor nuevo
                    if ($value) {
                        // Eliminar la imagen antigua si existe
                        $oldValue = SchoolSetting::get($key);
                        if ($oldValue && $oldValue !== $value) {
                            Storage::disk('public')->delete($oldValue);
                        }
                        SchoolSetting::set($key, $value, 'image', 'signatures');
                    }
                }
            } else {
                // Campos de texto (nombres)
                SchoolSetting::set($key, $value, 'text', 'signatures');
            }
        }
        
        // Limpiar los campos de archivo del formulario
        $this->signaturesData['rector_signature'] = null;
        $this->signaturesData['secretary_signature'] = null;
        
        Notification::make()
            ->title('Firmas guardadas')
            ->body('Las firmas se mostrarán en los boletines y documentos oficiales')
            ->success()
            ->send();
    }

    public function saveAll(): void
    {
        $this->saveGeneral();
        $this->saveContact();
        $this->saveAppearance();
        $this->saveSocial();
        $this->saveLogin();
        $this->saveSignatures();
        
        Notification::make()
            ->title('Toda la configuración ha sido guardada')
            ->success()
            ->send();
    }

    public function deleteLogo(): void
    {
        $currentLogo = SchoolSetting::get('school_logo');
        
        if ($currentLogo) {
            Storage::disk('public')->delete($currentLogo);
            SchoolSetting::set('school_logo', null, 'image', 'general');
            
            Notification::make()
                ->title('Logo eliminado')
                ->body('El logo del colegio ha sido eliminado correctamente.')
                ->success()
                ->send();
        }
    }

    public function deleteFavicon(): void
    {
        $currentFavicon = SchoolSetting::get('school_favicon');
        
        if ($currentFavicon) {
            Storage::disk('public')->delete($currentFavicon);
            SchoolSetting::set('school_favicon', null, 'image', 'general');
            
            Notification::make()
                ->title('Favicon eliminado')
                ->body('El favicon ha sido eliminado correctamente.')
                ->success()
                ->send();
        }
    }

    public function deleteRectorSignature(): void
    {
        $current = SchoolSetting::get('rector_signature');
        
        if ($current) {
            Storage::disk('public')->delete($current);
            SchoolSetting::set('rector_signature', null, 'image', 'signatures');
            
            Notification::make()
                ->title('Firma del Rector eliminada')
                ->success()
                ->send();
        }
    }

    public function deleteSecretarySignature(): void
    {
        $current = SchoolSetting::get('secretary_signature');
        
        if ($current) {
            Storage::disk('public')->delete($current);
            SchoolSetting::set('secretary_signature', null, 'image', 'signatures');
            
            Notification::make()
                ->title('Firma del Secretario eliminada')
                ->success()
                ->send();
        }
    }

    public function deleteLoginBackground(): void
    {
        $current = SchoolSetting::get('login_background');
        
        if ($current) {
            Storage::disk('public')->delete($current);
            SchoolSetting::set('login_background', null, 'image', 'login');
            
            Notification::make()
                ->title('Imagen de fondo eliminada')
                ->success()
                ->send();
        }
    }
}
