<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\SellerInterface;
use Omnireceipt\Common\Supports\ParametersTrait;

/**
 * @method string getName() // Наименование организации поставщика
 * @method string getNameOrNull() // Наименование организации поставщика
 * @method self setName(string $value)
 */
abstract class Seller implements SellerInterface
{
    use ParametersTrait;

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }

    public function __construct(
        array $parameters = []
    ) {
        $this->initialize($parameters);
    }
}
