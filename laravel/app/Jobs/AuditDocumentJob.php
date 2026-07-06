<?php

namespace App\Jobs;

use App\Enums\DocumentStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditDocumentJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $document_id, public string $text) { }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // update status to processing
            DB::table('documents')
                ->where('id', $this->document_id)
                ->update([
                    'status' => DocumentStatus::Processing
                ]);
        } catch (\Exception $e) {
            Log::error('AuditDocumentJob Failed', [
                'document_id' => $this->document_id,
                'error' => $e->getMessage()
            ]);

            DB::table('documents')
                ->where('id', $this->document_id)
                ->update([
                    'status' => DocumentStatus::Failed,
                    'error_message' => $e->getMessage()
                ]);

            throw $e; // job failed so it's retried
        }
    }
}
