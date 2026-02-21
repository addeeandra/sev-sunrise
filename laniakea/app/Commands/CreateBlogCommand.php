<?php

namespace App\Commands;

use App\Models\Author;
use App\Models\Blog;
use App\Services\SnowflakeGenerator;
use LaravelZero\Framework\Commands\Command;

class CreateBlogCommand extends Command
{
    protected $signature = 'blog:create
                            {title : The blog post title}
                            {author_id : Snowflake ID of the author}
                            {--body= : Optional body content}
                            {--db=pgsql-a : Database connection to use (pgsql-a, pgsql-b, pgsql-c)}';

    protected $description = 'Create a new Blog post linked to an Author, with a Snowflake ID';

    private const VALID_CONNECTIONS = ['pgsql-a', 'pgsql-b', 'pgsql-c'];

    public function handle(): int
    {
        $db = $this->option('db');

        if (! in_array($db, self::VALID_CONNECTIONS)) {
            $this->error('Invalid --db value. Allowed: '.implode(', ', self::VALID_CONNECTIONS));

            return self::FAILURE;
        }

        $authorId = $this->argument('author_id');

        $authorExists = Author::on($db)->where('id', $authorId)->exists();
        if (! $authorExists) {
            $this->error("Author with ID [{$authorId}] not found on [{$db}].");

            return self::FAILURE;
        }

        $nodeId    = (int) env('SNOWFLAKE_NODE_ID', 1);
        $generator = new SnowflakeGenerator($nodeId);
        $id        = $generator->generate();
        $parsed    = $generator->parse($id);

        $blog = (new Blog)->setConnection($db);
        $blog->fill([
            'id'        => $id,
            'author_id' => $authorId,
            'title'     => $this->argument('title'),
            'body'      => $this->option('body'),
        ])->save();

        $this->info("Blog post created on [{$db}]:");
        $this->table(
            ['ID', 'Title', 'Author ID', 'Created At'],
            [[$blog->id, $blog->title, $blog->author_id, $blog->created_at]]
        );

        $this->line('');
        $this->line('<fg=yellow>Snowflake ID breakdown:</>');
        $this->line("  Timestamp : {$parsed['datetime']}");
        $this->line("  Node ID   : {$parsed['node_id']}");
        $this->line("  Sequence  : {$parsed['sequence']}");

        return self::SUCCESS;
    }
}
