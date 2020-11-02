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

    public function __construct(string $path)
    {
        $this->path = $path;
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
        if ($this->title != null) LS::Make(ModuleViewer::class)->AddHtmlTitle($this->title);
        \Engine\Router::setActionTemplate("actions/$this->path.tpl");
    }
}