<?php

namespace Engine\Result\View;

use App\Modules\ModuleTools;

class HtmlMeta
{
    /**
     * @var string
     */
    protected $htmlKeywords;
    /**
     * @var string
     */
    protected $htmlDescription;
    /**
     * @var array
     */
    protected $htmlRssAlternate = null;
    /**
     * @var string|null
     */
    protected $htmlCanonical;

    public function __construct() {}

    /**
     * @param string $htmlKeywords
     */
    public function setHtmlKeywords(string $htmlKeywords)
    {
        $this->htmlKeywords;
    }

    /**
     * @param string $htmlDescription
     */
    public function setHtmlDescription(string $htmlDescription)
    {
        $this->htmlDescription = $htmlDescription;
    }

    /**
     * @param string $url
     * @param string $title
     */
    public function setHtmlRssAlternate(string $url, string $title)
    {
        $this->htmlRssAlternate['title'] = htmlspecialchars($title);
        $this->htmlRssAlternate['url'] = htmlspecialchars($url);
    }

    /**
     * @param string|null $htmlCanonical
     */
    public function setHtmlCanonical(?string $htmlCanonical)
    {
        $this->htmlCanonical = $htmlCanonical;
    }

    /**
     * @return string
     */
    public function getHtmlKeywords(): string
    {
        return $this->htmlKeywords;
    }

    /**
     * @return string
     */
    public function getHtmlDescription(): string
    {
        return $this->htmlDescription;
    }

    /**
     * @return array
     */
    public function getHtmlRssAlternate(): array
    {
        return $this->htmlRssAlternate;
    }

    /**
     * @return string|null
     */
    public function getHtmlCanonical(): ?string
    {
        return $this->htmlCanonical;
    }

    public function getVars(): array
    {
        return [
            'sHtmlKeywords' => htmlspecialchars($this->htmlKeywords),
            'sHtmlDescription' => htmlspecialchars($this->htmlDescription),
            'aHtmlRssAlternate' => $this->htmlRssAlternate,
            'sHtmlCanonical' => ModuleTools::Urlspecialchars($this->htmlCanonical)
        ];
    }
}