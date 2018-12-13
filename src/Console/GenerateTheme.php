<?php

namespace Caffeinated\Themes\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:theme {slug} {--quick}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new starter theme.';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $options     = $this->getOptions();
        $root = base_path('themes');
        $stubsPath   = __DIR__ . '/../../resources/stubs/theme';
        $slug        = $options['slug'];
        $name        = $this->format($slug);

        if (File::isDirectory($root . '/' . $name)) {
            return $this->error('Theme already exists!');
        }

        if (! File::isDirectory($root)) {
            File::makeDirectory($root);
        }

        foreach (File::allFiles($stubsPath) as $file) {
            $contents = $this->replacePlaceholders($file->getContents(), $options);
            $subPath  = $file->getRelativePathname();
            $filePath = $root.'/'.$options['name'].'/'.$subPath;
            $dir      = dirname($filePath);

            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            File::put($filePath, $contents);
        }

        $this->info("Theme created successfully.");
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $slug   = str_slug($this->argument('slug'));
        $name   = $this->format($slug);
        $quick  = $this->option('quick');
        $vendor = config('themes.vendor');
        $author = config('themes.author');

        return [
            'slug'              => $slug,
            'namespace'         => "Themes\\$name",
            'escaped_namespace' => "Themes\\\\$name",
            'name'              => $quick ? $name : $this->ask('What is your theme\'s name?', $name),
            'author'            => $quick ? $author : $this->ask('Who is the author of your theme?', $author),
            'version'           => $quick ? '1.0.0' : $this->ask('What is the version of your theme?', '1.0.0'),
            'description'       => $quick ? "$name theme." : $this->ask('Can you describe your theme?', "$name theme."),
            'package_name'      => $quick ? "{$vendor}/{$slug}" : $this->ask('What is the composer package name? [optional]', "{$vendor}/{$slug}")
        ];
    }

    /**
     * Replace placeholders with actual content.
     *
     * @param $contents
     * @param $options
     * @return mixed
     */
    protected function replacePlaceholders($contents, $options)
    {
        $find = [
            'DummyNamespace',
            'DummyEscapedNamespace',
            'DummyName',
            'DummySlug',
            'DummyVersion',
            'DummyDescription',
            'DummyPackageName',
            'DummyAuthor',
        ];

        $replace = [
            $options['namespace'],
            $options['escaped_namespace'],
            $options['name'],
            $options['slug'],
            $options['version'],
            $options['description'],
            $options['package_name'],
            $options['author'],
        ];

        return str_replace($find, $replace, $contents);
    }

    /**
     * Format the given name as the directory basename.
     * 
     * @param  string  $name
     * @return string
     */
    private function format($name)
    {
        return ucfirst(camel_case($name));
    }
}
