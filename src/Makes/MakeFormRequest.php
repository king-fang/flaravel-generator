<?php

namespace Flaravel\Generator\Makes;

use Flaravel\Generator\Commands\FlaravelGenerator;
use Flaravel\Generator\MakerTrait;
use Illuminate\Filesystem\Filesystem;

class MakeFormRequest
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
        $this->makeRequest('Request', 'request');
        $this->makeRequest($name . 'Request', 'request_model');
    }

    protected function makeRequest($name, $stubname)
    {
        $path = $this->getPath($name, 'request');

        if(! $this->files->exists($path))
        {
            $this->makeDirectory($path);

            $this->files->put($path, $this->compileStub($stubname));

            $this->flaravelCommandObj->info('+ ' . $path);
        }
    }
}
