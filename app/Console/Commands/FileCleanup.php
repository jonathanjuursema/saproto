<?php

namespace App\Console\Commands;

use App\Models\StorageEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FileCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:filecleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears all files that are orphaned (i.e. not referenced anywhere) for privacy reasons as well as storage space.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting clean-up.');

        $count = 0;

        foreach (StorageEntry::all() as $file) {
            if ($file->isOrphan()) {
                $count++;
                Storage::delete($file->filename);
                $file->delete();
            }
        }

        $this->info("Found and deleted {$count} orphaned files.");
    }
}
