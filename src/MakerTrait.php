<?php

namespace Flaravel\Generator;

use App\Console\Commands\FlaravelGenerator;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;

trait MakerTrait
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * @param ScaffoldMakeCommand $scaffoldCommand
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * 生成文件替换模板
     *
     * @param array $metas
     * @param string &$template
     * @return void
     */
    protected function buildStub(array $metas, &$template)
    {

        foreach($metas as $k => $v)
        {
            $template = str_replace("{{". $k ."}}", $v, $template);
        }

        return $template;
    }

    /**
     * Get the path to where we should store the controller.
     *
     * @param $file_name
     * @param string $path
     * @return string
     */
    protected function getPath($file_name, $path='controller')
    {
        if($path == "controller")
        {
            return './app/Http/Controllers/' . $file_name . '.php';
        }
        elseif($path == "request")
        {
            return './app/Http/Requests/'.$file_name.'.php';
        }
        elseif($path == "model")
        {
            return './app/Models/'.$file_name.'.php';
        }
        elseif($path == "localization"){
            return './resources/lang/'.$file_name.'.php';
        }
        elseif($path == "route"){
            return './routes/api.php';
        }
    }

    protected function getFile($file)
    {
        return $this->files->get($file);
    }

    protected function existsDirectory($path)
    {
        return !$this->files->isDirectory($path);
    }

    /**
     * 没有目录生成目录
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if ( ! $this->files->isDirectory(dirname($path)))
        {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    protected function compileStub($filename)
    {
        $stub = $this->files->get(__DIR__ . '/Stubs/'.$filename.'.stub');

        $this->buildStub($this->flaravelCommandObj->getMeta(), $stub);

        return $stub;
    }

    /**
     * Get the application namespace.
     *
     * @return string
     */
    protected function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }

}
