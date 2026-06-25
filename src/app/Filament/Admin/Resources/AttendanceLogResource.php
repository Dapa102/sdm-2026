<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceLogResource\Pages;
use App\Models\AttendanceApproval;
use App\Models\AttendanceLog;
use App\Services\AttendanceService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;

class AttendanceLogResource extends Resource
{
    protected static ?string $model = AttendanceLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Absensi';

    protected static ?string $modelLabel = 'Absensi';

    protected static ?string $pluralModelLabel = 'Absensi';

    protected static ?int $navigationSort = 20;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user', 'employee', 'assignment.workLocation', 'suratTugas', 'approver'])
            ->latest('attendance_date');

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
                Forms\Components\Section::make('Informasi Karyawan')
                    ->schema([
                        Forms\Components\TextInput::make('user.name')
                            ->label('Karyawan')
                            ->disabled(),
                        Forms\Components\TextInput::make('attendance_date')
                            ->label('Tanggal')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Google Maps')
                    ->schema([
                        Forms\Components\Placeholder::make('target_location_map')
                            ->label('Lokasi Tugas')
                            ->content(fn (?AttendanceLog $record): HtmlString => static::mapLink(
                                $record?->assignment?->workLocation?->latitude ?? $record?->suratTugas?->target_lat,
                                $record?->assignment?->workLocation?->longitude ?? $record?->suratTugas?->target_lng,
                                'Buka lokasi tugas',
                            )),
                        Forms\Components\Placeholder::make('check_in_map')
                            ->label('Titik Check-In')
                            ->content(fn (?AttendanceLog $record): HtmlString => static::mapLink(
                                $record?->check_in_lat,
                                $record?->check_in_lng,
                                'Buka titik check-in',
                            )),
                        Forms\Components\Placeholder::make('check_out_map')
                            ->label('Titik Check-Out')
                            ->content(fn (?AttendanceLog $record): HtmlString => static::mapLink(
                                $record?->check_out_lat,
                                $record?->check_out_lng,
                                'Buka titik check-out',
                            )),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Check-In')
                    ->schema([
                        Forms\Components\TextInput::make('check_in_at')
                            ->label('Waktu Check-In')
                            ->disabled(),
                        Forms\Components\TextInput::make('check_in_lat')
                            ->label('Latitude')
                            ->disabled(),
                        Forms\Components\TextInput::make('check_in_lng')
                            ->label('Longitude')
                            ->disabled(),
                        Forms\Components\TextInput::make('check_in_distance_meters')
                            ->label('Jarak')
                            ->suffix(' m')
                            ->disabled(),
                        Forms\Components\TextInput::make('check_in_photo_url')
                            ->label('Foto Check-In')
                            ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Ada' : '-')
                            ->disabled()
                            ->columnSpan('full'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Check-Out')
                    ->schema([
                        Forms\Components\TextInput::make('check_out_at')
                            ->label('Waktu Check-Out')
                            ->disabled(),
                        Forms\Components\TextInput::make('check_out_lat')
                            ->label('Latitude')
                            ->disabled(),
                        Forms\Components\TextInput::make('check_out_lng')
                            ->label('Longitude')
                            ->disabled(),
                        Forms\Components\TextInput::make('check_out_distance_meters')
                            ->label('Jarak')
                            ->suffix(' m')
                            ->disabled(),
                        Forms\Components\TextInput::make('check_out_photo_url')
                            ->label('Foto Check-Out')
                            ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Ada' : '-')
                            ->disabled()
                            ->columnSpan('full'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Status dan Approval')
                    ->schema([
                        Forms\Components\TextInput::make('location_status')
                            ->label('Status Lokasi')
                            ->disabled(),
                        Forms\Components\TextInput::make('approval_status')
                            ->label('Status Approval')
                            ->disabled(),
                        Forms\Components\TextInput::make('attendance_status')
                            ->label('Status Kehadiran')
                            ->disabled(),
                        Forms\Components\TextInput::make('verification_status')
                            ->label('Status Validasi')
                            ->disabled(),
                        Forms\Components\TextInput::make('approver.name')
                            ->label('Disetujui Oleh')
                            ->disabled(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->disabled()
                            ->columnSpan('full'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->disabled()
                            ->columnSpan('full'),
                    ])
                    ->columns(3),
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
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('suratTugas.location_name')
                    ->label('Lokasi Tugas')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('check_in_at')
                    ->label('Check-In')
                    ->dateTime('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out_at')
                    ->label('Check-Out')
                    ->dateTime('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location_status')
                    ->label('Lokasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'VALID' => 'success',
                        'OUT_OF_RANGE' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'VALID' => 'Valid',
                        'OUT_OF_RANGE' => 'Luar Radius',
                        'UNKNOWN' => 'GPS Gagal',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('attendance_status')
                    ->label('Kehadiran')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'hadir' => 'success',
                        'terlambat' => 'warning',
                        'izin', 'sakit' => 'info',
                        'tidak_hadir' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'tidak_hadir' => 'Tidak Hadir',
                        default => $state ?? '-',
                    }),
                Tables\Columns\TextColumn::make('verification_status')
                    ->label('Validasi')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'valid' => 'success',
                        'perlu_verifikasi', 'di_luar_lokasi' => 'warning',
                        'ditolak' => 'danger',
                        'dikoreksi_manual' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'valid' => 'Valid',
                        'perlu_verifikasi' => 'Perlu Verifikasi',
                        'di_luar_lokasi' => 'Di Luar Lokasi',
                        'ditolak' => 'Ditolak',
                        'dikoreksi_manual' => 'Dikoreksi Manual',
                        default => $state ?? '-',
                    }),
                Tables\Columns\TextColumn::make('approval_status')
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
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Status Approval')
                    ->options([
                        'PENDING' => 'Menunggu',
                        'APPROVED' => 'Disetujui',
                        'REJECTED' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('location_status')
                    ->label('Status Lokasi')
                    ->options([
                        'VALID' => 'Valid',
                        'OUT_OF_RANGE' => 'Luar Radius',
                        'UNKNOWN' => 'GPS Gagal',
                    ]),
                Tables\Filters\SelectFilter::make('attendance_status')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'tidak_hadir' => 'Tidak Hadir',
                    ]),
                Tables\Filters\SelectFilter::make('verification_status')
                    ->label('Status Validasi')
                    ->options([
                        'valid' => 'Valid',
                        'perlu_verifikasi' => 'Perlu Verifikasi',
                        'di_luar_lokasi' => 'Di Luar Lokasi',
                        'ditolak' => 'Ditolak',
                        'dikoreksi_manual' => 'Dikoreksi Manual',
                    ]),
                Tables\Filters\Filter::make('attendance_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '<=', $date),
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
                    ->visible(fn (AttendanceLog $record): bool => $record->approval_status === 'PENDING')
                    ->action(function (AttendanceLog $record): void {
                        $record->update([
                            'approval_status' => 'APPROVED',
                            'verification_status' => 'valid',
                            'approved_by' => auth()->id(),
                        ]);

                        AttendanceApproval::create([
                            'attendance_log_id' => $record->id,
                            'approved_by' => auth()->id(),
                            'approval_status' => 'disetujui',
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Absensi Disetujui')
                            ->body('Absensi telah divalidasi.')
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (AttendanceLog $record): bool => $record->approval_status === 'PENDING')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (AttendanceLog $record, array $data): void {
                        $record->update([
                            'approval_status' => 'REJECTED',
                            'verification_status' => 'ditolak',
                            'approved_by' => auth()->id(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        AttendanceApproval::create([
                            'attendance_log_id' => $record->id,
                            'approved_by' => auth()->id(),
                            'approval_status' => 'ditolak',
                            'approval_note' => $data['rejection_reason'],
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Absensi Ditolak')
                            ->body('Absensi telah ditolak.')
                            ->send();
                    }),
                Tables\Actions\Action::make('correct')
                    ->label('Koreksi')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->fillForm(fn (AttendanceLog $record): array => [
                        'check_in_at' => $record->check_in_at,
                        'check_out_at' => $record->check_out_at,
                        'attendance_status' => $record->attendance_status,
                        'notes' => $record->notes,
                    ])
                    ->form([
                        Forms\Components\DateTimePicker::make('check_in_at')
                            ->label('Waktu Masuk')
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('check_out_at')
                            ->label('Waktu Pulang')
                            ->seconds(false),
                        Forms\Components\Select::make('attendance_status')
                            ->label('Status Kehadiran')
                            ->options([
                                'hadir' => 'Hadir',
                                'terlambat' => 'Terlambat',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                                'tidak_hadir' => 'Tidak Hadir',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->maxLength(500)
                            ->columnSpan('full'),
                        Forms\Components\Textarea::make('correction_reason')
                            ->label('Alasan Koreksi')
                            ->required()
                            ->maxLength(500)
                            ->columnSpan('full'),
                    ])
                    ->action(function (AttendanceLog $record, array $data): void {
                        $reason = $data['correction_reason'];
                        unset($data['correction_reason']);

                        app(AttendanceService::class)->correctAttendance(
                            auth()->user(),
                            $record,
                            $data,
                            $reason,
                        );

                        Notification::make()
                            ->success()
                            ->title('Absensi Dikoreksi')
                            ->body('Riwayat koreksi telah disimpan.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulkApprove')
                    ->label('Setujui Terpilih')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records): void {
                        $approved = 0;

                        foreach ($records as $record) {
                            if ($record->approval_status === 'PENDING') {
                                $record->update([
                                    'approval_status' => 'APPROVED',
                                    'verification_status' => 'valid',
                                    'approved_by' => auth()->id(),
                                ]);

                                AttendanceApproval::create([
                                    'attendance_log_id' => $record->id,
                                    'approved_by' => auth()->id(),
                                    'approval_status' => 'disetujui',
                                    'approved_at' => now(),
                                ]);

                                $approved++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Bulk Approval Berhasil')
                            ->body("{$approved} absensi telah disetujui.")
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceLogs::route('/'),
            'view' => Pages\ViewAttendanceLog::route('/{record}'),
        ];
    }

    private static function mapLink(mixed $latitude, mixed $longitude, string $label): HtmlString
    {
        $url = static::googleMapsUrl($latitude, $longitude);

        if ($url === null) {
            return new HtmlString('<span class="text-sm text-gray-500 dark:text-gray-400">Koordinat belum tersedia</span>');
        }

        return new HtmlString(sprintf(
            '<a href="%s" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">%s</a>',
            e($url),
            e($label),
        ));
    }

    private static function googleMapsUrl(mixed $latitude, mixed $longitude): ?string
    {
        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return null;
        }

        return sprintf('https://www.google.com/maps/search/?api=1&query=%s,%s', $latitude, $longitude);
    }
}
