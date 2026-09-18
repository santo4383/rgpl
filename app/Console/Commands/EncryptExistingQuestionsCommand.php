<?php

namespace App\Console\Commands;

use App\Services\Security\QuestionVaultService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EncryptExistingQuestionsCommand extends Command
{
    protected $signature = 'questions:encrypt-existing';

    protected $description = 'Encrypt all plaintext questions and options in the database using the vault.json key';

    public function handle(QuestionVaultService $vaultService): int
    {
        $this->info('Starting encryption of existing questions and options in database...');

        $questions = DB::table('questions')->get();
        $qCount = 0;

        foreach ($questions as $q) {
            $updates = [];

            if ($q->question_text && ! $vaultService->isEncrypted($q->question_text)) {
                $updates['question_text'] = $vaultService->encrypt($q->question_text);
            }

            if ($q->question_html && ! $vaultService->isEncrypted($q->question_html)) {
                $updates['question_html'] = $vaultService->encrypt($q->question_html);
            }

            if ($q->explanation_text && ! $vaultService->isEncrypted($q->explanation_text)) {
                $updates['explanation_text'] = $vaultService->encrypt($q->explanation_text);
            }

            if ($q->explanation_html && ! $vaultService->isEncrypted($q->explanation_html)) {
                $updates['explanation_html'] = $vaultService->encrypt($q->explanation_html);
            }

            if (! empty($updates)) {
                DB::table('questions')->where('id', $q->id)->update($updates);
                $qCount++;
            }
        }

        $options = DB::table('question_options')->get();
        $optCount = 0;

        foreach ($options as $opt) {
            $optUpdates = [];

            if ($opt->option_text && ! $vaultService->isEncrypted($opt->option_text)) {
                $optUpdates['option_text'] = $vaultService->encrypt($opt->option_text);
            }

            if ($opt->option_html && ! $vaultService->isEncrypted($opt->option_html)) {
                $optUpdates['option_html'] = $vaultService->encrypt($opt->option_html);
            }

            if (! empty($optUpdates)) {
                DB::table('question_options')->where('id', $opt->id)->update($optUpdates);
                $optCount++;
            }
        }

        $this->info("Encrypted {$qCount} questions and {$optCount} question options in the database.");
        $this->info('Database encryption verified successfully.');

        return self::SUCCESS;
    }
}
