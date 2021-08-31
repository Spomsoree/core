<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\QueryInterface;
use Doctrine\ORM\Query\Parameter;

/**
 *  Directive a raw output of the generated query.
 */
class PrintQueryDirective
{
    public const NAME = 'printquery';

    public function __invoke(QueryInterface $query): void
    {
        $dql = $query->getQueryBuilder()->getDQL();
        $parameters = $query->getQueryBuilder()->getParameters();

        $dql = strtr($dql, [
            'INNER' => "\nINNER",
            'WHERE' => "\nWHERE",
            'ORDER' => "\nORDER",
        ]);

        /** @var Parameter $parameter */
        foreach ($parameters as $parameter) {
            $dql = str_replace(
                ':' . $parameter->getName() . '',
                '<b title="' . self::stringifyValue($parameter->getValue()) . '">:' . $parameter->getName() . '</b>',
                $dql
            );
        }

        $output = sprintf('<code>%s</code>', $dql);

        $output .= '<ul>';

        foreach ($parameters as $parameter) {
            $output .= sprintf(
                '<li><code>%s</code>: <code>%s</code></li>',
                $parameter->getName(),
                self::stringifyValue($parameter->getValue())
            );
        }

        $output .= '</ul>';

        echo $output;
    }

    private static function stringifyValue($value): string
    {
        return is_iterable($value) ? sprintf('[%s]', implode(',', $value)) : $value;
    }
}
