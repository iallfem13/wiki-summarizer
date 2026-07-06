<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Jobs\AuditDocumentJob;
use App\Models\Document;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;

class DocumentController extends Controller
{
    /**
     * Receives and processes request
     *  Stores into DB for follow up
     *  Dispatches job into Queue for Ai Service to pickup
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // get file content
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'text' => 'required|string'
            ]);

            // save into db
            $newDocument = Document::create([
                'filename' => $validated['title'],
                'slug' => \Str::slug($validated['title'], '-'),
                'original_content' => $validated['text']
            ]);

            // dispatch doc to queue
            AuditDocumentJob::dispatch($newDocument->id, $newDocument->original_content);

            return response()->json([
                'message' => 'Request received!'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('There was an error: ' . $e->getMessage());

            return response()->json([
                'error' => 'There was an error while processing your request.'
            ], 500);
        }
    }

    /**
     * Process result from summarizing user input text.
     *  Expected request:
     *      document_id {integer} - db's document id
     *      summary {string}      - text summarized by AI service
     *      tech_stack {array}    - list of technologies in text
     */
    public function result(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_id' => 'required|integer',
            'summary' => 'required|string',
            'execution_lifecycle' => 'required|string',
            'tech_stack' => 'required|array'
        ]);

        if ($validated['execution_lifecycle']) {
            $validated['summary'] .= "\n\n" . $validated['execution_lifecycle'];
        }

        DB::table('documents')
            ->where('id', $validated['document_id'])
            ->update([
                'status' => DocumentStatus::Completed,
                'summary' => $validated['summary'],
                'tech_stacks' => $validated['tech_stack'],
                'processed_at' => now()
            ]);

        return response()->json([
            'message' => 'Results have been received!'
        ], 200);
    }

    /**
     * Fetch all documents based status
     *  Uses $request parameter: selectedStatus
     */
    public function documents(Request $request): JsonResponse
    {
        $response = [
            'data' => 'Results have been received!',
            'code' => 200
        ];

        try {
            // fetch all docs based on 
            $status = $request->input('selectedStatus') ?: DocumentStatus::Completed;
            $validatedStatus = DocumentStatus::from($status);

            $documents = DB::table('documents')
                ->select('id', 'filename', 'slug', 'updated_at')
                ->where('status', $validatedStatus->value)
                ->get();

            $response['data'] = $documents;
        } catch (\ValueError $e) {
            $response = [
                'data' => 'Invalid status provided.',
                'code' => 400
            ];
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());

            $response = [
                'data' => 'An error has ocurred.',
                'code' => 400
            ];
        }

        return response()->json($response['data'], $response['code']);
    }

    /**
     * Display a document details based on the slug
     *  Returns the filename, summary (proccessed text) and tech stacks
     */
    public function view(Request $request)
    {
        try {
            $document = DB::table('documents')
                ->select('id', 'filename', 'summary', 'tech_stacks', 'original_content')
                ->where('slug', $request->slug)
                ->firstOrFail();

            $document->tech_stacks = json_decode($document->tech_stacks, true);

            $data = ['document' => $document];
        } catch (ModelNotFoundException $e) {
            $data = ['error' => 'Document not found.'];
        } catch (\Exception $e) {
            $data = ['error' => 'Something went wrong.'];
        }

        return Inertia::render('Document', $data);
    }
}
