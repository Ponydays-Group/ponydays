<?php

namespace Engine\Result\View;

use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Result\Traits\IWithVariables;
use Engine\Result\Traits\Messages;
use Engine\Result\Traits\WithVariablesArray;
use Engine\Routing\Router;

class HtmlView extends View implements IWithVariables
{
    use WithVariablesArray;
    use Messages;
    /**
     * @var string
     */
    private $path;
    /**
     * @var string|array
     */
    private $title = null;
    /**
     * @var \Engine\Result\View\Paging
     */
    private $paging = null;
    /**
     * @var \Engine\Result\View\HtmlMeta
     */
    private $meta = null;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->meta = new HtmlMeta();
    }

    public function withHtmlTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function meta(): HtmlMeta
    {
        return $this->meta;
    }

    public function paging(Paging $paging): self
    {
        $paging->setupHtml($this);

        return $this;
    }

    public static function by(string $relTemplatePath): self
    {
        return new HtmlView("actions/$relTemplatePath");
    }

    public static function global(string $globTemplatePath): self
    {
        return new HtmlView($globTemplatePath);
    }

    public function render(Router $router)
    {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        $this->setup($viewer);

        \Engine\Router::setActionTemplate("$this->path.tpl");
    }

    public function fetch(): string
    {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $local = $viewer->GetLocalViewer();

        $this->setup($local);

        return $local->Fetch("$this->path.tpl");
    }

    protected function setup(ModuleViewer $viewer)
    {
        if ($this->title != null) {
            if (is_string($this->title)) {
                $viewer->AddHtmlTitle($this->title);
            } else if (is_array($this->title)) {
                foreach ($this->title as $title) {
                    $viewer->AddHtmlTitle($title);
                }
            }
        }
        foreach ($this->getVariables() as $key => $value) {
            $viewer->Assign($key, $value);
        }

        foreach ($this->meta()->getVars() as $key => $value) {
            $viewer->Assign($key, $value);
        }

        $viewer->Assign('aMsgError', $this->getErrorMsgs());
        $viewer->Assign('aMsgNotice', $this->getNoticeMsgs());
    }
}