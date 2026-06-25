<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Services\MeritService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopEmployeesByPointsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'Top 5 Karyawan Berdasarkan Point';
    }

    public function table(Table $table): Table
    {
        $meritService = app(MeritService::class);

        $employees = User::whereHas('roles', fn (Builder $q) => $q->where('name', 'karyawan'))
            ->with('roles')
            ->get()
            ->map(function ($user) use ($meritService) {
                $user->current_balance = $meritService->getBalance($user);

                return $user;
            })
            ->sortByDesc('current_balance')
            ->take(5);

        $orderedIds = $employees->pluck('id')->toArray();

        return $table
            ->query(
                User::query()
                    ->whereIn('id', $orderedIds)
                    ->whereHas('roles', fn (Builder $q) => $q->where('name', 'karyawan'))
                    ->when(! empty($orderedIds), function ($query) use ($orderedIds) {
                        $idsString = implode(',', array_map(fn ($id) => "'{$id}'", $orderedIds));
                        $query->orderByRaw("FIELD(id, {$idsString})");
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('current_balance')
                    ->label('Saldo Point')
                    ->state(function (User $record) use ($meritService): int {
                        return $meritService->getBalance($record);
                    })
                    ->numeric()
                    ->suffix(' pt')
                    ->color('success')
                    ->alignEnd(),
            ])
            ->paginated(false);
    }

    public static function canView(): bool
    {
        return false;
    }
}
