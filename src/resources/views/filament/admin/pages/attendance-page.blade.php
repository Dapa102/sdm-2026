<x-filament-panels::page>
    <div
        x-data="attendanceCapture()"
        class="space-y-6"
    >
        <div
            x-show="message"
            x-cloak
            class="rounded-lg border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-700 dark:border-danger-500/40 dark:bg-danger-500/10 dark:text-danger-300"
            x-text="message"
        ></div>

        <section class="space-y-4">
            @forelse ($this->getActiveSuratTugas() as $suratTugas)
                @php
                    $todayLog = $suratTugas->attendanceLogs->first();
                @endphp

                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base font-semibold text-gray-950 dark:text-white">
                                    {{ $suratTugas->location_name }}
                                </h2>

                                @if ($todayLog?->location_status === 'OUT_OF_RANGE')
                                    <span class="rounded-md bg-warning-100 px-2 py-1 text-xs font-medium text-warning-700 dark:bg-warning-500/10 dark:text-warning-300">
                                        Di luar radius
                                    </span>
                                @elseif ($todayLog)
                                    <span class="rounded-md bg-success-100 px-2 py-1 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-300">
                                        Dalam radius
                                    </span>
                                @endif
                            </div>

                            <dl class="grid gap-2 text-sm text-gray-600 dark:text-gray-300 sm:grid-cols-2">
                                <div>
                                    <dt class="font-medium text-gray-950 dark:text-white">Periode</dt>
                                    <dd>{{ $suratTugas->start_date->format('d M Y') }} - {{ $suratTugas->end_date->format('d M Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-950 dark:text-white">Radius</dt>
                                    <dd>{{ number_format($suratTugas->radius_meters) }} m</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-950 dark:text-white">Check-in</dt>
                                    <dd>{{ $todayLog?->check_in_at?->format('H:i') ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-950 dark:text-white">Check-out</dt>
                                    <dd>{{ $todayLog?->check_out_at?->format('H:i') ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row md:flex-col">
                            @if (! $todayLog)
                                <x-filament::button
                                    icon="heroicon-m-camera"
                                    x-bind:disabled="busy"
                                    x-on:click="submit('check-in', '{{ $suratTugas->id }}')"
                                >
                                    Check-in
                                </x-filament::button>
                            @elseif (! $todayLog->check_out_at)
                                <x-filament::button
                                    icon="heroicon-m-arrow-right-on-rectangle"
                                    color="warning"
                                    x-bind:disabled="busy"
                                    x-on:click="submit('check-out', '{{ $todayLog->id }}')"
                                >
                                    Check-out
                                </x-filament::button>
                            @else
                                <x-filament::button
                                    icon="heroicon-m-check-circle"
                                    color="gray"
                                    disabled
                                >
                                    Selesai
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white p-6 text-sm text-gray-600 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    Tidak ada surat tugas aktif untuk hari ini.
                </div>
            @endforelse
        </section>

        <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-950 dark:text-white">Riwayat Absensi</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($this->getRecentAttendanceLogs() as $log)
                    <div class="grid gap-2 px-4 py-3 text-sm text-gray-600 dark:text-gray-300 md:grid-cols-4">
                        <div>
                            <span class="font-medium text-gray-950 dark:text-white">{{ $log->attendance_date->format('d M Y') }}</span>
                            <div>{{ $log->suratTugas?->location_name }}</div>
                        </div>
                        <div>Masuk: {{ $log->check_in_at?->format('H:i') ?? '-' }}</div>
                        <div>Keluar: {{ $log->check_out_at?->format('H:i') ?? '-' }}</div>
                        <div>
                            <span @class([
                                'rounded-md px-2 py-1 text-xs font-medium',
                                'bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-300' => $log->approval_status === 'APPROVED',
                                'bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-300' => $log->approval_status === 'REJECTED',
                                'bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-300' => $log->approval_status === 'PENDING',
                            ])>
                                {{ $log->approval_status }}
                            </span>
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

                async submit(type, id) {
                    this.busy = true
                    this.message = ''

                    try {
                        const position = await this.getPosition()
                        const photo = await this.capturePhoto()

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
                            facingMode: 'user',
                        },
                        audio: false,
                    }).catch(() => {
                        throw new Error('Akses kamera ditolak.')
                    })

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

                    stream.getTracks().forEach((track) => track.stop())

                    return canvas.toDataURL('image/jpeg', 0.82)
                },
            }
        }
    </script>
</x-filament-panels::page>
