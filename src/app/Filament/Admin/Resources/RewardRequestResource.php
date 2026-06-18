<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RewardRequestResource\Pages;
use App\Models\RewardCatalog;
use App\Models\RewardRequest;
use App\Services\MeritService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RewardRequestResource extends Resource
{
    protected static ?string $model = RewardRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Permintaan Reward';

    protected static ?string $modelLabel = 'Permintaan Reward';

    protected static ?string $pluralModelLabel = 'Permintaan Reward';

    protected static ?int $navigationSort = 50;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user', 'rewardCatalog', 'approver'])
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
        $user = auth()->user();
        $meritService = app(MeritService::class);
        $currentBalance = $user ? $meritService->getBalance($user) : 0;

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Permintaan')
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn (): ?string => auth()->id()),
                        Forms\Components\Select::make('reward_catalog_id')
                            ->label('Pilih Reward')
                            ->options(function (): array {
                                return RewardCatalog::where('is_active', true)
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn ($reward) => [
                                        $reward->id => $reward->name . ' (' . $reward->point_cost . ' pt)',
                                    ])
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) use ($meritService) {
                                if ($state) {
                                    $reward = RewardCatalog::find($state);
                                    $user = auth()->user();
                                    if ($reward && $user) {
                                        $balance = $meritService->getBalance($user);
                                        $set('_balance_check', $balance >= $reward->point_cost);
                                    }
                                }
                            })
                            ->helperText(fn () => "Saldo point Anda: {$currentBalance} pt")
                            ->disabled(fn (?RewardRequest $record): bool => $record !== null),
                        Forms\Components\Placeholder::make('reward_info')
                            ->label('Info Reward')
                            ->content(function (Forms\Get $get) use ($meritService): string {
                                $rewardId = $get('reward_catalog_id');
                                if (! $rewardId) {
                                    return 'Pilih reward terlebih dahulu';
                                }

                                $reward = RewardCatalog::find($rewardId);
                                if (! $reward) {
                                    return '-';
                                }

                                $user = auth()->user();
                                if (! $user) {
                                    return '-';
                                }

                                $balance = $meritService->getBalance($user);
                                $sufficient = $balance >= $reward->point_cost;

                                return sprintf(
                                    '%s - %d pt%s',
                                    $reward->description,
                                    $reward->point_cost,
                                    $sufficient ? '' : ' (Saldo tidak cukup!)'
                                );
                            })
                            ->visible(fn (Forms\Get $get): bool => filled($get('reward_catalog_id'))),
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan Pengajuan')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->helperText('Jelaskan alasan Anda mengajukan reward ini')
                            ->disabled(fn (?RewardRequest $record): bool => $record !== null)
                            ->columnSpan('full'),
                        Forms\Components\Hidden::make('status')
                            ->default('PENDING'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Status Approval')
                    ->schema([
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),
                        Forms\Components\TextInput::make('approver.name')
                            ->label('Disetujui Oleh')
                            ->disabled(),
                        Forms\Components\TextInput::make('approved_at')
                            ->label('Tanggal Approval')
                            ->disabled(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->disabled()
                            ->columnSpan('full'),
                    ])
                    ->columns(3)
                    ->visible(fn (?RewardRequest $record): bool => $record !== null && $record->status !== 'PENDING'),
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
                Tables\Columns\TextColumn::make('rewardCatalog.name')
                    ->label('Reward')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('rewardCatalog.point_cost')
                    ->label('Point')
                    ->numeric()
                    ->sortable()
                    ->suffix(' pt')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(40)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'APPROVED' => 'success',
                        'REJECTED' => 'danger',
                        'PENDING' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'APPROVED' => 'Disetujui',
                        'REJECTED' => 'Ditolak',
                        'PENDING' => 'Menunggu',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Approval')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'PENDING' => 'Menunggu',
                        'APPROVED' => 'Disetujui',
                        'REJECTED' => 'Ditolak',
                    ]),
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
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (RewardRequest $record): bool => $record->status === 'PENDING' && auth()->user()?->hasAnyRole(['super_admin', 'admin_hr', 'manajer']))
                    ->action(function (RewardRequest $record): void {
                        $meritService = app(MeritService::class);
                        $balance = $meritService->getBalance($record->user);

                        if ($balance < $record->rewardCatalog->point_cost) {
                            Notification::make()
                                ->danger()
                                ->title('Approval Gagal')
                                ->body("Saldo point karyawan tidak cukup. Dibutuhkan {$record->rewardCatalog->point_cost} pt, tersedia {$balance} pt.")
                                ->send();

                            return;
                        }

                        $record->update([
                            'status' => 'APPROVED',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);

                        try {
                            $meritService->processRewardApproval($record);

                            Notification::make()
                                ->success()
                                ->title('Reward Disetujui')
                                ->body("Point {$record->rewardCatalog->point_cost} telah dikurangi dari saldo karyawan.")
                                ->send();
                        } catch (\Exception $e) {
                            $record->update(['status' => 'PENDING']);

                            Notification::make()
                                ->danger()
                                ->title('Approval Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (RewardRequest $record): bool => $record->status === 'PENDING' && auth()->user()?->hasAnyRole(['super_admin', 'admin_hr', 'manajer']))
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (RewardRequest $record, array $data): void {
                        $record->update([
                            'status' => 'REJECTED',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Reward Ditolak')
                            ->body('Permintaan reward telah ditolak.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                // No bulk actions for reward requests
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRewardRequests::route('/'),
            'create' => Pages\CreateRewardRequest::route('/create'),
            'view' => Pages\ViewRewardRequest::route('/{record}'),
        ];
    }
}
