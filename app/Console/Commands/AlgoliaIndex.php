<?php

namespace App\Console\Commands;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use App\Airtable;
use App\Blog;
use App\Person;
use App\PrivateTag;
use App\PublicTag;
use App\SavedSearch;
use App\User;
use App\Util;
use Illuminate\Console\Command;

class AlgoliaIndex extends Command
{

    /** @var SearchIndex */
    protected $index;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:index {--limit=} {--v} {--all} {--clear}
    {--people} {--saved-searches} {--public-tags} {--private-tags}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send data to algolia index';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function _limit()
    {
        return $this->option('limit') ? $this->option('limit') : 5;
    }

    protected function _tables()
    {
        return $this->option('tables') ? explode(',', $this->option('tables')) : null;
    }

    protected function shouldIndex($table)
    {
        if ($this->hasOption($table) && $this->option($table)) {
            return true;
        }

        if ($this->option('all')) {
            return true;
        }

        return false;
    }

    protected function _shouldClearIndex()
    {
        return $this->option('clear') ? $this->option('clear') : false;
    }

    /**
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     * @throws \Exception
     */
    public function handle()
    {
        $client = SearchClient::create(Util::algoliaAppId(), Util::algoliaPrivateKey());
        $this->index = $client->initIndex('all');
        $this->info("Updating Algolia search index (limit: " . $this->_limit() . ")");

        if ($this->_shouldClearIndex()) {
            $this->info("*Clearing index*");
            $this->index->clearObjects();
        }

        if ($this->shouldIndex('users')) {
            $users = (new User())->getRecords();
            $this->_indexRecords($users, 'users');
        }

        if ($this->shouldIndex('people')) {
            $persons = (new Person())->getRecords();
            $this->_indexRecords($persons, 'people');
        }

        if ($this->shouldIndex('saved-searches')) {
            $savedSearches = (new SavedSearch())->getRecords();
            $this->_indexRecords($savedSearches, 'saved searches');
        }

        if ($this->shouldIndex('public-tags')) {
            $tags = (new PublicTag())->getRecords();
            foreach ($tags as $tag) {
                /** @var PublicTag $tag */
                if (! $tag->slug()) {
                    $slug = $tag->generateAndSaveSlug();
                    $this->info("Generating slug for {$tag->name()}: $slug");
                }
            }
            $this->_indexRecords($tags, 'public tags');
        }

        if ($this->shouldIndex('private-tags')) {
            $tags = (new PrivateTag())->getRecords();
            $this->_indexRecords($tags, 'tags');
        }

        return;
    }

    /**
     * @param Blog $blog
     *
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     */
    protected function _indexRecords($records, $name)
    {
        $this->info(count($records) . " $name");
        foreach ($records as $record) {
            /** @var Airtable $record */
            $this->info($record->searchTitle() . " - " . $record->searchIndexId());
            $data = $record->toSearchIndexArray();

            if ($this->option('v')) {
                $this->info(" - Data:");
                foreach ($data as $key => $val) {
                    $this->info("    - $key: $val");
                }
            }
            $this->index->saveObjects([$data], [
                'objectIDKey' => 'object_id',
            ]);
        }
    }

    /**
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     */
    protected function _indexPages()
    {
        $pages = [
            [
                'url'  => '/',
                'name' => 'Home',
            ],
            [
                'url'  => '/account/settings',
                'name' => 'Settings',
            ],
        ];

        $this->info("Indexing " . count($pages) . " pages");
        foreach ($pages as $page) {
            $page['object_id'] = 'page_' . $page['url'];
            $page['search_title'] = "Page: " . $page['name'];
            $page['type'] = 'page';
            $page['public'] = true;

            $this->info(" - " . $page['search_title'] . " - " . $page['object_id']);

            $this->index->saveObjects([$page], [
                'objectIDKey' => 'object_id',
            ]);
        }
    }
}
