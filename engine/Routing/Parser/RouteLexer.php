<?php

namespace Engine\Routing\Parser;

class RouteLexer
{
    /**
     * @var int
     */
    protected $line;
    /**
     * @var int
     */
    protected $column;
    /**
     * @var string
     */
    protected $source;
    /**
     * @var string
     */
    protected $filename;
    /**
     * @var int
     */
    protected $pos;
    /**
     * @var string
     */
    protected $curch;
    /**
     * @var int
     */
    protected $tokLine;
    /**
     * @var int
     */
    protected $tokColumn;

    public function init(string $source, string $filename)
    {
        $this->source = $source;
        $this->filename = $filename;
        $this->column = 0;
        $this->line = 1;
        $this->pos = -1;

        $this->next();
    }

    private function next(): string
    {
        if (strlen($this->source) <= $this->pos + 1) {
            return $this->curch = "\0";
        }
        if ($this->curch == "\n") {
            $this->line++;
            $this->column = 1;
        } else {
            $this->column++;
        }
        $this->pos++;
        return $this->curch = $this->source[$this->pos];
    }

    private function fix()
    {
        $this->tokLine = $this->line;
        $this->tokColumn = $this->column;
    }

    private function tok(int $kind, string $lexeme): RouteToken
    {
        return new RouteToken($kind, $this->tokLine, $this->tokColumn, $lexeme);
    }

    private function skipWhitespaces()
    {
        while (ctype_space($this->curch)) $this->next();
    }

    private function skipLine()
    {
        while ($this->curch != "\n" && $this->curch != "\0") $this->next();
        $this->next();
    }

    private function lexIdentifier(): RouteToken
    {
        $ident = $this->curch;
        while (ctype_alnum($this->next())) {
            $ident .= $this->curch;
        }
        return $this->tok(RouteToken::T_IDENT, $ident);
    }

    private function lexString(): RouteToken
    {
        $str = '';
        while ($this->curch != "'") {
            if ($this->curch == "\0")
                throw new ParseException($this->filename, $this->tokLine, $this->tokColumn, "missing termination ' character");
            $str .= $this->curch;
            $this->next();
        }
        $this->next();
        return $this->tok(RouteToken::T_STRING, $str);
    }

    public function getNextToken(): RouteToken
    {
        while (true) {
            $this->skipWhitespaces();

            $this->fix();

            if (ctype_alpha($this->curch)) return $this->lexIdentifier();

            switch ($this->curch) {
                case "'": $this->next(); return $this->lexString();
                case '|': $this->next(); return $this->tok(RouteToken::T_OR, '|');
                case '#': $this->skipLine(); continue 2;
                case '(': $this->next(); return $this->tok(RouteToken::T_LPAREN, '(');
                case ')': $this->next(); return $this->tok(RouteToken::T_RPAREN, ')');
                case ';': $this->next(); return $this->tok(RouteToken::T_SEMI, ';');

                case "\0": return $this->tok(RouteToken::T_EOF, "\0");

                default: throw new ParseException($this->filename, $this->line, $this->column, "unrecognized character: `$this->curch`");
            }
        }
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}