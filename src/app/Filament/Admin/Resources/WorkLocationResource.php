<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WorkLocationResource\Pages;
use App\Models\WorkLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkLocationResource extends Resource
{
    protected static ?string $model = WorkLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Lokasi Kerja';

    protected static ?string $modelLabel = 'Lokasi Kerja';

    protected static ?string $pluralModelLabel = 'Lokasi Kerja';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['super_admin', 'admin_hr', 'management'])) {
            return $query;
        }

        if ($user->hasAnyRole(['manajer', 'supervisor'])) {
            return $query->whereHas('assignments', fn (Builder $assignmentQuery) => $assignmentQuery
                ->where('supervisor_user_id', $user->id));
        }

        return $query->whereHas('assignments.employee', fn (Builder $employeeQuery) => $employeeQuery
            ->where('user_id', $user->id));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('location_name')
                            ->label('Nama Lokasi')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nama Klien')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->required()
                            ->rows(3)
                            ->columnSpan('full'),
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->required(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->required(),
                        Forms\Components\TextInput::make('radius_tolerance')
                            ->label('Radius Toleransi')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->default(300)
                            ->suffix('m')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location_name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Klien')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('radius_tolerance')
                    ->label('Radius')
                    ->suffix(' m')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        default => $state,
                    }),
            ])
            ->defaultSort('location_name')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkLocations::route('/'),
            'create' => Pages\CreateWorkLocation::route('/create'),
            'edit' => Pages\EditWorkLocation::route('/{record}/edit'),
        ];
    }
}
