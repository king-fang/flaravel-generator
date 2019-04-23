<?php

namespace Flaravel\Generator\Makes;

use Flaravel\Generator\Commands\FlaravelGenerator;
use Flaravel\Generator\MakerTrait;
use Flaravel\Generator\MigrateParser;
use Flaravel\Generator\SyntaxBuilder;
use Illuminate\Filesystem\Filesystem;

class MakeMigration
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
     * 开始创建迁移
     *
     * @return void
     */
    protected function start(){
        $name = 'create_'.str_plural(strtolower( $this->flaravelCommandObj->argument('name') )).'_table';

        $path = $this->getPath($name);

        if ( ! $this->classExists($name))
        {
            $this->makeDirectory($path);
            $this->files->put($path, $this->compileMigrationStub());
            return $this->flaravelCommandObj->info('+ ' . $path);
        }
        return $this->flaravelCommandObj->comment('x ' . $path);
    }

    /**
     * 获取存储迁移的路径
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return './database/migrations/'.date('Y_m_d_His').'_'.$name.'.php';
    }

    /**
     * 编译迁移模板
     *
     * @return string
     */
    protected function compileMigrationStub()
    {
        $stub = $this->files->get(substr(__DIR__,0, -5). 'Stubs/migration.stub');

        $this->replaceSchema($stub);
        $this->buildStub($this->flaravelCommandObj->getMeta(), $stub);

        return $stub;
    }

    /**
     * 将获取的migrate替换到 migration.stub中
     *
     * @param  string $stub
     * @param string $type
     * @return $this
     */
    protected function replaceSchema(&$stub)
    {
        if ($schema = $this->flaravelCommandObj->getMeta()['migrate'])
        {
            $schema = (new MigrateParser)->parse($schema);
        }

        $schema = (new SyntaxBuilder)->create($schema, $this->flaravelCommandObj->getMeta());

        $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], $schema, $stub);

        return $this;
    }

    /**
     * 判断迁移文件是否有重复
     * @param  string $name 预生成的迁移文件名称
     * @return bool
     */
    public function classExists($name)
    {
        $files = $this->files->allFiles('./database/migrations/');

        foreach ($files as $file) {
            if (strpos($file->getFilename(), $name) !== false) {
                return true;
            }
        }

        return false;
    }
}
