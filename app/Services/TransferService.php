<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\StudentGrade;
use App\Models\TeachingAssignment;
use App\Models\TransferRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{
    /**
     * Crear solicitud de traslado (para profesores)
     */
    public function createTransferRequest(
        int $studentId,
        int $destinationSedeId,
        int $destinationGradeId,
        int $requestedById,
        ?string $reason = null
    ): TransferRequest {
        $activeYear = SchoolYear::where('is_active', true)->firstOrFail();
        
        // Obtener matrícula actual del estudiante
        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('school_year_id', $activeYear->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Verificar que no exista una solicitud pendiente para este estudiante
        $existingRequest = TransferRequest::where('student_id', $studentId)
            ->where('school_year_id', $activeYear->id)
            ->where('status', TransferRequest::STATUS_PENDING)
            ->exists();

        if ($existingRequest) {
            throw new \Exception('Ya existe una solicitud de traslado pendiente para este estudiante.');
        }

        // Verificar que la sede destino sea diferente a la origen
        if ($enrollment->sede_id === $destinationSedeId) {
            throw new \Exception('La sede de destino debe ser diferente a la sede actual.');
        }

        return TransferRequest::create([
            'student_id' => $studentId,
            'enrollment_id' => $enrollment->id,
            'origin_sede_id' => $enrollment->sede_id,
            'destination_sede_id' => $destinationSedeId,
            'origin_grade_id' => $enrollment->grade_id,
            'destination_grade_id' => $destinationGradeId,
            'school_year_id' => $activeYear->id,
            'requested_by' => $requestedById,
            'status' => TransferRequest::STATUS_PENDING,
            'type' => TransferRequest::TYPE_REQUEST,
            'reason' => $reason,
        ]);
    }

    /**
     * Ejecutar traslado directo (para administrador)
     */
    public function executeDirectTransfer(
        int $studentId,
        int $destinationSedeId,
        int $destinationGradeId,
        int $executedById,
        ?string $reason = null,
        ?string $notes = null
    ): TransferRequest {
        $activeYear = SchoolYear::where('is_active', true)->firstOrFail();
        
        // Obtener matrícula actual
        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('school_year_id', $activeYear->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Verificar sede diferente
        if ($enrollment->sede_id === $destinationSedeId) {
            throw new \Exception('La sede de destino debe ser diferente a la sede actual.');
        }

        return DB::transaction(function () use (
            $studentId, $enrollment, $destinationSedeId, $destinationGradeId,
            $executedById, $activeYear, $reason, $notes
        ) {
            // Crear registro de traslado
            $transfer = TransferRequest::create([
                'student_id' => $studentId,
                'enrollment_id' => $enrollment->id,
                'origin_sede_id' => $enrollment->sede_id,
                'destination_sede_id' => $destinationSedeId,
                'origin_grade_id' => $enrollment->grade_id,
                'destination_grade_id' => $destinationGradeId,
                'school_year_id' => $activeYear->id,
                'requested_by' => $executedById,
                'approved_by' => $executedById,
                'status' => TransferRequest::STATUS_APPROVED,
                'type' => TransferRequest::TYPE_DIRECT,
                'reason' => $reason,
                'notes' => $notes,
                'processed_at' => now(),
            ]);

            // Ejecutar el traslado
            $this->processTransfer($transfer);

            return $transfer;
        });
    }

    /**
     * Aprobar solicitud de traslado
     */
    public function approveTransfer(TransferRequest $transfer, int $approvedById, ?string $notes = null): void
    {
        if (!$transfer->isPending()) {
            throw new \Exception('Esta solicitud ya fue procesada.');
        }

        DB::transaction(function () use ($transfer, $approvedById, $notes) {
            $transfer->update([
                'status' => TransferRequest::STATUS_APPROVED,
                'approved_by' => $approvedById,
                'notes' => $notes,
                'processed_at' => now(),
            ]);

            $this->processTransfer($transfer);
        });
    }

    /**
     * Rechazar solicitud de traslado
     */
    public function rejectTransfer(TransferRequest $transfer, int $rejectedById, string $rejectionReason): void
    {
        if (!$transfer->isPending()) {
            throw new \Exception('Esta solicitud ya fue procesada.');
        }

        $transfer->update([
            'status' => TransferRequest::STATUS_REJECTED,
            'approved_by' => $rejectedById,
            'rejection_reason' => $rejectionReason,
            'processed_at' => now(),
        ]);
    }

    /**
     * Cancelar solicitud de traslado (solo el solicitante)
     */
    public function cancelTransfer(TransferRequest $transfer, int $cancelledById): void
    {
        if (!$transfer->isPending()) {
            throw new \Exception('Esta solicitud ya fue procesada.');
        }

        if ($transfer->requested_by !== $cancelledById) {
            throw new \Exception('Solo el solicitante puede cancelar la solicitud.');
        }

        $transfer->update([
            'status' => TransferRequest::STATUS_CANCELLED,
            'processed_at' => now(),
        ]);
    }

    /**
     * Procesar el traslado (actualizar matrícula y reasignar notas)
     */
    private function processTransfer(TransferRequest $transfer): void
    {
        $enrollment = $transfer->enrollment;
        $activeYear = $transfer->schoolYear;

        // Actualizar la matrícula con la nueva sede y grado
        $enrollment->update([
            'sede_id' => $transfer->destination_sede_id,
            'grade_id' => $transfer->destination_grade_id,
        ]);

        // Trasladar las notas del estudiante a la nueva sede
        $this->transferStudentGrades(
            $transfer->student_id,
            $activeYear->id,
            $transfer->origin_sede_id,
            $transfer->destination_sede_id,
            $transfer->origin_grade_id,
            $transfer->destination_grade_id
        );

        Log::info('Traslado procesado', [
            'transfer_id' => $transfer->id,
            'student_id' => $transfer->student_id,
            'from_sede' => $transfer->origin_sede_id,
            'to_sede' => $transfer->destination_sede_id,
            'from_grade' => $transfer->origin_grade_id,
            'to_grade' => $transfer->destination_grade_id,
        ]);
    }

    /**
     * Trasladar las notas del estudiante a la nueva sede
     * Busca el teaching_assignment equivalente en la sede destino para cada materia
     */
    private function transferStudentGrades(
        int $studentId,
        int $schoolYearId,
        int $originSedeId,
        int $destinationSedeId,
        int $originGradeId,
        int $destinationGradeId
    ): void {
        // Obtener todas las notas del estudiante en la sede origen
        $studentGrades = StudentGrade::whereHas('teachingAssignment', function ($q) use ($schoolYearId, $originSedeId) {
            $q->where('school_year_id', $schoolYearId)
              ->where('sede_id', $originSedeId);
        })
        ->where('student_id', $studentId)
        ->with('teachingAssignment')
        ->get();

        Log::info('Trasladando notas', [
            'student_id' => $studentId,
            'grades_count' => $studentGrades->count(),
        ]);

        foreach ($studentGrades as $grade) {
            $oldAssignment = $grade->teachingAssignment;
            
            // Buscar el teaching_assignment equivalente en la sede destino
            // (misma materia, mismo año escolar, sede destino)
            $newAssignment = TeachingAssignment::where('school_year_id', $schoolYearId)
                ->where('sede_id', $destinationSedeId)
                ->where('subject_id', $oldAssignment->subject_id)
                ->where('is_active', true)
                ->first();

            if ($newAssignment) {
                // Verificar si ya existe una nota para este estudiante en el nuevo assignment
                $existingGrade = StudentGrade::where('teaching_assignment_id', $newAssignment->id)
                    ->where('student_id', $studentId)
                    ->where('period_id', $grade->period_id)
                    ->first();

                if (!$existingGrade) {
                    // Actualizar la nota para que apunte al nuevo teaching_assignment
                    $grade->update([
                        'teaching_assignment_id' => $newAssignment->id,
                    ]);

                    Log::info('Nota trasladada', [
                        'student_grade_id' => $grade->id,
                        'subject' => $oldAssignment->subject_id,
                        'from_assignment' => $oldAssignment->id,
                        'to_assignment' => $newAssignment->id,
                    ]);
                } else {
                    Log::warning('Ya existe nota en destino, no se traslada', [
                        'student_grade_id' => $grade->id,
                        'existing_grade_id' => $existingGrade->id,
                    ]);
                }
            } else {
                Log::warning('No se encontró teaching_assignment equivalente en destino', [
                    'student_id' => $studentId,
                    'subject_id' => $oldAssignment->subject_id,
                    'destination_sede_id' => $destinationSedeId,
                ]);
            }
        }
    }

    /**
     * Obtener estudiantes disponibles para traslado en una sede
     */
    public function getStudentsForTransfer(int $sedeId, ?int $gradeId = null): \Illuminate\Database\Eloquent\Collection
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            return collect();
        }

        $query = Enrollment::where('school_year_id', $activeYear->id)
            ->where('sede_id', $sedeId)
            ->where('status', 'active')
            ->with(['student', 'grade'])
            ->whereDoesntHave('transferRequests', function ($q) {
                $q->where('status', TransferRequest::STATUS_PENDING);
            });

        if ($gradeId) {
            $query->where('grade_id', $gradeId);
        }

        return $query->get();
    }

    /**
     * Obtener solicitudes pendientes para una sede (como origen)
     */
    public function getPendingRequestsForOriginSede(int $sedeId): \Illuminate\Database\Eloquent\Collection
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            return collect();
        }

        return TransferRequest::where('origin_sede_id', $sedeId)
            ->where('school_year_id', $activeYear->id)
            ->where('status', TransferRequest::STATUS_PENDING)
            ->with(['student', 'originSede', 'destinationSede', 'originGrade', 'destinationGrade', 'requester'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener solicitudes realizadas desde una sede (como destino/solicitante)
     */
    public function getRequestsMadeBySede(int $sedeId): \Illuminate\Database\Eloquent\Collection
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            return collect();
        }

        return TransferRequest::where('destination_sede_id', $sedeId)
            ->where('school_year_id', $activeYear->id)
            ->with(['student', 'originSede', 'destinationSede', 'originGrade', 'destinationGrade', 'requester', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener historial de traslados de un estudiante
     */
    public function getStudentTransferHistory(int $studentId): \Illuminate\Database\Eloquent\Collection
    {
        return TransferRequest::where('student_id', $studentId)
            ->with(['originSede', 'destinationSede', 'originGrade', 'destinationGrade', 'requester', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
