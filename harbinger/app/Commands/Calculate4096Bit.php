<?php

namespace App\Commands;

use App\Utils\NativeCalculator;
use LaravelZero\Framework\Commands\Command;

class Calculate4096Bit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-4096';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trying binary arithmetic with 4096 bit integers';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // insert two numbers or random
        $this->warn('=== Binary Arithmetic with 4096 bit integers ===');
        $num1 = fake()->numerify(str_repeat('#', 1233));
        $num2 = fake()->numerify(str_repeat('#', 1233));

        $this->line('Number 1: ' . $num1);
        $this->line('Number 2: ' . $num2);

        $additionResult = (new NativeCalculator())->add($num1, $num2);

        $this->info('Addition: ' . $additionResult);
        $this->warn('=== =============================================== ===');
    }
}
