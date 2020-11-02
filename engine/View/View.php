<?php

namespace Engine\View;

use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Routing\Result;
use Engine\Routing\Router;

class View extends Result
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $title = null;
    /**
     * @var array
     */
    private $vars = [];

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function with(array $vars): View
    {
        $this->vars = array_merge($this->vars, $vars);
        return $this;
    }

    public function withHtmlTitle(string $title): View
    {
        $this->title = $title;
        return $this;
    }

    public static function by(string $templatePath): View
    {
        return new View($templatePath);
    }

    public function _handle(Router $router)
    {
        /**
         * @var ModuleViewer $viewer
         */
        $viewer = LS::Make(ModuleViewer::class);

        if ($this->title != null) $viewer->AddHtmlTitle($this->title);

        foreach ($this->vars as $key => $value) {
            $viewer->Assign($key, $value);
        }

        \Engine\Router::setActionTemplate("actions/$this->path.tpl");
    }
}