<?php

namespace Engine\Result\View;

use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Routing\Router;

class HtmlView extends View
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

    public function with(array $vars): HtmlView
    {
        $this->vars = array_merge($this->vars, $vars);

        return $this;
    }

    public function withHtmlTitle(string $title): HtmlView
    {
        $this->title = $title;

        return $this;
    }

    public static function by(string $relTemplatePath): HtmlView
    {
        return new HtmlView("actions/$relTemplatePath");
    }

    public static function global(string $globTemplatePath): HtmlView
    {
        return new HtmlView($globTemplatePath);
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

        \Engine\Router::setActionTemplate("$this->path.tpl");
    }

    public function fetch(): string
    {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        $local = $viewer->GetLocalViewer();
        foreach ($this->vars as $key => $value) {
            $local->Assign($key, $value);
        }

        return $local->Fetch("$this->path.tpl");
    }
}