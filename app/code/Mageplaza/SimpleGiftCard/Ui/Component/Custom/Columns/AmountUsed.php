<?php

namespace Mageplaza\SimpleGiftCard\Ui\Component\Custom\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class AmountUsed extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
//    const NAME = 'column.qty';
    const NAME = 'amount_used';

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = "$".round($item[$fieldName] ,0);
                }
            }
        }
        return $dataSource;
    }
}
