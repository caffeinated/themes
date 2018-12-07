<?php

namespace Caffeinated\Themes\Console;

use File;
use Illuminate\Console\Command;

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
        $options = $this->getOptions();
        $destination = config('themes.paths.absolute');

        if (File::isDirectory($destination.'/'.ucfirst(camel_case($options['slug'])))) {
            return $this->error('Theme already exists!');
        }

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination);
        }

        $sourceFiles = File::allFiles(__DIR__.'/../../resources/stubs/theme');

        $this->comment('Generating theme...');

        foreach ($sourceFiles as $file) {
            $contents = $this->replacePlaceholders($file->getContents(), $options);
            $subPath = $file->getRelativePathname();
            $filePath = $destination . '/' . $options['name'] . '/' . $subPath;
            $dir = dirname($filePath);

            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            File::put($filePath, $contents);
        }

        $this->comment("Theme generated at [$destination].");
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $slug = str_slug($this->argument('slug'));
        $name = ucfirst(camel_case($slug));
        $quick = $this->option('quick');

        return [
            'slug' => $slug,
            'namespace' => "Themes\\$name",
            'name' => $quick ? $name : $this->ask('What is your theme\'s name?', $name),
            'version' => $quick ? '1.0.0' : $this->ask('What is the version of your theme?', '1.0.0'),
            'description' => $quick ? "$name theme." : $this->ask('Can you describe your theme?', "$name theme."),
            'package_name' => $quick ? "vendor/$slug" : $this->ask('What is the composer package name? [optional]', "vendor/$slug")
        ];
    }

    protected function replacePlaceholders($contents, $options)
    {
        $find = [
            'DummyNamespace',
            'DummyName',
            'DummySlug',
            'DummyVersion',
            'DummyDescription',
            'DummyPackageName',
        ];

        $replace = [
            $options['namespace'],
            $options['name'],
            $options['slug'],
            $options['version'],
            $options['description'],
            $options['package_name'],
        ];

        return str_replace($find, $replace, $contents);
    }

    /**
     * Add a path repository for the tool to the application's composer.json file.
     *
     * @return void
     */
    protected function addToolRepositoryToRootComposer()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['repositories'][] = [
            'type' => 'path',
            'url' => './'.$this->relativeToolPath(),
        ];

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Add a package entry for the tool to the application's composer.json file.
     *
     * @return void
     */
    protected function addToolPackageToRootComposer()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['require'][$this->argument('name')] = '*';

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
}
