<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestStudentsTable extends BaseWidget
{
    protected static ?string $heading = 'Últimos Estudiantes Registrados';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'half';
    
    protected static ?string $maxHeight = '400px';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('role', 'student')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('identification')
                    ->label('Identificación')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false)
            ->striped();
    }
}
