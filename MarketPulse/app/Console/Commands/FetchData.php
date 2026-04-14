<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Services\StockStrategy;
use App\Services\RedditStrategy;

#[Signature('app:fetch-data')]
#[Description('Fetch stock and Reddit data')]
class FetchData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(StockStrategy $stock, RedditStrategy $reddit)
    {
        $stock->fetch();
        $reddit->fetch();
    }
}
