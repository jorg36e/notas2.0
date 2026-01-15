<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTeachersTable extends BaseWidget
{
    protected static ?string $heading = 'Docentes del Sistema';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 'half';
    
    protected static ?string $maxHeight = '400px';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('role', 'teacher')
                    ->orderBy('name')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->icon('heroicon-m-phone')
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->paginated(false)
            ->striped();
    }
}
