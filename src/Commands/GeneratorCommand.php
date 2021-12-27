<?php

namespace DXMB\Modules\Commands;

use Illuminate\Console\Command;
use DXMB\Modules\Exceptions\FileAlreadyExistException;
use DXMB\Modules\Generators\FileGenerator;

abstract class GeneratorCommand extends Command
{
    /**
     * The name of 'name' argument.
     *
     * @var string
     */
    protected $argumentName = '';

    /**
     * Get template contents.
     *
     * @return string
     */
    abstract protected function getTemplateContents();

    /**
     * Get the destination file path.
     *
     * @return string
     */
    abstract protected function getDestinationFilePath();

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        if (is_array($this->getDestinationFilePath())){
            $path = [];
            foreach ($this->getDestinationFilePath() as $item){
                $path[]= str_replace('\\', '/', $item);

                if (!$this->laravel['files']->isDirectory($dir = dirname(str_replace('\\', '/', $item)))) {
                    $this->laravel['files']->makeDirectory($dir, 0777, true);
                }
            }
            $contents = $this->getTemplateContents();
            if (is_array($contents)){
                foreach ($contents as $key => $content){
                    $this->renderContents($path[$key],$content);
                }
            }
        }else{
            $path = str_replace('\\', '/', $this->getDestinationFilePath());

            if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
                $this->laravel['files']->makeDirectory($dir, 0777, true);
            }

            $contents = $this->getTemplateContents();
            $this->renderContents($path,$contents);

        }
        return 0;
    }

    private function renderContents($path,$contents){
        try {
            $overwriteFile = $this->hasOption('force') ? $this->option('force') : false;
            (new FileGenerator($path, $contents))->withFileOverwrite($overwriteFile)->generate();

            $this->info("Created : {$path}");

        } catch (FileAlreadyExistException $e) {
            $this->error("File : {$path} already exists.");

            return E_ERROR;
        }
    }

    /**
     * Get class name.
     *
     * @return string
     */
    public function getClass()
    {
        return class_basename($this->argument($this->argumentName));
    }

    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace() : string
    {
        return '';
    }

    /**
     * Get class namespace.
     *
     * @param \DXMB\Modules\Module $module
     *
     * @return string
     */
    public function getClassNamespace($module)
    {
        $extra = str_replace($this->getClass(), '', $this->argument($this->argumentName));

        $extra = str_replace('/', '\\', $extra);

        $namespace = $this->laravel['modules']->config('namespace');

        $namespace .= '\\' . $module->getStudlyName();

        $namespace .= '\\' . $this->getDefaultNamespace();

        $namespace .= '\\' . $extra;

        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }
}
