<x-filament-panels::page>
    @php
        $activeSuratTugas = $this->getActiveSuratTugas();
        $recentAttendanceLogs = $this->getRecentAttendanceLogs();
        $todayStats = $this->getTodayStats();
    @endphp

    <div
        x-data="attendanceCapture()"
        class="space-y-6"
    >
        <div
            x-show="message"
            x-cloak
            class="rounded-lg border border-danger-200 bg-danger-50 px-4 py-3 text-sm font-medium text-danger-700 dark:border-danger-500/40 dark:bg-danger-500/10 dark:text-danger-300"
            x-text="message"
        ></div>

        <div
            x-show="statusMessage"
            x-cloak
            class="rounded-lg border border-primary-200 bg-primary-50 px-4 py-3 text-sm font-medium text-primary-700 dark:border-primary-500/40 dark:bg-primary-500/10 dark:text-primary-300"
            x-text="statusMessage"
        ></div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dinas aktif</p>
                <p class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ $todayStats['active_assignments'] }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sudah check-in</p>
                <p class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ $todayStats['checked_in'] }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Selesai</p>
                <p class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ $todayStats['completed'] }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu approval</p>
                <p class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ $todayStats['pending_approval'] }}</p>
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Surat Tugas Hari Ini</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
                </div>
            </div>

            @forelse ($activeSuratTugas as $suratTugas)
                @php
                    $todayLog = $suratTugas->attendanceLogs->first();
                    $locationStatusLabel = match ($todayLog?->location_status) {
                        'VALID' => 'Dalam radius',
                        'OUT_OF_RANGE' => 'Di luar radius',
                        default => 'Belum absen',
                    };
                    $approvalStatusLabel = match ($todayLog?->approval_status) {
                        'APPROVED' => 'Disetujui',
                        'REJECTED' => 'Ditolak',
                        'PENDING' => 'Menunggu',
                        default => 'Belum dikirim',
                    };
                @endphp

                <div
                    class="rounded-lg border border-gray-200 bg-white shadow-sm transition dark:border-gray-700 dark:bg-gray-900"
                    x-bind:class="{ 'opacity-70': busy }"
                >
                    <div class="grid gap-4 p-4 lg:grid-cols-[1fr_auto] lg:items-start">
                        <div class="space-y-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                                        {{ $suratTugas->location_name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $suratTugas->start_date->format('d M Y') }} - {{ $suratTugas->end_date->format('d M Y') }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <span @class([
                                        'rounded-md px-2 py-1 text-xs font-medium',
                                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => ! $todayLog,
                                        'bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-300' => $todayLog?->location_status === 'VALID',
                                        'bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-300' => $todayLog?->location_status === 'OUT_OF_RANGE',
                                    ])>
                                        {{ $locationStatusLabel }}
                                    </span>

                                    <span @class([
                                        'rounded-md px-2 py-1 text-xs font-medium',
                                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => ! $todayLog,
                                        'bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-300' => $todayLog?->approval_status === 'APPROVED',
                                        'bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-300' => $todayLog?->approval_status === 'REJECTED',
                                        'bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-300' => $todayLog?->approval_status === 'PENDING',
                                    ])>
                                        {{ $approvalStatusLabel }}
                                    </span>
                                </div>
                            </div>

                            <dl class="grid gap-3 text-sm sm:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-md bg-gray-50 px-3 py-2 dark:bg-gray-800/70">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Radius</dt>
                                    <dd class="mt-1 text-gray-950 dark:text-white">{{ number_format($suratTugas->radius_meters) }} m</dd>
                                </div>
                                <div class="rounded-md bg-gray-50 px-3 py-2 dark:bg-gray-800/70">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Koordinat</dt>
                                    <dd class="mt-1 text-gray-950 dark:text-white">
                                        {{ number_format((float) $suratTugas->target_lat, 5) }},
                                        {{ number_format((float) $suratTugas->target_lng, 5) }}
                                    </dd>
                                </div>
                                <div class="rounded-md bg-gray-50 px-3 py-2 dark:bg-gray-800/70">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Check-in</dt>
                                    <dd class="mt-1 text-gray-950 dark:text-white">{{ $todayLog?->check_in_at?->format('H:i') ?? '-' }}</dd>
                                </div>
                                <div class="rounded-md bg-gray-50 px-3 py-2 dark:bg-gray-800/70">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Check-out</dt>
                                    <dd class="mt-1 text-gray-950 dark:text-white">{{ $todayLog?->check_out_at?->format('H:i') ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="flex min-w-40 flex-col gap-2 sm:flex-row lg:flex-col">
                            @if (! $todayLog)
                                <x-filament::button
                                    icon="heroicon-m-camera"
                                    size="lg"
                                    x-bind:disabled="busy"
                                    x-on:click="submit('check-in', '{{ $suratTugas->id }}')"
                                >
                                    Check-in
                                </x-filament::button>
                            @elseif (! $todayLog->check_out_at)
                                <x-filament::button
                                    icon="heroicon-m-arrow-right-on-rectangle"
                                    color="warning"
                                    size="lg"
                                    x-bind:disabled="busy"
                                    x-on:click="submit('check-out', '{{ $todayLog->id }}')"
                                >
                                    Check-out
                                </x-filament::button>
                            @else
                                <x-filament::button
                                    icon="heroicon-m-check-circle"
                                    color="gray"
                                    size="lg"
                                    disabled
                                >
                                    Selesai
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-sm font-medium text-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    Tidak ada surat tugas aktif hari ini.
                </div>
            @endforelse
        </section>

        <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-col gap-1 border-b border-gray-200 px-4 py-3 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Riwayat Absensi</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">10 aktivitas terbaru</p>
                </div>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($recentAttendanceLogs as $log)
                    @php
                        $historyLocationStatusLabel = match ($log->location_status) {
                            'VALID' => 'Dalam radius',
                            'OUT_OF_RANGE' => 'Di luar radius',
                            default => $log->location_status,
                        };
                        $historyApprovalStatusLabel = match ($log->approval_status) {
                            'APPROVED' => 'Disetujui',
                            'REJECTED' => 'Ditolak',
                            'PENDING' => 'Menunggu',
                            default => $log->approval_status,
                        };
                    @endphp

                    <div class="grid gap-3 px-4 py-4 text-sm text-gray-600 dark:text-gray-300 lg:grid-cols-[1.4fr_1fr_1fr_1fr] lg:items-center">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-950 dark:text-white">{{ $log->attendance_date->format('d M Y') }}</p>
                            <p class="truncate text-gray-500 dark:text-gray-400">{{ $log->suratTugas?->location_name }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 sm:flex sm:gap-4 lg:grid-cols-none">
                            <span>Masuk {{ $log->check_in_at?->format('H:i') ?? '-' }}</span>
                            <span>Keluar {{ $log->check_out_at?->format('H:i') ?? '-' }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span @class([
                                'rounded-md px-2 py-1 text-xs font-medium',
                                'bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-300' => $log->location_status === 'VALID',
                                'bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-300' => $log->location_status === 'OUT_OF_RANGE',
                            ])>
                                {{ $historyLocationStatusLabel }}
                            </span>
                            <span @class([
                                'rounded-md px-2 py-1 text-xs font-medium',
                                'bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-300' => $log->approval_status === 'APPROVED',
                                'bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-300' => $log->approval_status === 'REJECTED',
                                'bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-300' => $log->approval_status === 'PENDING',
                            ])>
                                {{ $historyApprovalStatusLabel }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-3 lg:justify-end">
                            @if ($log->check_in_photo_url)
                                <a
                                    href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($log->check_in_photo_url) }}"
                                    target="_blank"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400"
                                >
                                    Foto masuk
                                </a>
                            @endif

                            @if ($log->check_out_photo_url)
                                <a
                                    href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($log->check_out_photo_url) }}"
                                    target="_blank"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400"
                                >
                                    Foto keluar
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-6 text-sm text-gray-600 dark:text-gray-300">
                        Belum ada riwayat absensi.
                    </div>
                @endforelse
            </div>
        </section>

        <video x-ref="video" class="hidden" playsinline muted></video>
        <canvas x-ref="canvas" class="hidden"></canvas>
    </div>

    <script>
        function attendanceCapture() {
            return {
                busy: false,
                message: '',
                statusMessage: '',

                async submit(type, id) {
                    this.busy = true
                    this.message = ''
                    this.statusMessage = 'Mengambil lokasi GPS...'

                    try {
                        const position = await this.getPosition()
                        this.statusMessage = 'Mengambil foto...'
                        const photo = await this.capturePhoto()

                        this.statusMessage = 'Menyimpan absensi...'

                        if (type === 'check-in') {
                            await this.$wire.performCheckIn(
                                id,
                                position.coords.latitude,
                                position.coords.longitude,
                                photo,
                            )
                        } else {
                            await this.$wire.performCheckOut(
                                id,
                                position.coords.latitude,
                                position.coords.longitude,
                                photo,
                            )
                        }
                    } catch (error) {
                        this.message = error?.message || 'GPS atau kamera tidak dapat diakses.'
                    } finally {
                        this.busy = false
                        this.statusMessage = ''
                    }
                },

                getPosition() {
                    if (! navigator.geolocation) {
                        return Promise.reject(new Error('Browser tidak mendukung GPS.'))
                    }

                    return new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, () => {
                            reject(new Error('Akses GPS ditolak.'))
                        }, {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0,
                        })
                    })
                },

                async capturePhoto() {
                    if (! navigator.mediaDevices?.getUserMedia) {
                        throw new Error('Browser tidak mendukung kamera.')
                    }

                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: {
                                ideal: 'environment',
                            },
                        },
                        audio: false,
                    }).catch(() => {
                        throw new Error('Akses kamera ditolak.')
                    })

                    try {
                        const video = this.$refs.video
                        video.srcObject = stream
                        await new Promise((resolve) => {
                            if (video.readyState >= 2) {
                                resolve()
                                return
                            }

                            video.onloadedmetadata = resolve
                        })
                        await video.play()

                        const canvas = this.$refs.canvas
                        canvas.width = video.videoWidth || 640
                        canvas.height = video.videoHeight || 480
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height)

                        return canvas.toDataURL('image/jpeg', 0.82)
                    } finally {
                        stream.getTracks().forEach((track) => track.stop())
                    }
                },
            }
        }
    </script>
</x-filament-panels::page>
