<?php

namespace App\Commands;

use App\Services\SnowflakeGenerator;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class ListBlogsCommand extends Command
{
    protected $signature = 'blog:list
                            {--db=pgsql-a : Database connection to use (pgsql-a, pgsql-b, pgsql-c)}';

    protected $description = 'List all Blog posts with their Author (JOIN) and decoded Snowflake metadata';

    private const VALID_CONNECTIONS = ['pgsql-a', 'pgsql-b', 'pgsql-c'];

    public function handle(): int
    {
        $db = $this->option('db');

        if (! in_array($db, self::VALID_CONNECTIONS)) {
            $this->error('Invalid --db value. Allowed: '.implode(', ', self::VALID_CONNECTIONS));

            return self::FAILURE;
        }

        // JOIN blogs with authors on the same connection
        $rows = DB::connection($db)
            ->table('blogs')
            ->join('authors', 'blogs.author_id', '=', 'authors.id')
            ->select(
                'blogs.id as blog_id',
                'blogs.title',
                'blogs.body',
                'authors.id as author_id',
                'authors.name as author_name',
                'authors.email as author_email',
            )
            ->orderBy('blogs.id')
            ->get();

        if ($rows->isEmpty()) {
            $this->warn("No blog posts found on [{$db}].");

            return self::SUCCESS;
        }

        $this->info("Blog posts from [{$db}] — ".count($rows).' record(s):');
        $this->line('');

        $generator = new SnowflakeGenerator((int) env('SNOWFLAKE_NODE_ID', 1));

        $tableRows = [];
        $enriched  = [];

        foreach ($rows as $row) {
            $blogMeta   = $generator->parse((string) $row->blog_id);
            $authorMeta = $generator->parse((string) $row->author_id);

            $tableRows[] = [
                $row->blog_id,
                $row->title,
                $row->author_name,
                $row->author_email,
                $blogMeta['datetime'],
            ];

            $enriched[] = [
                'blog_id'   => $row->blog_id,
                'author_id' => $row->author_id,
                'blog_meta' => $blogMeta,
                'auth_meta' => $authorMeta,
            ];
        }

        $this->table(
            ['Blog ID', 'Title', 'Author', 'Author Email', 'Created At (from Snowflake)'],
            $tableRows
        );

        $this->line('');
        $this->line('<fg=yellow>Snowflake ID metadata:</>');

        foreach ($enriched as $entry) {
            $b = $entry['blog_meta'];
            $a = $entry['auth_meta'];
            $this->line("  Blog   {$entry['blog_id']}  → node={$b['node_id']}  seq={$b['sequence']}  ts={$b['datetime']}");
            $this->line("  Author {$entry['author_id']}  → node={$a['node_id']}  seq={$a['sequence']}  ts={$a['datetime']}");
            $this->line('');
        }

        return self::SUCCESS;
    }
}
