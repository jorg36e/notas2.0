<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SedeResource\Pages;
use App\Filament\Resources\SedeResource\RelationManagers;
use App\Models\Sede;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SedeResource extends Resource
{
    protected static ?string $model = Sede::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Configuración Académica';

    protected static ?string $modelLabel = 'Sede';

    protected static ?string $pluralModelLabel = 'Sedes';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Sede')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Sede')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Sede Principal'),

                        Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->maxLength(50)
                            ->placeholder('Ej: SP001')
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('address')
                            ->label('Dirección')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Sede Activa')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin código'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->placeholder('Sin dirección'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->placeholder('Sin teléfono'),

                Tables\Columns\TextColumn::make('subjects_count')
                    ->label('Asignaturas')
                    ->counts('subjects')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
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
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSedes::route('/'),
            'create' => Pages\CreateSede::route('/create'),
            'edit' => Pages\EditSede::route('/{record}/edit'),
        ];
    }
}
