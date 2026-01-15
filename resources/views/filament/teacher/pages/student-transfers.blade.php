<x-filament-panels::page>
    {{-- Solicitudes Pendientes de Aprobación (como sede origen) --}}
    @php
        $pendingApprovals = $this->getPendingApprovals();
    @endphp

    @if($pendingApprovals->count() > 0)
        <x-filament::section 
            icon="heroicon-o-bell-alert"
            icon-color="warning"
        >
            <x-slot name="heading">
                <span class="flex items-center gap-2">
                    Solicitudes Pendientes de Aprobación
                    <x-filament::badge color="warning">{{ $pendingApprovals->count() }}</x-filament::badge>
                </span>
            </x-slot>
            <x-slot name="description">
                Otras sedes solicitan trasladar estudiantes de tu sede
            </x-slot>

            <div class="space-y-4">
                @foreach($pendingApprovals as $transfer)
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-warning-200 dark:border-warning-800 p-4 shadow-sm">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <x-heroicon-o-user-circle class="w-10 h-10 text-gray-400"/>
                                    <div>
                                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                            {{ $transfer->student->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $transfer->student->identification_number ?? 'Sin identificación' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
                                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-2">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Sede Origen</p>
                                        <p class="font-medium text-red-600 dark:text-red-400">{{ $transfer->originSede->name }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Grado Actual</p>
                                        <p class="font-medium">{{ $transfer->originGrade->name }}</p>
                                    </div>
                                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-2">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Sede Destino</p>
                                        <p class="font-medium text-green-600 dark:text-green-400">{{ $transfer->destinationSede->name }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Grado Destino</p>
                                        <p class="font-medium">{{ $transfer->destinationGrade->name }}</p>
                                    </div>
                                </div>

                                @if($transfer->reason)
                                    <div class="mt-3 bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Motivo:</p>
                                        <p class="text-sm">{{ $transfer->reason }}</p>
                                    </div>
                                @endif

                                <p class="text-xs text-gray-400 mt-2">
                                    Solicitado por: {{ $transfer->requester->name }} • {{ $transfer->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <div class="flex flex-col gap-2 lg:w-48">
                                <x-filament::button 
                                    color="success" 
                                    icon="heroicon-o-check"
                                    wire:click="approveRequest({{ $transfer->id }})"
                                    wire:confirm="¿Aprobar el traslado de {{ $transfer->student->name }}? Esta acción es irreversible."
                                >
                                    Aprobar
                                </x-filament::button>

                                <x-filament::button 
                                    color="danger" 
                                    outlined
                                    icon="heroicon-o-x-mark"
                                    wire:click="openRejectModal({{ $transfer->id }})"
                                >
                                    Rechazar
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    {{-- Formulario para Nueva Solicitud --}}
    <x-filament::section>
        <x-slot name="heading">
            <span class="flex items-center justify-between w-full">
                <span class="flex items-center gap-2">
                    <x-heroicon-o-paper-airplane class="w-5 h-5"/>
                    Solicitar Traslado
                </span>
                <x-filament::button 
                    size="sm"
                    color="{{ $showRequestForm ? 'gray' : 'primary' }}"
                    icon="{{ $showRequestForm ? 'heroicon-o-x-mark' : 'heroicon-o-plus' }}"
                    wire:click="toggleRequestForm"
                >
                    {{ $showRequestForm ? 'Cancelar' : 'Nueva Solicitud' }}
                </x-filament::button>
            </span>
        </x-slot>
        <x-slot name="description">
            Solicita el traslado de un estudiante de otra sede hacia tu sede
        </x-slot>

        @if($showRequestForm)
            <div class="space-y-4">
                {{-- Sede de Origen --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sede de Origen <span class="text-danger-500">*</span>
                    </label>
                    <select 
                        wire:model.live="origin_sede_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="">Selecciona una sede...</option>
                        @foreach($this->getAvailableSedes() as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Estudiante --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Estudiante <span class="text-danger-500">*</span>
                    </label>
                    <select 
                        wire:model="student_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        {{ !$origin_sede_id ? 'disabled' : '' }}
                    >
                        <option value="">{{ $origin_sede_id ? 'Selecciona un estudiante...' : 'Primero selecciona una sede' }}</option>
                        @foreach($this->getStudentsForSede() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @if($origin_sede_id && count($this->getStudentsForSede()) === 0)
                        <p class="text-sm text-gray-500 mt-1">No hay estudiantes disponibles en esta sede.</p>
                    @endif
                </div>

                {{-- Grado Destino --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Grado en Tu Sede <span class="text-danger-500">*</span>
                    </label>
                    <select 
                        wire:model="destination_grade_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="">Selecciona un grado...</option>
                        @foreach($this->getAvailableGrades() as $grade)
                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Motivo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Motivo de la Solicitud
                    </label>
                    <textarea 
                        wire:model="reason"
                        rows="3"
                        placeholder="Explica por qué solicitas este traslado..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    ></textarea>
                </div>

                <div class="flex justify-end pt-2">
                    <x-filament::button 
                        wire:click="submitRequest" 
                        icon="heroicon-o-paper-airplane"
                    >
                        Enviar Solicitud
                    </x-filament::button>
                </div>
            </div>
        @else
            <div class="text-center py-6 text-gray-500">
                <x-heroicon-o-arrow-down-tray class="w-12 h-12 mx-auto mb-3 text-gray-300"/>
                <p>Haz clic en "Nueva Solicitud" para solicitar el traslado de un estudiante de otra sede.</p>
            </div>
        @endif
    </x-filament::section>

    {{-- Tabla de Mis Solicitudes --}}
    <x-filament::section>
        <x-slot name="heading">
            <span class="flex items-center gap-2">
                <x-heroicon-o-document-text class="w-5 h-5"/>
                Mis Solicitudes Realizadas
            </span>
        </x-slot>
        <x-slot name="description">
            Historial de solicitudes de traslado que has realizado
        </x-slot>

        {{ $this->table }}
    </x-filament::section>

    {{-- Modal de Rechazo --}}
    <x-filament::modal id="reject-modal" width="md">
        <x-slot name="heading">
            Rechazar Solicitud de Traslado
        </x-slot>

        <x-slot name="description">
            Indica el motivo por el cual rechazas esta solicitud.
        </x-slot>

        <div class="space-y-4">
            <textarea 
                wire:model="rejectionReason" 
                placeholder="Escribe el motivo del rechazo..."
                rows="3"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
            ></textarea>
        </div>

        <x-slot name="footerActions">
            <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'reject-modal' })">
                Cancelar
            </x-filament::button>
            <x-filament::button 
                color="danger" 
                wire:click="confirmReject"
            >
                Confirmar Rechazo
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>
