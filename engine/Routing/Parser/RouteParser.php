<?php

namespace Engine\Routing\Parser;

class RouteParser
{
    public const STMT_GROUP = 1;
    public const STMT_ROUTE = 2;

    /**
     * @var \Engine\Routing\Parser\RouteLexer
     */
    protected $lexer;
    /**
     * @var \Engine\Routing\Parser\RouteToken
     */
    protected $tok;

    public function init(RouteLexer $lexer)
    {
        $this->lexer = $lexer;
    }

    protected function next(): RouteToken
    {
        return $this->tok = $this->lexer->getNextToken();
    }

    protected function expect($tokenKind)
    {
        if ($this->tok->getKind() != $tokenKind) {
            throw new ParseException(
                $this->lexer->getFilename(),
                $this->tok->getLine(), $this->tok->getColumn(),
                "expected ".RouteToken::tokenKindToName($tokenKind).", given: ".$this->tok
            );
        }
    }

    protected function eat($tokenKind)
    {
        $this->expect($tokenKind);
        $this->next();
    }

    public function parseGroup(): array
    {
        $this->expect(RouteToken::T_STRING);
        $prefix = $this->tok->getLexeme();
        $this->next();
        $this->eat(RouteToken::T_LPAREN);

        $stmts = $this->parseStmtList(RouteToken::T_RPAREN);
        $this->next();

        $this->eat(RouteToken::T_SEMI);

        return ['kind' => self::STMT_GROUP,
                'prefix' => $prefix,
                'stmts' => $stmts];
    }

    protected function isMethod(string $name): bool
    {
        return in_array($name, ['GET', 'POST', 'ANY', 'PUT', 'DELETE', 'PATCH', 'HEAD']);
    }

    public function parseMethodList(): array
    {
        $methods = [];
        while (true) {
            if ($this->isMethod($this->tok->getLexeme())) {
                $methods[] = $this->tok->getLexeme();
            } else {
                throw new ParseException(
                    $this->lexer->getFilename(),
                    $this->tok->getLine(), $this->tok->getColumn(),
                    "undefined method: `".$this->tok->getLexeme()."`"
                );
            }
            if ($this->next()->getKind() != RouteToken::T_OR) {
                break;
            } else {
                $this->next();
            }
        }
        return $methods;
    }

    public function parseRouteParams(): array
    {
        $params = [];
        while ($this->tok->getKind() == RouteToken::T_IDENT) {
            $key = $this->tok->getLexeme();
            $this->next();
            $this->expect(RouteToken::T_STRING);
            $value = $this->tok->getLexeme();
            $params[$key] = $value;
            $this->next();
        }
        return $params;
    }

    public function parseRoute(): array
    {
        $methods = $this->parseMethodList();

        $this->expect(RouteToken::T_STRING);
        $uri = $this->tok->getLexeme();
        $this->next();

        $params = $this->parseRouteParams();

        $this->eat(RouteToken::T_SEMI);

        return ['kind' => self::STMT_ROUTE,
                'methods' => $methods,
                'uri' => $uri,
                'params' => $params];
    }

    public function parseStmt(): array
    {
        $this->expect(RouteToken::T_IDENT);
        if ($this->tok->getLexeme() == 'GROUP') {
            $this->next();
            return $this->parseGroup();
        }
        return $this->parseRoute();
    }

    public function parseStmtList(int $endKind): array
    {
        $result = [];
        while ($this->tok->getKind() != $endKind) {
            $result[] = $this->parseStmt();
        }
        return $result;
    }

    public function parse(): array
    {
        $this->next();
        return $this->parseStmtList(RouteToken::T_EOF);
    }
}