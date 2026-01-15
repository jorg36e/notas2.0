<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Models\Sede;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeacherResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Gestión de Usuarios';

    protected static ?string $modelLabel = 'Profesor';

    protected static ?string $pluralModelLabel = 'Profesores';

    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'teachers';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'teacher');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Foto de Perfil')
                    ->schema([
                        Forms\Components\FileUpload::make('profile_photo')
                            ->label('Imagen de Perfil')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory('profile-photos')
                            ->maxSize(2048)
                            ->helperText('Formatos permitidos: JPG, PNG. Máximo 2MB.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Información del Profesor')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Juan Pérez García'),

                        Forms\Components\TextInput::make('identification')
                            ->label('Identificación (Cédula)')
                            ->required()
                            ->unique(table: 'users', ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: 1234567890')
                            ->helperText('Se usará como contraseña inicial.'),

                        Forms\Components\Select::make('sede_id')
                            ->label('Sede')
                            ->relationship('sede', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione una sede')
                            ->helperText('Sede a la que pertenece el profesor'),

                        Forms\Components\Hidden::make('role')
                            ->default('teacher'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('ejemplo@correo.com'),

                        Forms\Components\Textarea::make('address')
                            ->label('Dirección')
                            ->maxLength(500)
                            ->rows(2),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Usuario Activo')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(fn ($record) => $record->profile_photo ? asset('storage/' . $record->profile_photo) : null)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=10b981&color=fff'),

                Tables\Columns\TextColumn::make('identification')
                    ->label('Identificación')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Identificación copiada'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sede.name')
                    ->label('Sede')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder('Sin sede'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->placeholder('Sin teléfono'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copiado')
                    ->placeholder('Sin email'),

                Tables\Columns\TextColumn::make('teaching_assignments_count')
                    ->label('Asignaciones')
                    ->counts('teachingAssignments')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sede_id')
                    ->label('Sede')
                    ->relationship('sede', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\Action::make('resetPassword')
                    ->label('Resetear Contraseña')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Resetear Contraseña')
                    ->modalDescription('La contraseña será restablecida al número de identificación del profesor.')
                    ->modalSubmitActionLabel('Sí, resetear')
                    ->action(function (User $record): void {
                        $record->update([
                            'password' => \Illuminate\Support\Facades\Hash::make($record->identification),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Contraseña restablecida')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
