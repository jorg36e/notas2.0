<?php

namespace App\Filament\Resources\SedeResource\RelationManagers;

use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    protected static ?string $title = 'Asignaturas Habilitadas';

    protected static ?string $modelLabel = 'Asignatura';

    protected static ?string $pluralModelLabel = 'Asignaturas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->label('Asignatura')
                    ->options(Subject::pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pivot.created_at')
                    ->label('Habilitada el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Agregar Asignatura')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'code']),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Quitar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Quitar seleccionadas'),
                ]),
            ])
            ->defaultSort('name');
    }
}
