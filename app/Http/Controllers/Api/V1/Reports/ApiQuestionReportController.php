<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\AuditLog;
use App\Models\Question;
use App\Models\QuestionReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiQuestionReportController extends BaseApiController
{
    /**
     * Submit an issue report for a specific question.
     */
    public function store(int $questionId, Request $request): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'in:INCORRECT_ANSWER,TYPO,WRONG_OPTION,IMAGE_PROBLEM,EXPLANATION_PROBLEM,OTHER'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $question = Question::findOrFail($questionId);

        $report = QuestionReport::create([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'PENDING',
        ]);

        AuditLog::record(
            action: 'QUESTION_REPORTED',
            module: 'QUESTION',
            recordId: (string) $question->id,
            newValues: ['reason' => $request->reason]
        );

        return $this->successResponse([
            'report_id' => $report->id,
            'status' => $report->status,
        ], 'Report submitted successfully. Thank you for helping us maintain question quality.', 201);
    }
}
