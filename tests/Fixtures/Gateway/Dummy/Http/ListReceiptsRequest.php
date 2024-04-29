<?php

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\Common\Http\Request\AbstractListReceiptRequest;
use Omnireceipt\Common\Http\Response\AbstractResponse;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Helper;

/**
 * @method string getDateFrom()
 * @method self setDateFrom(string $value)
 * @method string getDateTo()
 * @method self setDateTo(string $value)
 * @method bool getDeleted()
 * @method self setDeleted(bool $value)
 */
class ListReceiptsRequest extends AbstractListReceiptRequest
{
    const RULES = [
        'date_from' => ['required', 'string'],
        'date_to' => ['required', 'string'],
        'deleted' => ['required', 'bool'],
    ];

    public function getData(): array
    {
        return [
            'date_from' => $this->getDateFrom(),
            'date_to' => $this->getDateTo(),
            'deleted' => $this->getDeleted(),
        ];
    }

    public function sendData(array $data): AbstractResponse
    {
        $options = [
            'date_from' => $data['date_from'],
            'date_to' => $data['date_to'],
            'deleted' => $data['deleted'],
        ];

        $collection = (new ArrayCollection(Helper::getFixtureAsArray('receipts')))
                      ->filter(static function (array $item) use ($options) {
                          if (! empty($options['date_from']) && ! Carbon::parse($item['doc_date'])->gte(Carbon::parse($options['date_from']))) {
                              return false;
                          }
                          if (! empty($options['date_to']) && ! Carbon::parse($item['doc_date'])->lte(Carbon::parse($options['date_to']))) {
                              return false;
                          }
                          if (! empty($options['deleted']) && false) {
                              // @TODO PASS..
                              return false;
                          }
                          return true;
                      });

        return $collection->count()
            ? new ListReceiptsResponse($this, $collection->toArray(), 200)
            : new ListReceiptsResponse($this, null, 404);
    }
}
