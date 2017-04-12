<?php
namespace Leantony\Database\Extra;

use Symfony\Component\HttpKernel\Exception\HttpException;

trait BulkOperations
{
    /**
     * Perform a bulk insert
     * Note that this will skip all model events as it uses the DB query builder
     *
     * @param array $data
     * @return bool
     */
    public function createMany(array $data)
    {
        return $this->getModel()->insert($data);
    }

    /**
     * Update many records at once
     *
     * @param array $ids
     * @param array $data
     * @return bool
     */
    public function updateMany(array $ids, array $data)
    {
        $status = $this->getModel()->whereIn($this->getModel()->getKeyName(), $ids)->update($data);
        if (!$status) {
            throw new HttpException(500, 'Unable to bulk update.', null, []);
        }

        return $status;
    }

    /**
     * Delete many records
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMany(array $ids)
    {
        $status = $this->getModel()->whereIn($this->getModel()->getKeyName(), $ids)->delete();
        if (!$status) {
            throw new HttpException(500, 'Unable to bulk delete.', null, []);
        }

        return $status;
    }
}