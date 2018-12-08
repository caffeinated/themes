<?php

namespace Caffeinated\Themes\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

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
        $name = ucfirst(camel_case($options['slug']));

        if (File::isDirectory($destination.'/'. $name)) {
            return $this->error('Theme already exists!');
        }

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination);
        }

        $this->comment('Generating theme...');

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

        $this->addToolRepositoryToRootComposer(config('themes.paths.absolute') . '/' . $name);
        $this->addToolPackageToRootComposer($options['package_name']);
        $this->runCommand('composer update', getcwd());

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
     * @param $relativeThemePath
     * @return void
     */
    protected function addToolRepositoryToRootComposer($relativeThemePath)
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['repositories'][] = [
            'type' => 'path',
            'url' => $relativeThemePath,
        ];

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Add a package entry for the tool to the application's composer.json file.
     *
     * @param $packageName
     * @return void
     */
    protected function addToolPackageToRootComposer($packageName)
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['require'][$packageName] = '*';

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Run the given command as a process.
     *
     * @param  string  $command
     * @param  string  $path
     * @return void
     */
    protected function runCommand($command, $path)
    {
        $process = (new Process($command, $path))->setTimeout(null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });
    }
}
