<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssignmentResource\Pages;
use App\Models\Assignment;
use App\Models\Employee;
use App\Models\User;
use App\Models\WorkLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Assignment';

    protected static ?string $modelLabel = 'Assignment';

    protected static ?string $pluralModelLabel = 'Assignment';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['employee.user', 'workLocation', 'supervisor'])
            ->withCount('attendanceLogs');

        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['super_admin', 'admin_hr', 'management'])) {
            return $query;
        }

        if ($user->hasAnyRole(['manajer', 'supervisor'])) {
            return $query->where('supervisor_user_id', $user->id);
        }

        return $query->whereHas('employee', fn (Builder $employeeQuery) => $employeeQuery
            ->where('user_id', $user->id));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pegawai dan Lokasi')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Pegawai')
                            ->options(fn (): array => Employee::query()
                                ->with('user')
                                ->where('status', 'active')
                                ->get()
                                ->mapWithKeys(fn (Employee $employee): array => [
                                    $employee->id => trim(($employee->employee_code ? "{$employee->employee_code} - " : '') . ($employee->user?->name ?? 'Tanpa user')),
                                ])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('work_location_id')
                            ->label('Lokasi Kerja')
                            ->options(fn (): array => WorkLocation::query()
                                ->where('status', 'active')
                                ->orderBy('location_name')
                                ->pluck('location_name', 'id')
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('supervisor_user_id')
                            ->label('Supervisor')
                            ->options(fn (): array => User::query()
                                ->whereHas('roles', fn (Builder $query) => $query->whereIn('name', ['supervisor', 'manajer', 'admin_hr']))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->default(fn (): ?string => auth()->user()?->hasAnyRole(['manajer', 'supervisor']) ? auth()->id() : null)
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Jadwal')
                    ->schema([
                        Forms\Components\DatePicker::make('assignment_date')
                            ->label('Tanggal')
                            ->native(false)
                            ->required(),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Jam Mulai')
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('Jam Selesai')
                            ->seconds(false)
                            ->after('start_time')
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpan('full'),
                        Forms\Components\Select::make('assignment_status')
                            ->label('Status')
                            ->options([
                                'terjadwal' => 'Terjadwal',
                                'berjalan' => 'Berjalan',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('terjadwal')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assignment_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('workLocation.location_name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('assignment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'terjadwal' => 'info',
                        'berjalan' => 'success',
                        'selesai' => 'gray',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'terjadwal' => 'Terjadwal',
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('attendance_logs_count')
                    ->label('Absensi')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->defaultSort('assignment_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('assignment_status')
                    ->label('Status')
                    ->options([
                        'terjadwal' => 'Terjadwal',
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Assignment $record): bool => ! $record->attendanceLogs()->exists()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Assignment $record): bool => ! $record->attendanceLogs()->exists()),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
