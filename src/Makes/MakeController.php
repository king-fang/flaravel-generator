<?php

namespace Flaravel\Generator\Makes;

use Flaravel\Generator\Commands\FlaravelGenerator;
use Flaravel\Generator\MakerTrait;
use Illuminate\Filesystem\Filesystem;

class MakeController
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
        $name = $this->flaravelCommandObj->option('f').'/'.$this->flaravelCommandObj->getObjName('Names') . 'Controller';
        $path = $this->getPath($name, 'controller');

        $this->createBaseControllerStub();

        if ($this->files->exists($path))
        {
            return $this->flaravelCommandObj->comment("x " . $path);
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileControllerStub());

        $this->flaravelCommandObj->info('+ ' . $path);
    }

    /**
     * Compile the controller stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        $stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/controller.stub');

        $stub = $this->buildStub($this->flaravelCommandObj->getMeta(), $stub);

        $stub = str_replace('\{{Folder}}', $this->flaravelCommandObj->option('f') == null ? '' : '\\'.$this->flaravelCommandObj->option('f'), $stub);

        return $stub;
    }

    public function createBaseControllerStub()
    {
        $base_stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/base_controller.stub');
        $base_controller_path = $this->getPath('', 'base_controller');

        if (!$this->files->exists($base_controller_path))
        {
            $this->files->put($base_controller_path, $base_stub);
            return $this->flaravelCommandObj->info("+ $base_controller_path");
        }
    }
}
