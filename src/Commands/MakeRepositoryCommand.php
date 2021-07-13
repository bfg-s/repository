<?php

namespace Bfg\Repository\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeRepositoryCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository for Eloquent model class';

    /**
     * RepositoryMakeCommand constructor.
     *
     * @param  Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        if (!is_dir(app_path('Repositories'))) {

            mkdir(app_path('Repositories'), 0777, 1);
        }

        parent::__construct($files);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $searches = [
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel', 'repositoryMethods', 'repositoryImplement', 'repositoryModel'],
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ namespacedUserModel }}', '{{ methods }}',  '{{ implement }}', '{{ r_model }}'],
            ['{{namespace}}', '{{rootNamespace}}', '{{namespacedUserModel}}', '{{methods}}', '{{implement}}', '{{r_model}}'],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search, [
                    $this->getNamespace($name),
                    $this->rootNamespace(),
                    $this->userProviderModel(),
                    $this->repositoryMethods(),
                    $this->repositoryImplement(),
                    $this->repositoryModel()
                ], $stub
            );
        }

        return $this;
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        if (!str_ends_with($name, "Repository")) {

            $name .= "Repository";
        }

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * @return string
     */
    protected function repositoryModel()
    {
        $line = "// TODO: Implement getModelClass() method.";

        $model = $this->option('model');

        if (!$model) {

            $model = $this->argument('name');
        }

        if ($model && !class_exists($model)) {

            if (class_exists("App\\Models\\" . $model)) {

                $model = "App\\Models\\" . $model;

            } else if (class_exists("App\\" . $model)) {

                $model = "App\\" . $model;

            } else {

                $model = null;
            }
        } else {
            $model = null;
        }

        return $model ? "return \\" . trim($model) . "::class;" : $line;
    }

    /**
     * @return string
     */
    protected function repositoryImplement()
    {
        return "";
    }

    /**
     * @return string
     */
    protected function repositoryMethods()
    {
        $methodsText = "";

        foreach ($this->option('methods') as $item) {
            $methods = array_map('trim', explode(',', $item));
            foreach ($methods as $method) {
                $m = <<<TEXT

    /**
     * @return mixed
     */
    public function {$method}()
    {
        //   
    }
    
TEXT;
                $methodsText .= $m;
            }
        }

        return !empty($methodsText) ? "\n    " . rtrim($methodsText) : "";
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            //['contract', 'c', InputOption::VALUE_OPTIONAL, 'Create contract for repository'],
            ['methods', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Methods for repository'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Model of repository'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the repository already exists'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/repository.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return is_dir(app_path('Repositories')) ? $rootNamespace.'\\Repositories' : $rootNamespace;
    }
}
