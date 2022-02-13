<?php

namespace App\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;
use function base_path;

class MakeTrait extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Trait';

    protected $type = 'Trait';

    protected function getStub(){
        return base_path('stubs/trait.stub');
    }

    protected function getDefaultNamespace($rootNamespace){
        return $rootNamespace . '\Traits';
    }

    protected function replaceClass($stub, $name){
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace('{{trait_name}}', $class, $stub);
    }
}
