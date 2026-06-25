<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RewardCatalogResource\Pages;
use App\Models\RewardCatalog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RewardCatalogResource extends Resource
{
    protected static ?string $model = RewardCatalog::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Katalog Reward';

    protected static ?string $modelLabel = 'Reward';

    protected static ?string $pluralModelLabel = 'Katalog Reward';

    protected static ?int $navigationSort = 40;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Reward')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Reward')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpan('full'),
                        Forms\Components\TextInput::make('point_cost')
                            ->label('Biaya Point')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix('pt'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Hanya reward aktif yang bisa dipilih karyawan'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Reward')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('point_cost')
                    ->label('Biaya Point')
                    ->numeric()
                    ->sortable()
                    ->suffix(' pt')
                    ->alignEnd(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('reward_requests_count')
                    ->label('Total Permintaan')
                    ->counts('rewardRequests')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRewardCatalogs::route('/'),
            'create' => Pages\CreateRewardCatalog::route('/create'),
            'edit' => Pages\EditRewardCatalog::route('/{record}/edit'),
        ];
    }
}
