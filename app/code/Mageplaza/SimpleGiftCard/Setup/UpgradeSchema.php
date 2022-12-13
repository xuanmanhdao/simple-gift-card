<?php

namespace Mageplaza\SimpleGiftCard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
//        die(124);
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            if ($installer->tableExists('mageplaza_simple_gift_card')) {
                $installer->getConnection()->changeColumn(
                    $installer->getTable('mageplaza_simple_gift_card'),
                    'code',
                    'code',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Gift Card Code'
                    ]
                );
            }
        }

        $installer->endSetup();
    }
}
