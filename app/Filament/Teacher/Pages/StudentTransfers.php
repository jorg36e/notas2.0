<?php

namespace App\Filament\Teacher\Pages;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\TeachingAssignment;
use App\Models\TransferRequest;
use App\Services\TransferService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class StudentTransfers extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationLabel = 'Traslados';
    protected static ?string $title = 'Traslados de Estudiantes';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.teacher.pages.student-transfers';

    public ?array $requestFormData = [];
    public bool $showRequestForm = false;

    /**
     * Solo mostrar si el profesor tiene asignaciones multigrado
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $activeYear = SchoolYear::where('is_active', true)->first();
        if (!$activeYear) return false;

        return TeachingAssignment::where('teacher_id', $user->id)
            ->where('school_year_id', $activeYear->id)
            ->where('is_active', true)
            ->whereHas('grade', function($query) {
                $query->where('type', 'multi');
            })
            ->exists();
    }

    /**
     * Badge para mostrar solicitudes pendientes de aprobación
     */
    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (!$user) return null;

        $activeYear = SchoolYear::where('is_active', true)->first();
        if (!$activeYear) return null;

        // Obtener la sede del profesor
        $assignment = TeachingAssignment::where('teacher_id', $user->id)
            ->where('school_year_id', $activeYear->id)
            ->where('is_active', true)
            ->first();

        if (!$assignment) return null;

        // Contar solicitudes pendientes donde su sede es origen
        $count = TransferRequest::where('origin_sede_id', $assignment->sede_id)
            ->where('school_year_id', $activeYear->id)
            ->where('status', TransferRequest::STATUS_PENDING)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    // Propiedades para el formulario
    public ?int $origin_sede_id = null;
    public ?int $student_id = null;
    public ?int $destination_grade_id = null;
    public ?string $reason = null;

    public function mount(): void
    {
        $this->requestFormData = [];
    }

    /**
     * Obtener estudiantes de la sede origen seleccionada
     */
    public function getStudentsForSede(): array
    {
        if (!$this->origin_sede_id) return [];

        $activeYear = SchoolYear::where('is_active', true)->first();
        if (!$activeYear) return [];

        return Enrollment::where('school_year_id', $activeYear->id)
            ->where('sede_id', $this->origin_sede_id)
            ->where('status', 'active')
            ->whereDoesntHave('transferRequests', function ($q) {
                $q->where('status', TransferRequest::STATUS_PENDING);
            })
            ->with(['student', 'grade'])
            ->get()
            ->mapWithKeys(function ($enrollment) {
                return [
                    $enrollment->student_id => "{$enrollment->student->name} ({$enrollment->grade->name})"
                ];
            })
            ->toArray();
    }

    /**
     * Cuando cambia la sede de origen, resetear estudiante
     */
    public function updatedOriginSedeId(): void
    {
        $this->student_id = null;
    }

    /**
     * Obtener la sede del profesor
     */
    public function getTeacherSede(): ?Sede
    {
        $user = auth()->user();
        $activeYear = SchoolYear::where('is_active', true)->first();
        
        if (!$activeYear) return null;

        $assignment = TeachingAssignment::where('teacher_id', $user->id)
            ->where('school_year_id', $activeYear->id)
            ->where('is_active', true)
            ->whereHas('grade', function($query) {
                $query->where('type', 'multi');
            })
            ->with('sede')
            ->first();

        return $assignment?->sede;
    }

    /**
     * Obtener sedes disponibles (excepto la del profesor)
     */
    public function getAvailableSedes(): \Illuminate\Support\Collection
    {
        $teacherSede = $this->getTeacherSede();
        return Sede::where('is_active', true)
            ->when($teacherSede, fn($q) => $q->where('id', '!=', $teacherSede->id))
            ->orderBy('name')
            ->get();
    }

    /**
     * Obtener grados disponibles
     */
    public function getAvailableGrades(): \Illuminate\Support\Collection
    {
        return Grade::where('is_active', true)->orderBy('level')->get();
    }

    /**
     * Formulario para solicitar traslado - YA NO SE USA, se maneja con propiedades directas
     */
    public function requestForm(Form $form): Form
    {
        return $form
            ->schema([])
            ->statePath('requestFormData');
    }

    protected function getForms(): array
    {
        return ['requestForm'];
    }

    /**
     * Tabla de solicitudes realizadas (como sede destino)
     */
    public function table(Table $table): Table
    {
        $teacherSede = $this->getTeacherSede();
        $activeYear = SchoolYear::where('is_active', true)->first();

        return $table
            ->query(
                TransferRequest::query()
                    ->where('destination_sede_id', $teacherSede?->id)
                    ->where('school_year_id', $activeYear?->id)
                    ->with(['student', 'originSede', 'destinationSede', 'originGrade', 'destinationGrade', 'requester', 'approver'])
            )
            ->columns([
                TextColumn::make('student.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('originSede.name')
                    ->label('Sede Origen')
                    ->badge()
                    ->color('danger'),

                TextColumn::make('originGrade.name')
                    ->label('Grado Origen'),

                TextColumn::make('destinationGrade.name')
                    ->label('Grado Destino'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (TransferRequest $record) => $record->isPending() && $record->requested_by === auth()->id())
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Solicitud')
                    ->action(function (TransferRequest $record) {
                        $service = new TransferService();
                        $service->cancelTransfer($record, auth()->id());
                        
                        Notification::make()
                            ->title('Solicitud cancelada')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Sin solicitudes')
            ->emptyStateDescription('No has realizado solicitudes de traslado.');
    }

    /**
     * Toggle mostrar formulario
     */
    public function toggleRequestForm(): void
    {
        $this->showRequestForm = !$this->showRequestForm;
        if (!$this->showRequestForm) {
            $this->resetRequestForm();
        }
    }

    /**
     * Resetear formulario
     */
    public function resetRequestForm(): void
    {
        $this->origin_sede_id = null;
        $this->student_id = null;
        $this->destination_grade_id = null;
        $this->reason = null;
    }

    /**
     * Enviar solicitud de traslado
     */
    public function submitRequest(): void
    {
        // Validar campos requeridos
        if (!$this->origin_sede_id || !$this->student_id || !$this->destination_grade_id) {
            Notification::make()
                ->title('Error')
                ->body('Por favor completa todos los campos requeridos.')
                ->danger()
                ->send();
            return;
        }

        $teacherSede = $this->getTeacherSede();

        if (!$teacherSede) {
            Notification::make()
                ->title('Error')
                ->body('No tienes una sede asignada.')
                ->danger()
                ->send();
            return;
        }

        try {
            $service = new TransferService();
            $service->createTransferRequest(
                $this->student_id,
                $teacherSede->id,
                $this->destination_grade_id,
                auth()->id(),
                $this->reason
            );

            Notification::make()
                ->title('Solicitud enviada')
                ->body('La solicitud de traslado ha sido enviada a la sede de origen para su aprobación.')
                ->success()
                ->send();

            $this->resetRequestForm();
            $this->showRequestForm = false;

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Obtener solicitudes pendientes que necesitan aprobación
     */
    public function getPendingApprovals(): \Illuminate\Database\Eloquent\Collection
    {
        $teacherSede = $this->getTeacherSede();
        $activeYear = SchoolYear::where('is_active', true)->first();

        if (!$teacherSede || !$activeYear) {
            return collect();
        }

        return TransferRequest::where('origin_sede_id', $teacherSede->id)
            ->where('school_year_id', $activeYear->id)
            ->where('status', TransferRequest::STATUS_PENDING)
            ->with(['student', 'originSede', 'destinationSede', 'originGrade', 'destinationGrade', 'requester'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Aprobar una solicitud
     */
    public function approveRequest(int $transferId): void
    {
        $transfer = TransferRequest::findOrFail($transferId);
        $teacherSede = $this->getTeacherSede();

        if ($transfer->origin_sede_id !== $teacherSede?->id) {
            Notification::make()
                ->title('Error')
                ->body('No tienes permiso para aprobar esta solicitud.')
                ->danger()
                ->send();
            return;
        }

        try {
            $service = new TransferService();
            $service->approveTransfer($transfer, auth()->id());

            Notification::make()
                ->title('Traslado aprobado')
                ->body("El estudiante {$transfer->student->name} ha sido trasladado exitosamente.")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Rechazar una solicitud
     */
    public function rejectRequest(int $transferId, string $reason): void
    {
        $transfer = TransferRequest::findOrFail($transferId);
        $teacherSede = $this->getTeacherSede();

        if ($transfer->origin_sede_id !== $teacherSede?->id) {
            Notification::make()
                ->title('Error')
                ->body('No tienes permiso para rechazar esta solicitud.')
                ->danger()
                ->send();
            return;
        }

        try {
            $service = new TransferService();
            $service->rejectTransfer($transfer, auth()->id(), $reason);

            Notification::make()
                ->title('Solicitud rechazada')
                ->warning()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public ?string $rejectionReason = '';
    public ?int $rejectingTransferId = null;

    public function openRejectModal(int $transferId): void
    {
        $this->rejectingTransferId = $transferId;
        $this->rejectionReason = '';
        $this->dispatch('open-modal', id: 'reject-modal');
    }

    public function confirmReject(): void
    {
        if ($this->rejectingTransferId && $this->rejectionReason) {
            $this->rejectRequest($this->rejectingTransferId, $this->rejectionReason);
            $this->dispatch('close-modal', id: 'reject-modal');
            $this->rejectingTransferId = null;
            $this->rejectionReason = '';
        }
    }
}
