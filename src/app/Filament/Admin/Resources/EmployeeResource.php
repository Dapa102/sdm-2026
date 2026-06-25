<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Pegawai';

    protected static ?string $modelLabel = 'Pegawai';

    protected static ?string $pluralModelLabel = 'Pegawai';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user', 'supervisor']);
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['super_admin', 'admin_hr'])) {
            return $query;
        }

        if ($user->hasAnyRole(['manajer', 'supervisor'])) {
            return $query->where('supervisor_user_id', $user->id);
        }

        return $query->where('user_id', $user->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pegawai')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('employee_code')
                            ->label('Kode Pegawai')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('position')
                            ->label('Jabatan')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\DatePicker::make('join_date')
                            ->label('Tanggal Bergabung')
                            ->native(false),
                        Forms\Components\Select::make('supervisor_user_id')
                            ->label('Supervisor')
                            ->options(fn (): array => User::query()
                                ->whereHas('roles', fn (Builder $query) => $query->whereIn('name', ['supervisor', 'manajer', 'admin_hr']))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('join_date')
                    ->label('Bergabung')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('employee_code')
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
