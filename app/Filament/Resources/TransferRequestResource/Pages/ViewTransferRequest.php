<?php

namespace App\Filament\Resources\TransferRequestResource\Pages;

use App\Filament\Resources\TransferRequestResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewTransferRequest extends ViewRecord
{
    protected static string $resource = TransferRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información del Estudiante')
                    ->schema([
                        Infolists\Components\TextEntry::make('student.name')
                            ->label('Estudiante'),
                        Infolists\Components\TextEntry::make('student.identification')
                            ->label('Identificación'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detalles del Traslado')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('originSede.name')
                                    ->label('Sede Origen')
                                    ->badge()
                                    ->color('danger'),
                                Infolists\Components\TextEntry::make('destinationSede.name')
                                    ->label('Sede Destino')
                                    ->badge()
                                    ->color('success'),
                            ]),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('originGrade.name')
                                    ->label('Grado Origen'),
                                Infolists\Components\TextEntry::make('destinationGrade.name')
                                    ->label('Grado Destino'),
                            ]),
                        Infolists\Components\TextEntry::make('status')
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
                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state === 'direct' ? 'Traslado Directo' : 'Solicitud'),
                    ]),

                Infolists\Components\Section::make('Información Adicional')
                    ->schema([
                        Infolists\Components\TextEntry::make('reason')
                            ->label('Motivo')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Motivo de Rechazo')
                            ->visible(fn ($record) => $record->status === 'rejected')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notas')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Registro')
                    ->schema([
                        Infolists\Components\TextEntry::make('requester.name')
                            ->label('Solicitado por'),
                        Infolists\Components\TextEntry::make('approver.name')
                            ->label('Procesado por'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Solicitud')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('processed_at')
                            ->label('Fecha de Procesamiento')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
