<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MeritTransactionResource\Pages;
use App\Models\MeritTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MeritTransactionResource extends Resource
{
    protected static ?string $model = MeritTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Histori Point';

    protected static ?string $modelLabel = 'Transaksi Merit';

    protected static ?string $pluralModelLabel = 'Histori Point';

    protected static ?int $navigationSort = 30;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user', 'reference'])
            ->latest();

        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['super_admin', 'admin_hr'])) {
            return $query;
        }

        if ($user->hasRole('manajer')) {
            return $query->whereHas('user', fn (Builder $subQuery) => $subQuery->where('manager_id', $user->getKey()));
        }

        return $query->where('user_id', $user->getKey());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Transaksi')
                    ->schema([
                        Forms\Components\TextInput::make('user.name')
                            ->label('Karyawan')
                            ->disabled(),
                        Forms\Components\TextInput::make('points')
                            ->label('Point')
                            ->disabled()
                            ->suffix('pt'),
                        Forms\Components\TextInput::make('type')
                            ->label('Tipe')
                            ->disabled(),
                        Forms\Components\TextInput::make('source')
                            ->label('Sumber')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->disabled()
                            ->columnSpan('full'),
                        Forms\Components\TextInput::make('reference_type')
                            ->label('Referensi')
                            ->disabled(),
                        Forms\Components\TextInput::make('reference_id')
                            ->label('ID Referensi')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Masa Berlaku')
                    ->schema([
                        Forms\Components\TextInput::make('expiry_date')
                            ->label('Tanggal Kadaluarsa')
                            ->disabled(),
                        Forms\Components\Toggle::make('is_expired')
                            ->label('Kadaluarsa')
                            ->disabled(),
                        Forms\Components\TextInput::make('created_at')
                            ->label('Dibuat Pada')
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: auth()->user()?->hasRole('karyawan')),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CREDIT' => 'success',
                        'DEBIT' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'CREDIT' => 'Masuk',
                        'DEBIT' => 'Keluar',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('points')
                    ->label('Point')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => ($state > 0 ? '+' : '') . $state)
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'attendance_approval' => 'Absensi Disetujui',
                        'training_completion' => 'Pelatihan Selesai',
                        'reward_redemption' => 'Penukaran Reward',
                        'manual_adjustment' => 'Penyesuaian Manual',
                        default => $state,
                    })
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Kadaluarsa')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('is_expired')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Kadaluarsa' : 'Aktif')
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'CREDIT' => 'Masuk',
                        'DEBIT' => 'Keluar',
                    ]),
                Tables\Filters\SelectFilter::make('source')
                    ->label('Sumber')
                    ->options([
                        'attendance_approval' => 'Absensi Disetujui',
                        'training_completion' => 'Pelatihan Selesai',
                        'reward_redemption' => 'Penukaran Reward',
                        'manual_adjustment' => 'Penyesuaian Manual',
                    ]),
                Tables\Filters\TernaryFilter::make('is_expired')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Kadaluarsa')
                    ->falseLabel('Aktif'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions - transactions are immutable
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeritTransactions::route('/'),
            'view' => Pages\ViewMeritTransaction::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
