<?php

namespace Flaravel\Generator\Commands;

use Flaravel\Generator\MakerTrait;
use Flaravel\Generator\Makes\MakeController;
use Flaravel\Generator\Makes\MakeFormRequest;
use Flaravel\Generator\Makes\MakeMigration;
use Flaravel\Generator\Makes\MakeModel;
use Flaravel\Generator\Makes\MakeRoute;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class FlaravelGenerator extends Command
{
    use MakerTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flaravel:make {name : Class (singular) for example User} {--migrate=} {--f=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '代码自动生成器';


    protected $meta;

    protected $files;

    protected $composer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        $this->composer = app()['composer'];
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $header = "flaravel: {$this->getObjName("Name")}".'--START';
        $footer = "flaravel: {$this->getObjName("Name")}".'--END';

        $this->line("\n----------- $header -----------\n");

        $this->makeMeta();
        $this->makeModel();
        $this->makeController();
        $this->makeFormRequest();
        $this->makeRoute();
        if($this->option('migrate'))
        {
            $this->makeMigration();
            $this->call('migrate');
        }
        $this->line("\n----------- $footer -------------");

        $this->composer->dumpAutoloads();

        $this->info("dump autoload successfully!");
    }


     /**
     * 生成所需要的迁移文件
     *
     * @return void
     */
    protected function makeMeta()
    {
        $this->meta['action'] = 'create';
        $this->meta['var_name'] = $this->getObjName("name");
        $this->meta['table'] = $this->getObjName("names");

        $this->meta['namespace'] = $this->getAppNamespace();

        $this->meta['Model'] = $this->getObjName('Name');
        $this->meta['Models'] = $this->getObjName('Names');
        $this->meta['model'] = $this->getObjName('name');
        $this->meta['models'] = $this->getObjName('names');
        $this->meta['ModelMigration'] = "Create{$this->meta['Models']}Table";
        if( $this->option('migrate'))
        {
            $this->meta['migrate'] = $this->option('migrate');
        }
    }

    /**
     * 生成迁移文件
     */
    protected function makeMigration()
    {
        new MakeMigration($this, $this->files);
    }


    /**
     * 生成 模型
     *
     * @return void
     */
    protected function makeModel()
    {
        new MakeModel($this, $this->files);
    }

    /**
     * 创建控制器
     *
     * @return void
     */
    private function makeController()
    {
        new MakeController($this, $this->files);
    }


    /**
     * 创建路由
     *
     * @return void
     */
    private function makeRoute()
    {
        new MakeRoute($this, $this->files);
    }

    /**
     * 创建Request
     * @return void
     */
    private function makeFormRequest()
    {
        new MakeFormRequest($this, $this->files);
    }
    /**
     * 生成文件所需要的名称
     *
     * @param string $config
     * @return mixed
     * @throws \Exception
     */
    public function getObjName($config = 'Name')
    {
        $names = [];
        $args_name = $this->argument('name');

        // Name[0] = Tweet
        $names['Name'] = str_singular(ucfirst($args_name));
        // Name[1] = Tweets
        $names['Names'] = str_plural(ucfirst($args_name));
        // Name[2] = tweets
        $names['names'] = str_plural(strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', $args_name)));
        // Name[3] = tweet
        $names['name'] = str_singular(strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', $args_name)));

        if (!isset($names[$config])) {
            throw new \Exception("Position name is not found");
        };

        return $names[$config];
    }


    public function getMeta()
    {
        return $this->meta;
    }
}
