<?php

namespace App\Filament\Resources\SchoolYearResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PeriodsRelationManager extends RelationManager
{
    protected static string $relationship = 'periods';

    protected static ?string $title = 'Periodos';

    protected static ?string $modelLabel = 'Periodo';

    protected static ?string $pluralModelLabel = 'Periodos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('number')
                    ->label('Número de Periodo')
                    ->options([
                        1 => 'Periodo 1',
                        2 => 'Periodo 2',
                        3 => 'Periodo 3',
                        4 => 'Periodo 4',
                    ])
                    ->required()
                    ->unique(
                        table: 'periods',
                        column: 'number',
                        ignoreRecord: true,
                        modifyRuleUsing: fn ($rule) => $rule->where('school_year_id', $this->ownerRecord->id)
                    ),

                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Fecha de Inicio'),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Fecha de Fin')
                    ->afterOrEqual('start_date'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('N°')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->placeholder('Sin definir'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->placeholder('Sin definir'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn (): bool => $this->ownerRecord->periods()->count() < 4),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('number');
    }
}
