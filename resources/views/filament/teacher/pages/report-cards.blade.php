<x-filament-panels::page>
    @php $info = $this->getMultigradeDirectorInfo(); @endphp
    
    @if($info)
    <div class="space-y-6">
        {{-- Header con información de la sede --}}
        <div style="background: linear-gradient(to right, #3b82f6, #6366f1, #8b5cf6) !important;" class="rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-6">
                <div style="background-color: rgba(255,255,255,0.2) !important;" class="flex h-16 w-16 items-center justify-center rounded-2xl backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold" style="color: white !important;">
                        Boletines - {{ $info['sede']->name }}
                    </h2>
                    <p class="mt-1" style="color: #e0e7ff !important;">
                        {{ $info['grade']->name }} • Año {{ $info['schoolYear']->name }}
                    </p>
                    <div class="mt-2 flex items-center gap-2">
                        <span style="background-color: rgba(255,255,255,0.2) !important; color: white !important;" class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            {{ $info['studentsCount'] }} estudiantes
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjetas de resumen por grado --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach($info['studentsByGrade'] as $gradeName => $count)
            <div class="p-4 rounded-xl" style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;">
                <div class="flex items-center gap-3">
                    <div style="background-color: #eff6ff !important;" class="flex h-10 w-10 items-center justify-center rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: #6b7280 !important;">{{ $gradeName }}</p>
                        <p class="text-xl font-bold" style="color: #1e40af !important;">{{ $count }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Panel de generación --}}
        <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-2xl shadow-lg overflow-hidden">
            <div style="background-color: #f8fafc !important; border-bottom: 1px solid #e5e7eb !important;" class="px-6 py-4">
                <h3 class="flex items-center gap-2 text-lg font-semibold" style="color: #1e293b !important;">
                    <div style="background-color: #dbeafe !important;" class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                        </svg>
                    </div>
                    Generar Boletines
                </h3>
                <p class="mt-1 text-sm" style="color: #64748b !important;">Selecciona el período y genera los boletines en formato PDF</p>
            </div>
            <div class="p-6">
                {{-- Formulario de selección de período --}}
                <div class="mb-6 max-w-md">
                    <x-filament-panels::form>
                        {{ $this->form }}
                    </x-filament-panels::form>
                </div>

                {{-- Botones de acción --}}
                <div class="flex items-center gap-4">
                    <button type="button" 
                            wire:click="generateReportCards"
                            wire:loading.attr="disabled"
                            wire:target="generateReportCards"
                            @if($isGenerating) disabled @endif
                            style="background: linear-gradient(to right, #3b82f6, #6366f1) !important; color: white !important;"
                            class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50">
                        @if($isGenerating)
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generando boletines...
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                            </svg>
                            Generar Boletines
                        @endif
                    </button>

                    @if($downloadUrl)
                        <a href="{{ $downloadUrl }}" 
                           download
                           style="background-color: #059669 !important; color: white !important;"
                           class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-lg transition hover:opacity-90">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Descargar ZIP
                        </a>
                    @endif
                </div>

                {{-- Estadísticas de generación --}}
                @if(!empty($generationStats))
                    <div class="mt-6 p-4 rounded-xl" style="background-color: #ecfdf5 !important; border: 1px solid #a7f3d0 !important;">
                        <div class="flex items-center gap-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span class="font-semibold" style="color: #065f46 !important;">Generación completada</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-3 rounded-lg" style="background-color: #d1fae5 !important;">
                                <p class="text-xs font-medium" style="color: #047857 !important;">Total</p>
                                <p class="text-2xl font-bold" style="color: #065f46 !important;">{{ $generationStats['total'] ?? 0 }}</p>
                            </div>
                            <div class="p-3 rounded-lg" style="background-color: #d1fae5 !important;">
                                <p class="text-xs font-medium" style="color: #047857 !important;">Exitosos</p>
                                <p class="text-2xl font-bold" style="color: #059669 !important;">{{ $generationStats['success'] ?? 0 }}</p>
                            </div>
                            <div class="p-3 rounded-lg" style="background-color: {{ ($generationStats['errors'] ?? 0) > 0 ? '#fee2e2' : '#d1fae5' }} !important;">
                                <p class="text-xs font-medium" style="color: {{ ($generationStats['errors'] ?? 0) > 0 ? '#dc2626' : '#047857' }} !important;">Errores</p>
                                <p class="text-2xl font-bold" style="color: {{ ($generationStats['errors'] ?? 0) > 0 ? '#dc2626' : '#059669' }} !important;">{{ $generationStats['errors'] ?? 0 }}</p>
                            </div>
                            <div class="p-3 rounded-lg" style="background-color: #d1fae5 !important;">
                                <p class="text-xs font-medium" style="color: #047857 !important;">Tiempo</p>
                                <p class="text-2xl font-bold" style="color: #065f46 !important;">{{ $generationStats['time'] ?? 0 }}s</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Información adicional --}}
        <div style="background-color: #eff6ff !important; border: 1px solid #bfdbfe !important;" class="rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div style="background-color: #3b82f6 !important;" class="flex h-10 w-10 items-center justify-center rounded-xl flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold" style="color: #1e40af !important;">Información sobre los boletines</h4>
                    <ul class="mt-2 text-sm space-y-1" style="color: #1d4ed8 !important;">
                        <li>• Los boletines se generan en formato PDF dentro de un archivo ZIP</li>
                        <li>• Cada boletín incluye las calificaciones del período seleccionado</li>
                        <li>• Los archivos están organizados por sede y grado</li>
                        <li>• Asegúrate de que todas las calificaciones estén registradas antes de generar</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-12">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9ca3af" class="h-16 w-16 mx-auto mb-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
        </svg>
        <h3 class="text-lg font-semibold" style="color: #6b7280 !important;">Sin acceso</h3>
        <p class="mt-2 text-sm" style="color: #9ca3af !important;">No tienes asignaciones de grados multigrado</p>
    </div>
    @endif
</x-filament-panels::page>
