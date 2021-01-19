<?php

namespace Engine\Result\Traits;

trait Messages
{
    protected $errorMsgs = [];
    protected $noticeMsgs = [];

    public function msgError(string $msg, ?string $title = null, bool $single = false): self
    {
        if ($single) $this->clearErrorMsgs();
        $this->errorMsgs[] = ['msg' => $msg, 'title' => $title];

        return $this;
    }

    public function msgNotice(string $msg, ?string $title = null, bool $single = false): self
    {
        if ($single) $this->clearNoticeMsgs();
        $this->errorMsgs[] = ['msg' => $msg, 'title' => $title];

        return $this;
    }

    public function clearErrorMsgs(): self
    {
        $this->errorMsgs = [];

        return $this;
    }

    public function clearNoticeMsgs(): self
    {
        $this->noticeMsgs = [];

        return $this;
    }

    public function getErrorMsgs(): array
    {
        return $this->errorMsgs;
    }

    public function getNoticeMsgs(): array
    {
        return $this->noticeMsgs;
    }
}