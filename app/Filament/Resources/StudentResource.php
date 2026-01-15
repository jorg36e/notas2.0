<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\User;
use App\Models\Sede;
use App\Models\Grade;
use App\Models\SchoolYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Gestión de Usuarios';

    protected static ?string $modelLabel = 'Estudiante';

    protected static ?string $pluralModelLabel = 'Estudiantes';

    protected static ?int $navigationSort = 7;

    protected static ?string $slug = 'students';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'student');
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

                Forms\Components\Section::make('Información del Estudiante')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: María García López'),

                        Forms\Components\TextInput::make('identification')
                            ->label('Identificación (Documento)')
                            ->required()
                            ->unique(table: 'users', ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: 1234567890')
                            ->helperText('Se usará como contraseña inicial.'),

                        Forms\Components\Hidden::make('role')
                            ->default('student'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Fecha de Nacimiento')
                            ->maxDate(now()),

                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('address')
                            ->label('Dirección')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('guardian_name')
                            ->label('Nombre del Acudiente')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('guardian_phone')
                            ->label('Teléfono del Acudiente')
                            ->tel()
                            ->maxLength(20),

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
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=8b5cf6&color=fff'),

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

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->placeholder('Sin teléfono'),

                Tables\Columns\TextColumn::make('guardian_name')
                    ->label('Acudiente')
                    ->placeholder('Sin acudiente')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('enrollments_count')
                    ->label('Matrículas')
                    ->counts('enrollments')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('currentSede')
                    ->label('Sede Actual')
                    ->getStateUsing(function ($record) {
                        $enrollment = $record->enrollments()
                            ->whereHas('schoolYear', fn($q) => $q->where('is_active', true))
                            ->with('sede')
                            ->first();
                        return $enrollment?->sede?->name ?? '—';
                    })
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('currentGrade')
                    ->label('Grado Actual')
                    ->getStateUsing(function ($record) {
                        $enrollment = $record->enrollments()
                            ->whereHas('schoolYear', fn($q) => $q->where('is_active', true))
                            ->with('grade')
                            ->first();
                        return $enrollment?->grade?->name ?? '—';
                    })
                    ->badge()
                    ->color('primary'),

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
                Tables\Filters\SelectFilter::make('sede')
                    ->label('Sede')
                    ->options(Sede::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['value']) {
                            return $query;
                        }
                        return $query->whereHas('enrollments', function ($q) use ($data) {
                            $q->where('sede_id', $data['value'])
                              ->whereHas('schoolYear', fn($sq) => $sq->where('is_active', true));
                        });
                    })
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('grade')
                    ->label('Grado')
                    ->options(Grade::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['value']) {
                            return $query;
                        }
                        return $query->whereHas('enrollments', function ($q) use ($data) {
                            $q->where('grade_id', $data['value'])
                              ->whereHas('schoolYear', fn($sq) => $sq->where('is_active', true));
                        });
                    })
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
                    ->modalDescription('La contraseña será restablecida al número de identificación del estudiante.')
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
