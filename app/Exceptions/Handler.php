<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // $this->reportable(function (Throwable $e) {
            
        // });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('V1.0/general/getAllClassesAndSubjectsOfGrade/*')) {
                return response()->json([
                    'message' => 'this grade id is not valid.',
                    'error' => $e->getMessage()
                ], 404);
            }

            if ($request->is('V1.0/principal/assignClassesToSupervisor/*') ||
                $request->is('V1.0/principal/assign_Class_Subject_ToTeacher/*')) {
                return response()->json([
                    'message' => 'this employee id is not valid.',
                    'error' => $e->getMessage()
                ], 404);
            }

            if ($request->is('V1.0/secretary/*')) {
                return response()->json([
                    'message' => 'this student id is not valid.',
                    'error' => $e->getMessage()
                ], 404);
            }

            if ($request->is('V1.0/supervisor/editTestType/*')) {
                return response()->json([
                    'message' => 'this type id is not valid.',
                    'error' => $e->getMessage()
                ], 404);
            }
        });
    }
}
