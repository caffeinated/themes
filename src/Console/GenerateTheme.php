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
        $options = $this->getOptions();
        $destination = config('themes.paths.absolute');
        $stubsPath = __DIR__ . '/../../resources/stubs/theme';
        $slug = $options['slug'];
        $name = ucfirst(camel_case($slug));

        if (File::isDirectory($destination . '/' . $name)) {
            return $this->error('Theme already exists!');
        }

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination);
        }

        $this->info('Generating theme...');

        foreach (File::allFiles($stubsPath) as $file) {
            $contents = $this->replacePlaceholders($file->getContents(), $options);
            $subPath = $file->getRelativePathname();
            $filePath = $destination . '/' . $options['name'] . '/' . $subPath;
            $dir = dirname($filePath);

            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            File::put($filePath, $contents);
        }

        $this->addThemeRepositoryToComposer(config('themes.paths.absolute') . '/' . $name);
        $this->addThemePackageToComposer($options['package_name']);

        symlink("../../themes/$slug/dist", public_path("theme-assets/$slug"));

        $this->info("Theme generated at [$destination].");
        $this->info("If there are required dependencies, please run <fg=cyan;>composer update</>.");
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
            'escaped_namespace' => "Themes\\\\$name", // for composer.json psr-4
            'name' => $quick ? $name : $this->ask('What is your theme\'s name?', $name),
            'version' => $quick ? '1.0.0' : $this->ask('What is the version of your theme?', '1.0.0'),
            'description' => $quick ? "$name theme." : $this->ask('Can you describe your theme?', "$name theme."),
            'package_name' => $quick ? "vendor/$slug" : $this->ask('What is the composer package name? [optional]', "vendor/$slug")
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
        ];

        $replace = [
            $options['namespace'],
            $options['escaped_namespace'],
            $options['name'],
            $options['slug'],
            $options['version'],
            $options['description'],
            $options['package_name'],
        ];

        return str_replace($find, $replace, $contents);
    }

    /**
     * Add package to composer
     *
     * @param $relativeThemePath
     */
    protected function addThemeRepositoryToComposer($relativeThemePath)
    {
        $composer = $this->getComposerContents();
        $composer['repositories'][] = [
            'type' => 'path',
            'url' => $relativeThemePath,
        ];

        $this->writeToComposer($composer);
    }

    /**
     * Add package as a required dependency to composer.
     *
     * @param $packageName
     */
    protected function addThemePackageToComposer($packageName)
    {
        $composer = $this->getComposerContents();

        $composer['require'][$packageName] = '*';

        $this->writeToComposer($composer);
    }

    /**
     * @return array
     */
    protected function getComposerContents()
    {
        return json_decode(file_get_contents(base_path('composer.json')), true);
    }

    /**
     * Write to composer
     *
     * @param $composer
     * @return bool|int
     */
    protected function writeToComposer($composer)
    {
        $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;

        return file_put_contents(base_path('composer.json'), json_encode($composer, $flags));
    }
}
