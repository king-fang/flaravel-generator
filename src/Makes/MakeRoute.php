<?php

namespace Flaravel\Generator\Makes;

use Flaravel\Generator\Commands\FlaravelGenerator;
use Flaravel\Generator\MakerTrait;
use Illuminate\Filesystem\Filesystem;

class MakeRoute
{
     use MakerTrait;

    protected $flaravelCommandObj;

    public function __construct(FlaravelGenerator $command, Filesystem $files)
    {
        $this->files = $files;
        $this->flaravelCommandObj = $command;

        $this->start();
    }

    /**
     * Start make controller.
     *
     * @return void
     */
    private function start()
    {
        $name = $this->flaravelCommandObj->getObjName('Name');
        $path = $this->getPath($name, 'route');
        $stub = $this->compileRouteStub();

        if (strpos($this->files->get($path), $stub) === false) {
            $this->files->append($path, $this->compileRouteStub());
            return $this->flaravelCommandObj->info('+ ' . $path . ' (Updated)');
        }

        return $this->flaravelCommandObj->comment("x $path" . ' (Skipped)');
    }

    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileRouteStub()
    {
        $stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/router.stub');

        $this->buildStub($this->flaravelCommandObj->getMeta(), $stub);

        return $stub;
    }
}
