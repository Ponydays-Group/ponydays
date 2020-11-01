<?php

namespace Engine\Routing\Parser;

use FastRoute\RouteCollector;

class RouteWalker
{
    public function walkStmt(array $stmt, RouteCollector $r)
    {
        switch ($stmt['kind']) {
            case RouteParser::STMT_GROUP:
                $r->addGroup(
                    $stmt['prefix'],
                    function () use ($stmt, $r) {
                        $this->walkList($stmt['stmts'], $r);
                    }
                );
                break;
            case RouteParser::STMT_ROUTE:
                $methods = array_merge($stmt['methods'], ['OPTIONS']);
                if (in_array('ANY', $methods)) {
                    $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
                }
                $params = [];
                $params['params'] = $stmt['params'];
                $params['options'] = $methods;
                $r->addRoute($methods, $stmt['uri'], $params);
                break;

            default: break;
        }
    }

    public function walkList(array $result, RouteCollector $r)
    {
        foreach ($result as $stmt) {
            $this->walkStmt($stmt, $r);
        }
    }
}