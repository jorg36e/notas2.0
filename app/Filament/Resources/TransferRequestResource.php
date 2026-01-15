<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferRequestResource\Pages;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\TransferRequest;
use App\Models\User;
use App\Services\TransferService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransferRequestResource extends Resource
{
    protected static ?string $model = TransferRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Traslados';

    protected static ?string $modelLabel = 'Traslado';

    protected static ?string $pluralModelLabel = 'Traslados';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Traslado')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Estudiante')
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                $activeYear = SchoolYear::where('is_active', true)->first();
                                if (!$activeYear) return [];
                                
                                return Enrollment::where('school_year_id', $activeYear->id)
                                    ->where('status', 'active')
                                    ->whereDoesntHave('transferRequests', function ($q) {
                                        $q->where('status', TransferRequest::STATUS_PENDING);
                                    })
                                    ->with(['student', 'sede', 'grade'])
                                    ->get()
                                    ->mapWithKeys(function ($enrollment) {
                                        return [
                                            $enrollment->student_id => "{$enrollment->student->name} - {$enrollment->sede->name} ({$enrollment->grade->name})"
                                        ];
                                    });
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $activeYear = SchoolYear::where('is_active', true)->first();
                                    $enrollment = Enrollment::where('student_id', $state)
                                        ->where('school_year_id', $activeYear->id)
                                        ->first();
                                    if ($enrollment) {
                                        $set('origin_sede_id', $enrollment->sede_id);
                                        $set('origin_grade_id', $enrollment->grade_id);
                                    }
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('origin_sede_id')
                                    ->label('Sede Origen')
                                    ->options(Sede::where('is_active', true)->pluck('name', 'id'))
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\Select::make('origin_grade_id')
                                    ->label('Grado Origen')
                                    ->options(Grade::where('is_active', true)->orderBy('level')->pluck('name', 'id'))
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('destination_sede_id')
                                    ->label('Sede Destino')
                                    ->options(Sede::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->native(false)
                                    ->searchable(),

                                Forms\Components\Select::make('destination_grade_id')
                                    ->label('Grado Destino')
                                    ->options(Grade::where('is_active', true)->orderBy('level')->pluck('name', 'id'))
                                    ->required()
                                    ->native(false)
                                    ->searchable(),
                            ]),

                        Forms\Components\Textarea::make('reason')
                            ->label('Motivo del Traslado')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas Adicionales')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('originSede.name')
                    ->label('Sede Origen')
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('destinationSede.name')
                    ->label('Sede Destino')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('originGrade.name')
                    ->label('Grado Origen')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('destinationGrade.name')
                    ->label('Grado Destino')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'direct' ? 'Directo' : 'Solicitud')
                    ->color(fn ($state) => $state === 'direct' ? 'info' : 'warning'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Solicitado por')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Procesado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        'cancelled' => 'Cancelado',
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'direct' => 'Directo (Admin)',
                        'request' => 'Solicitud (Profesor)',
                    ]),

                Tables\Filters\SelectFilter::make('origin_sede_id')
                    ->label('Sede Origen')
                    ->options(Sede::where('is_active', true)->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('destination_sede_id')
                    ->label('Sede Destino')
                    ->options(Sede::where('is_active', true)->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (TransferRequest $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar Traslado')
                    ->modalDescription(fn (TransferRequest $record) => 
                        "¿Aprobar traslado de {$record->student->name} de {$record->originSede->name} a {$record->destinationSede->name}?"
                    )
                    ->action(function (TransferRequest $record) {
                        $service = new TransferService();
                        $service->approveTransfer($record, auth()->id());
                        
                        Notification::make()
                            ->title('Traslado aprobado')
                            ->body("El estudiante {$record->student->name} ha sido trasladado exitosamente.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (TransferRequest $record) => $record->isPending())
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motivo del Rechazo')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (TransferRequest $record, array $data) {
                        $service = new TransferService();
                        $service->rejectTransfer($record, auth()->id(), $data['rejection_reason']);
                        
                        Notification::make()
                            ->title('Traslado rechazado')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferRequests::route('/'),
            'create' => Pages\CreateTransferRequest::route('/create'),
            'view' => Pages\ViewTransferRequest::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
