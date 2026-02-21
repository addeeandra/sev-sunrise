<?php

namespace App\Commands;

use App\Utils\NativeCalculator;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Calculate2048Bit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-2048';

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
        // insert two numbers or random
        $this->warn('=== Binary Arithmetic with 2048 - 4096 bit integers ===');
        $num1 = fake()->numerify(str_repeat('#', 617));
        $num2 = fake()->numerify(str_repeat('#', 617));

        $this->line('Number 1: ' . $num1);
        $this->line('Number 2: ' . $num2);

        $additionResult = (new NativeCalculator())->add($num1, $num2);

        $this->info('Addition: ' . $additionResult);
        $this->warn('=== =============================================== ===');
    }
}
