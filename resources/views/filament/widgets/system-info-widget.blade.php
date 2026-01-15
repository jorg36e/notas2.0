<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-information-circle class="h-5 w-5 text-primary-500" />
                Información del Sistema
            </div>
        </x-slot>
        
        <x-slot name="description">
            Vista general del estado actual del sistema NOTAS 2.0
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Columna 1: Año Escolar y Configuración --}}
            <div class="space-y-4">
                <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-80">Año Escolar Activo</p>
                            <p class="text-2xl font-bold">{{ $this->getSystemInfo()['school_year'] }}</p>
                        </div>
                        <x-heroicon-o-calendar-days class="h-10 w-10 opacity-80" />
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            {{ $this->getSystemInfo()['total_sedes'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Sedes</p>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                            {{ $this->getSystemInfo()['total_grades'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Grados</p>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                            {{ $this->getSystemInfo()['total_subjects'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Materias</p>
                    </div>
                </div>
            </div>
            
            {{-- Columna 2: Usuarios Activos --}}
            <div class="space-y-4">
                <div class="bg-gradient-to-r from-success-500 to-success-600 rounded-xl p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-80">Estudiantes Activos</p>
                            <p class="text-2xl font-bold">{{ $this->getSystemInfo()['total_students'] }}</p>
                        </div>
                        <x-heroicon-o-user-group class="h-10 w-10 opacity-80" />
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-info-500 to-info-600 rounded-xl p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-80">Docentes Activos</p>
                            <p class="text-2xl font-bold">{{ $this->getSystemInfo()['total_teachers'] }}</p>
                        </div>
                        <x-heroicon-o-academic-cap class="h-10 w-10 opacity-80" />
                    </div>
                </div>
            </div>
            
            {{-- Columna 3: Sistema --}}
            <div class="space-y-4">
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <x-heroicon-o-server class="h-4 w-4" />
                        Detalles Técnicos
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">PHP</span>
                            <span class="font-mono bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded text-xs">
                                {{ $this->getSystemInfo()['php_version'] }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Laravel</span>
                            <span class="font-mono bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded text-xs">
                                {{ $this->getSystemInfo()['laravel_version'] }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Base de Datos</span>
                            <span class="font-mono bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded text-xs">
                                {{ $this->getSystemInfo()['database_size'] }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Sesiones Activas</span>
                            <span class="font-mono bg-success-100 dark:bg-success-900 text-success-700 dark:text-success-300 px-2 py-0.5 rounded text-xs">
                                {{ $this->getQuickStats()['active_sessions'] }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
                    <h4 class="font-semibold text-yellow-700 dark:text-yellow-400 mb-2 flex items-center gap-2">
                        <x-heroicon-o-clock class="h-4 w-4" />
                        Actividad de Hoy
                    </h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="text-center">
                            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $this->getQuickStats()['students_today'] }}
                            </p>
                            <p class="text-xs text-yellow-600/70 dark:text-yellow-400/70">Nuevos estudiantes</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $this->getQuickStats()['teachers_today'] }}
                            </p>
                            <p class="text-xs text-yellow-600/70 dark:text-yellow-400/70">Nuevos docentes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
