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

        symlink("../../themes/$slug/dist", public_path("theme-assets/$slug"));

        $this->info("Theme generated at [$destination].");
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
}
