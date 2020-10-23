<?php

namespace Engine\View;

use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Router;

class View
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

    public function render()
    {
        if ($this->title != null) LS::Make(ModuleViewer::class)->AddHtmlTitle($this->title);
        Router::setActionTemplate("actions/$this->path.tpl");
    }

    public static function by(string $templatePath): View
    {
        return new View($templatePath);
    }
}