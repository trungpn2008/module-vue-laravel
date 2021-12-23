<?php

namespace DXMB\Modules\Commands;

use Illuminate\Support\Str;
use DXMB\Modules\Support\Config\GenerateConfigReader;
use DXMB\Modules\Support\Stub;
use DXMB\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class VueMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'vue';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-vue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new restful Vue for the specified module.';

    /**
     * Get controller name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $controllerPath = GenerateConfigReader::read('Resources');
        $viewPathVue = '/assets/js/Pages';
        return $path . $controllerPath->getPath() . $viewPathVue .'/' . $this->getControllerName() . '.vue';
    }

    /**
     * @return string
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub($this->getStubName(), [
            'MODULENAME'        => $module->getStudlyName(),
            'CONTROLLERNAME'    => $this->getControllerName(),
            'NAMESPACE'         => $module->getStudlyName(),
            'CLASS_NAMESPACE'   => $this->getClassNamespace($module),
            'CLASS'             => $this->getControllerNameWithoutNamespace(),
            'LOWER_NAME'        => $module->getLowerName(),
            'MODULE'            => $this->getModuleName(),
            'NAME'              => $this->getModuleName(),
            'STUDLY_NAME'       => $module->getStudlyName(),
            'MODULE_NAMESPACE'  => $this->laravel['modules']->config('namespace'),
        ]))->render();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['vue', InputArgument::REQUIRED, 'The name of the vue class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['create', 'c', InputOption::VALUE_NONE, 'Generate create vue', null],
            ['edit', 'e', InputOption::VALUE_NONE, 'Generate create edit'],
            ['show', 's', InputOption::VALUE_NONE, 'Generate create show'],
        ];
    }

    /**
     * @return array|string
     */
    protected function getVueName()
    {
        $vue = Str::studly($this->argument('vue'));

        if (Str::contains(strtolower($vue), 'vue') === false) {
            $vue .= 'vue';
        }

        return $vue;
    }

    /**
     * @return array|string
     */
    private function getControllerNameWithoutNamespace()
    {
        return class_basename($this->getVueName());
    }

    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.vue.namespace') ?: $module->config('paths.generator.vue.path', 'Resources/assets/js/Pages');
    }

    /**
     * Get the stub file name based on the options
     * @return string
     */
    protected function getStubName()
    {
        if ($this->option('create') === true) {
            $stub = '/assets/js/Pages/create.stub';
        } elseif ($this->option('edit') === true) {
            $stub = '/assets/js/Pages/edit.stub';
        } elseif ($this->option('show') === true) {
            $stub = '/assets/js/Pages/show.stub';
        } else {
            $stub = '/assets/js/Pages/index.stub';
        }

        return $stub;
    }
}
