<?php

namespace App\Filter\Api;

use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;

final class OrSearchFilter extends SearchFilter
{
    /**
     * addWhereByStrategy
     *
     * @param  mixed $strategy
     * @param  mixed $queryBuilder
     * @param  mixed $queryNameGenerator
     * @param  mixed $alias
     * @param  mixed $field
     * @param  mixed $value
     * @param  mixed $caseSensitive
     * @return void
     */
    protected function addWhereByStrategy(string $strategy, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $alias, string $field, $value, bool $caseSensitive)
    {
        $wrapCase = $this->createWrapCase($caseSensitive);
        $valueParameter = $queryNameGenerator->generateParameterName($field);

        switch ($strategy) {
            case null:
            case self::STRATEGY_EXACT:
                $queryBuilder
                    ->orWhere(sprintf($wrapCase('%s.%s') . ' = ' . $wrapCase(':%s'), $alias, $field, $valueParameter))
                    ->setParameter($valueParameter, $value);
                break;
            case self::STRATEGY_PARTIAL:
                $queryBuilder
                    ->orWhere(sprintf($wrapCase('%s.%s') . ' LIKE ' . $wrapCase('CONCAT(\'%%\', :%s, \'%%\')'), $alias, $field, $valueParameter))
                    ->setParameter($valueParameter, $value);
                break;
            case self::STRATEGY_START:
                $queryBuilder
                    ->orWhere(sprintf($wrapCase('%s.%s') . ' LIKE ' . $wrapCase('CONCAT(:%s, \'%%\')'), $alias, $field, $valueParameter))
                    ->setParameter($valueParameter, $value);
                break;
            case self::STRATEGY_END:
                $queryBuilder
                    ->orWhere(sprintf($wrapCase('%s.%s') . ' LIKE ' . $wrapCase('CONCAT(\'%%\', :%s)'), $alias, $field, $valueParameter))
                    ->setParameter($valueParameter, $value);
                break;
            case self::STRATEGY_WORD_START:
                $queryBuilder
                    ->orWhere(sprintf($wrapCase('%1$s.%2$s') . ' LIKE ' . $wrapCase('CONCAT(:%3$s, \'%%\')') . ' OR ' . $wrapCase('%1$s.%2$s') . ' LIKE ' . $wrapCase('CONCAT(\'%% \', :%3$s, \'%%\')'), $alias, $field, $valueParameter))
                    ->setParameter($valueParameter, $value);
                break;
            default:
                throw new InvalidArgumentException(sprintf('strategy %s does not exist.', $strategy));
        }
    }
}
