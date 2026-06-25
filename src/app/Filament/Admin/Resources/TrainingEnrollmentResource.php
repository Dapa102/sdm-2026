<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TrainingEnrollmentResource\Pages;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Services\MeritService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrainingEnrollmentResource extends Resource
{
    protected static ?string $model = TrainingEnrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Pendaftaran Pelatihan';

    protected static ?string $modelLabel = 'Pendaftaran';

    protected static ?string $pluralModelLabel = 'Pendaftaran Pelatihan';

    protected static ?int $navigationSort = 70;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user', 'training'])
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
                Forms\Components\Section::make('Pendaftaran Pelatihan')
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn (): ?string => auth()->id()),
                        Forms\Components\Select::make('training_id')
                            ->label('Pilih Pelatihan')
                            ->options(function () use ($currentBalance): array {
                                return Training::where('is_active', true)
                                    ->orderBy('title')
                                    ->get()
                                    ->mapWithKeys(fn ($training) => [
                                        $training->id => $training->title . 
                                            ' (Min: ' . $training->minimum_points . ' pt, Durasi: ' . $training->duration_hours . ' jam)' .
                                            ($currentBalance < $training->minimum_points ? ' [Tidak memenuhi syarat]' : ''),
                                    ])
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->helperText(fn () => "Saldo point Anda: {$currentBalance} pt")
                            ->disabled(fn (?TrainingEnrollment $record): bool => $record !== null),
                        Forms\Components\Placeholder::make('training_info')
                            ->label('Detail Pelatihan')
                            ->content(function (Forms\Get $get) use ($meritService): string {
                                $trainingId = $get('training_id');
                                if (! $trainingId) {
                                    return 'Pilih pelatihan terlebih dahulu';
                                }

                                $training = Training::find($trainingId);
                                if (! $training) {
                                    return '-';
                                }

                                $user = auth()->user();
                                if (! $user) {
                                    return '-';
                                }

                                $balance = $meritService->getBalance($user);
                                $eligible = $balance >= $training->minimum_points;

                                $alreadyEnrolled = TrainingEnrollment::where('user_id', $user->id)
                                    ->where('training_id', $training->id)
                                    ->exists();

                                return sprintf(
                                    "%s\n\nMin. Point: %d pt\nDurasi: %d jam\n%s%s",
                                    $training->description,
                                    $training->minimum_points,
                                    $training->duration_hours,
                                    $eligible ? '' : "\n⚠️ Saldo point Anda tidak mencukupi!",
                                    $alreadyEnrolled ? "\n⚠️ Anda sudah terdaftar di pelatihan ini!" : ''
                                );
                            })
                            ->visible(fn (Forms\Get $get): bool => filled($get('training_id')))
                            ->columnSpan('full'),
                        Forms\Components\Hidden::make('status')
                            ->default('ENROLLED'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Status Penyelesaian')
                    ->schema([
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),
                        Forms\Components\TextInput::make('completed_at')
                            ->label('Tanggal Selesai')
                            ->disabled(),
                        Forms\Components\TextInput::make('created_at')
                            ->label('Tanggal Daftar')
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->visible(fn (?TrainingEnrollment $record): bool => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: auth()->user()?->hasRole('karyawan')),
                Tables\Columns\TextColumn::make('training.title')
                    ->label('Pelatihan')
                    ->searchable()
                    ->wrap()
                    ->limit(40),
                Tables\Columns\TextColumn::make('training.minimum_points')
                    ->label('Min. Point')
                    ->numeric()
                    ->suffix(' pt')
                    ->alignEnd()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('training.duration_hours')
                    ->label('Durasi')
                    ->numeric()
                    ->suffix(' jam')
                    ->alignEnd()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'COMPLETED' => 'success',
                        'ENROLLED' => 'warning',
                        'CANCELLED' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'COMPLETED' => 'Selesai',
                        'ENROLLED' => 'Terdaftar',
                        'CANCELLED' => 'Dibatalkan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Tanggal Selesai')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'ENROLLED' => 'Terdaftar',
                        'COMPLETED' => 'Selesai',
                        'CANCELLED' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('complete')
                    ->label('Tandai Selesai')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (TrainingEnrollment $record): bool => 
                        $record->status === 'ENROLLED' && 
                        auth()->user()?->hasAnyRole(['super_admin', 'admin_hr'])
                    )
                    ->action(function (TrainingEnrollment $record): void {
                        $meritService = app(MeritService::class);

                        $record->update([
                            'status' => 'COMPLETED',
                            'completed_at' => now(),
                        ]);

                        try {
                            $transaction = $meritService->processTrainingCompletion($record);

                            if ($transaction) {
                                Notification::make()
                                    ->success()
                                    ->title('Pelatihan Selesai')
                                    ->body("Point +25 telah ditambahkan untuk {$record->user->name}.")
                                    ->send();
                            } else {
                                Notification::make()
                                    ->warning()
                                    ->title('Pelatihan Selesai')
                                    ->body('Status diperbarui, tapi point sudah pernah diberikan sebelumnya.')
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menambahkan Point')
                                ->body('Status diperbarui, tapi terjadi kesalahan: ' . $e->getMessage())
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                // No bulk actions
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainingEnrollments::route('/'),
            'create' => Pages\CreateTrainingEnrollment::route('/create'),
            'view' => Pages\ViewTrainingEnrollment::route('/{record}'),
        ];
    }
}
