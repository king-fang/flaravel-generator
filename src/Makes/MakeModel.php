<?php

namespace Flaravel\Generator\Makes;

use Flaravel\Generator\Commands\FlaravelGenerator;
use Flaravel\Generator\MakerTrait;
use Flaravel\Generator\MigrateParser;
use Illuminate\Filesystem\Filesystem;


class MakeModel
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
        $name = $this->flaravelCommandObj->option('f').'/'.$this->flaravelCommandObj->getObjName('Name');
        $path = $this->getPath($name, 'model');

        if ($this->files->exists($path))
        {
            return $this->flaravelCommandObj->comment("x $path");
        }
        $this->makeDirectory($path);
        $this->files->put($path, $this->compileModelStub());

        $this->flaravelCommandObj->info('+ ' . $path);
    }

    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileModelStub()
    {
        $stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/model.stub');

        $this->buildStub($this->flaravelCommandObj->getMeta(), $stub);
        $this->buildFillable($stub);

        return $stub;
    }

    /**
     * Build stub replacing the variable template.
     *
     * @return string
     */
    protected function buildFillable(&$stub)
    {
        $schemaArray = [];

        $schema = $this->flaravelCommandObj->getMeta()['migrate'] ?? '';

        if ($schema)
        {
            $items = (new MigrateParser)->parse($schema);
            foreach($items as $item)
            {
                $schemaArray[] = "'{$item['name']}'";
            }

            $schemaArray = join(", ", $schemaArray);
        }
        $stub = str_replace(['{{fillable}}','\{{Folder}}'], [empty($schemaArray) ? "'*'" : $schemaArray, $this->flaravelCommandObj->option('f') == null ? '' : '\\'.$this->flaravelCommandObj->option('f') ], $stub);
        return $this;
    }
}
