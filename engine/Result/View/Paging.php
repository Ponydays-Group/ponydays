<?php

namespace Engine\Result\View;

use App\Modules\ModuleTools;

class Paging
{
    /**
     * @var array
     */
    protected $pagesLeft;
    /**
     * @var array
     */
    protected $pagesRight;
    /**
     * @var int
     */
    protected $elementCount;
    /**
     * @var int
     */
    protected $pageCount;
    /**
     * @var int
     */
    protected $currentPage;
    /**
     * @var int
     */
    protected $nextPage;
    /**
     * @var int
     */
    protected $prevPage;
    /**
     * @var string
     */
    protected $baseUri;
    /**
     * @var string
     */
    protected $getParams;

    public function __construct(
        array $pagesLeft,
        array $pagesRight,
        int $elementCount,
        int $pageCount,
        int $currentPage,
        int $nextPage,
        int $prevPage,
        string $baseUri,
        string $getParams
    )
    {
        $this->pagesLeft = $pagesLeft;
        $this->pagesRight = $pagesRight;
        $this->elementCount = $elementCount;
        $this->pageCount = $pageCount;
        $this->currentPage = $currentPage;
        $this->nextPage = $nextPage;
        $this->prevPage = $prevPage;
        $this->baseUri = $baseUri;
        $this->getParams = $getParams;
    }

    public static function make(
        int $numberOfElements,
        int $currentPage,
        int $elementsPerPage,
        int $numberOfButtons,
        string $baseUri,
        array $httpGetParams = []
    ): self
    {
        if ($numberOfElements == 0) throw new \InvalidArgumentException('Number of elements cannot be 0');

        $pageCount = ceil($numberOfElements / $elementsPerPage);
        if (!preg_match("/^[1-9]\d*$/i", $currentPage)) {
            $currentPage = 1;
        }

        if ($currentPage > $pageCount) {
            $currentPage = $pageCount;
        }

        $pagesLeft = [];
        $temp = max(1, $currentPage - $numberOfButtons);
        for ($i = $temp; $i < $currentPage; $i++) {
            $pagesLeft[] = $i;
        }

        $pagesRight = [];
        for (
            $i = $currentPage + 1;
            $i <= $currentPage + $numberOfButtons and $i <= $pageCount;
            $i++
        ) {
            $pagesRight[] = $i;
        }

        $nextPage = $currentPage < $pageCount ? $currentPage + 1 : false;
        $prevPage = $currentPage > 1 ? $currentPage - 1 : false;

        $getParams = '';
        if (count($httpGetParams)) {
            $getParams = '?' . http_build_query($httpGetParams, '', '&');
        }
        return new Paging(
            $pagesLeft,
            $pagesRight,
            $numberOfElements,
            $pageCount,
            $currentPage,
            $nextPage,
            $prevPage,
            rtrim(ModuleTools::Urlspecialchars($baseUri), '/'),
            $getParams
        );
    }

    public function setupHtml(HtmlView $view): self
    {
        if ($this->currentPage == 1) {
            $view->meta()->setHtmlCanonical($this->baseUri . '/' . $this->getParams);
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'aPagesLeft'   => $this->pagesLeft,
            'aPagesRight'  => $this->pagesRight,
            'iCount'       => $this->elementCount,
            'iCountPage'   => $this->pageCount,
            'iCurrentPage' => $this->currentPage,
            'iNextPage'    => $this->nextPage,
            'iPrevPage'    => $this->prevPage,
            'sBaseUrl'     => $this->baseUri,
            'sGetParams'   => $this->getParams
        ];
    }
}