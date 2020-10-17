<?php

namespace Engine\Routing\Parser;

class RouteToken
{
    public const T_EOF    = 0;
    public const T_IDENT  = 1;
    public const T_OR     = 2;
    public const T_SEMI   = 3;
    public const T_LPAREN = 4;
    public const T_RPAREN = 5;
    public const T_STRING = 6;

    public static function tokenKindToName(int $kind): string
    {
        switch ($kind) {
            case self::T_EOF: return 'T_EOF';
            case self::T_IDENT: return 'T_IDENT';
            case self::T_OR: return 'T_OR';
            case self::T_SEMI: return 'T_SEMI';
            case self::T_LPAREN: return 'T_LPAREN';
            case self::T_RPAREN: return 'T_RPAREN';
            case self::T_STRING: return 'T_STRING';

            default: return "T_UNDEF";
        }
    }

    /**
     * @var int
     */
    protected $kind;
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
    protected $lexeme;

    public function __construct(int $kind, int $line, int $column, string $lexeme)
    {
        $this->kind = $kind;
        $this->line = $line;
        $this->column = $column;
        $this->lexeme = $lexeme;
    }

    public function getKind(): int
    {
        return $this->kind;
    }

    public function ofKind(int $kind): bool
    {
        return $this->kind == $kind;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    public function getLexeme(): string
    {
        return $this->lexeme;
    }

    public function __toString(): string
    {
        return self::tokenKindToName($this->kind)."($this->lexeme)";
    }
}