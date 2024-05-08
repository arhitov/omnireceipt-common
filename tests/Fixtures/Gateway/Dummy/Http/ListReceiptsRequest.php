<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\Common\Http\Request\AbstractListReceiptRequest;
use Omnireceipt\Common\Http\Response\AbstractResponse;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Fixtures\Helper;

/**
 * @method string getDateFrom()
 * @method self setDateFrom(string $value)
 * @method string getDateTo()
 * @method self setDateTo(string $value)
 */
class ListReceiptsRequest extends AbstractListReceiptRequest
{
    public static function rules(): array
    {
        return [
            'date_from' => ['required', 'string'],
            'date_to' => ['required', 'string'],
        ];
    }

    public function getData(): array
    {
        return [
            'date_from' => $this->getDateFrom(),
            'date_to' => $this->getDateTo(),
        ];
    }

    public function sendData(array $data): AbstractResponse
    {
        $options = [
            'date_from' => $data['date_from'],
            'date_to' => $data['date_to'],
        ];

        $collection = (new ArrayCollection(Helper::getFixtureAsArray('receipts')))
                      ->filter(static function (array $item) use ($options) {
                          if (! empty($options['date_from']) && ! Carbon::parse($item['date'])->gte(Carbon::parse($options['date_from']))) {
                              return false;
                          }
                          if (! empty($options['date_to']) && ! Carbon::parse($item['date'])->lte(Carbon::parse($options['date_to']))) {
                              return false;
                          }
                          return true;
                      });

        return $collection->count()
            ? new ListReceiptsResponse($this, $collection->toArray(), 200)
            : new ListReceiptsResponse($this, null, 404);
    }
}
