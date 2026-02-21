<?php

namespace App\Commands;

use App\Models\Author;
use App\Services\SnowflakeGenerator;
use LaravelZero\Framework\Commands\Command;

class CreateAuthorCommand extends Command
{
    protected $signature = 'author:create
                            {name : The author\'s display name}
                            {email : The author\'s email address}
                            {--db=pgsql-a : Database connection to use (pgsql-a, pgsql-b, pgsql-c)}';

    protected $description = 'Create a new Author with a Snowflake ID on the specified database';

    private const VALID_CONNECTIONS = ['pgsql-a', 'pgsql-b', 'pgsql-c'];

    public function handle(): int
    {
        $db = $this->option('db');

        if (! in_array($db, self::VALID_CONNECTIONS)) {
            $this->error('Invalid --db value. Allowed: '.implode(', ', self::VALID_CONNECTIONS));

            return self::FAILURE;
        }

        $nodeId    = (int) env('SNOWFLAKE_NODE_ID', 1);
        $generator = new SnowflakeGenerator($nodeId);
        $id        = $generator->generate();
        $parsed    = $generator->parse($id);

        $author = (new Author)->setConnection($db);
        $author->fill([
            'id'    => $id,
            'name'  => $this->argument('name'),
            'email' => $this->argument('email'),
        ])->save();

        $this->info("Author created on [{$db}]:");
        $this->table(
            ['ID', 'Name', 'Email', 'Created At'],
            [[$author->id, $author->name, $author->email, $author->created_at]]
        );

        $this->line('');
        $this->line('<fg=yellow>Snowflake ID breakdown:</>');
        $this->line("  Timestamp : {$parsed['datetime']}");
        $this->line("  Node ID   : {$parsed['node_id']}");
        $this->line("  Sequence  : {$parsed['sequence']}");

        return self::SUCCESS;
    }
}
