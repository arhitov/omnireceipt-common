<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities;

use Omnireceipt\Common\Entities\Receipt as BaseReceipt;

/**
 * @method string getUuid()
 * @method string|null getUuidOrNull()
 * @method self setUuid(string $value)
 *
 * @method string getState()
 * @method self setState(string $value)
 */
class Receipt extends BaseReceipt
{
    public function getId(): string
    {
        return $this->getUuid();
    }

    public function isPending(): bool
    {
        return 'pending' === $this->getState();
    }

    public function isSuccessful(): bool
    {
        return 'succeeded' === $this->getState();
    }

    public function isCancelled(): bool
    {
        return 'canceled' === $this->getState();
    }
}
