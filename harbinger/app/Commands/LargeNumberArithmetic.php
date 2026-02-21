<?php

namespace App\Commands;

use App\Utils\GmpCalculator;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class LargeNumberArithmetic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:arithmetic {database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trying binary arithmetic with 2048 bit integers';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Run for database ' . $dbconn = $this->argument('database'));

        Artisan::call('migrate:fresh', [
            '--database' => $dbconn,
            '--seed' => DatabaseSeeder::class,
        ]);

        $db = DB::connection($dbconn);

        $this->line('=== ======================================== ===');
        $this->line('=== Binary Arithmetic with 2048 bit integers ===');
        $this->line('=== ======================================== ===');

        $p2048 = $db->selectOne('select * from demo where code = \'P-2048\'');
        $number2048b = $p2048->price;
        $this->warn('[N]: ' . $number2048b);
        $this->warn('Length: ' . strlen($number2048b));
        $this->line('');

        $result = (new GmpCalculator())->add($number2048b, $number2048b);
        $this->info('>>> [ADDITION]: ' . $result);
        $this->info('Length: ' . strlen($result));
        $this->line('');

        $result = (new GmpCalculator())->sub($number2048b, $number2048b);
        $this->info('>>> [SUBSTRACTION]: ' . $result);
        $this->warn('Length: ' . strlen($result));
        $this->line('');

        $result = (new GmpCalculator())->mul($number2048b, '3');
        $this->info('>>> [MULTIPLY by 3]: ' . $result);
        $this->warn('Length: ' . strlen($result));
        $this->line('');

        $result = (new GmpCalculator())->divQ($number2048b, '2');
        $this->info('>>> [DIVIDE by 2]: ' . $result);
        $this->warn('Length: ' . strlen($result));
        $this->line('');

        $this->line('=== ======================================== ===');
        $this->line('=== Binary Arithmetic with 4096 bit integers ===');
        $this->line('=== ======================================== ===');

        $p4096 = $db->selectOne('select * from demo where code = \'P-4096\'');
        $number4096b = $p4096->price;
        $this->warn('[N]: ' . $number4096b);
        $this->warn('Length: ' . strlen($number4096b));
        $this->line('');

        $result = (new GmpCalculator())->add($number4096b, $number4096b);
        $this->info('>>> [ADDITION]: ' . $result);
        $this->warn('Length: ' . strlen($result));
        $this->line('');

        $result = (new GmpCalculator())->sub($number4096b, $number4096b);
        $this->info('>>> [SUBSTRACTION]: ' . $result);
        $this->warn('Length: ' . strlen($result));
        $this->line('');

        $result = (new GmpCalculator())->mul($number4096b, '3');
        $this->info('>>> [MULTIPLY by 3]: ' . $result);
        $this->warn('Length: ' . strlen($result));
        $this->line('');

        $result = (new GmpCalculator())->divQ($number4096b, '2');
        $this->info('>>> [DIVIDE by 2]: ' . $result);
        $this->warn('Length: ' . strlen($result));
        $this->line('');
    }
}
