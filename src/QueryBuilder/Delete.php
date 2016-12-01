<?php

namespace zsql\QueryBuilder;

use zsql\Query\Delete as DeleteInterface;

/**
 * Class Delete
 * Delete query builder
 * @package zsql\QueryBuilder
 */
class Delete extends ExtendedQuery implements DeleteInterface
{
    /**
     * Assemble parts
     *
     * @return void
     */
    protected function assemble()
    {
        $this->push('DELETE FROM');
        $this->pushTable();
        $this->pushWhere();
        $this->pushOrder();
        $this->pushLimit();

        $this->query = join(' ', $this->parts);
    }

    /**
     * Alias for {@link Query::table()}
     *
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        $this->table($table);
        return $this;
    }
}
