<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SuratTugasResource\Pages;
use App\Models\SuratTugas;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class SuratTugasResource extends Resource
{
    protected static ?string $model = SuratTugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Surat Tugas';

    protected static ?string $modelLabel = 'Surat Tugas';

    protected static ?string $pluralModelLabel = 'Surat Tugas';

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user', 'creator'])
            ->withCount('attendanceLogs');

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
                Forms\Components\Section::make('Karyawan')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Karyawan')
                            ->options(fn (): array => static::employeeOptions())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->rules([
                                fn () => function (string $attribute, mixed $value, Closure $fail): void {
                                    if (! static::canAssignEmployee((string) $value)) {
                                        $fail('Karyawan tidak sesuai dengan akses Anda.');
                                    }
                                },
                            ]),
                        Forms\Components\Hidden::make('created_by')
                            ->default(fn (): ?string => auth()->id()),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Periode dan Lokasi')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->minDate(fn (): ?string => auth()->user()?->hasRole('super_admin') ? null : now()->toDateString())
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->afterOrEqual('start_date')
                            ->required(),
                        Forms\Components\TextInput::make('location_name')
                            ->label('Nama Lokasi')
                            ->maxLength(255)
                            ->required()
                            ->columnSpan('full'),
                        Forms\Components\TextInput::make('target_lat')
                            ->label('Latitude')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->required(),
                        Forms\Components\TextInput::make('target_lng')
                            ->label('Longitude')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->required(),
                        Forms\Components\TextInput::make('radius_meters')
                            ->label('Radius Meter')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->default(300)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dokumen dan Status')
                    ->schema([
                        Forms\Components\FileUpload::make('document_url')
                            ->label('Dokumen PDF')
                            ->disk('public')
                            ->directory('surat-tugas')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'ACTIVE' => 'Aktif',
                                'CANCELLED' => 'Dibatalkan',
                                'COMPLETED' => 'Selesai',
                            ])
                            ->default('ACTIVE')
                            ->required(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location_name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('radius_meters')
                    ->label('Radius')
                    ->suffix(' m')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ACTIVE' => 'success',
                        'CANCELLED' => 'warning',
                        'COMPLETED' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ACTIVE' => 'Aktif',
                        'CANCELLED' => 'Dibatalkan',
                        'COMPLETED' => 'Selesai',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Pembuat')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('attendance_logs_count')
                    ->label('Absensi')
                    ->counts('attendanceLogs')
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_url')
                    ->label('Dokumen')
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'PDF' : '-')
                    ->url(fn (SuratTugas $record): ?string => filled($record->document_url)
                        ? Storage::disk('public')->url($record->document_url)
                        : null)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ACTIVE' => 'Aktif',
                        'CANCELLED' => 'Dibatalkan',
                        'COMPLETED' => 'Selesai',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (SuratTugas $record): bool => ! $record->attendanceLogs()->exists()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (SuratTugas $record): bool => ! $record->attendanceLogs()->exists()),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratTugas::route('/'),
            'create' => Pages\CreateSuratTugas::route('/create'),
            'edit' => Pages\EditSuratTugas::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function employeeOptions(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $query = User::query()
            ->orderBy('name');

        if ($user->hasRole('manajer') && ! $user->hasAnyRole(['super_admin', 'admin_hr'])) {
            $query->where('manager_id', $user->getKey());
        } else {
            $query->whereDoesntHave('roles', fn (Builder $roleQuery) => $roleQuery->whereIn('name', ['super_admin', 'admin_hr']));
        }

        return $query->pluck('name', 'id')->all();
    }

    private static function canAssignEmployee(string $userId): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'admin_hr'])) {
            return User::query()
                ->whereKey($userId)
                ->whereDoesntHave('roles', fn (Builder $roleQuery) => $roleQuery->whereIn('name', ['super_admin', 'admin_hr']))
                ->exists();
        }

        return $user->hasRole('manajer')
            && User::query()
                ->whereKey($userId)
                ->where('manager_id', $user->getKey())
                ->exists();
    }
}
