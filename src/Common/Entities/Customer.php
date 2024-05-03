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

use Omnireceipt\Common\Contracts\CustomerInterface;
use Omnireceipt\Common\Supports\ParametersTrait;

/**
 * @method string getId()
 * @method string getIdOrNull()
 * @method self setId(string $value)
 *
 * @method string getName()
 * @method string getNameOrNull()
 * @method self setName(string $value)
 *
 * @method string getPhone()
 * @method string getPhoneOrNull()
 * @method self setPhone(string $value)
 *
 * @method string getEmail()
 * @method string getEmailOrNull()
 * @method self setEmail(string $value)
 */
abstract class Customer implements CustomerInterface
{
    use ParametersTrait;

    public static function rules(): array
    {
        return [
            'id'    => ['nullable', 'string'],
            'name'  => ['required', 'string'],
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'string'],
        ];
    }

    public function __construct(
        array $parameters = [],
    ) {
        $this->initialize($parameters);
    }
}