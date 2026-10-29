<?php

namespace App\Console\Commands;

use App\Models\Folder;
use Illuminate\Console\Command;

class RecountRepositoryStats extends Command
{
    protected $signature = 'repository:recount';

    protected $description = 'Recompute subtree folder/file/storage stats for every repository folder';

    public function handle(): int
    {
        Folder::recountAll();

        $this->info('Repository stats recomputed.');

        return self::SUCCESS;
    }
}
