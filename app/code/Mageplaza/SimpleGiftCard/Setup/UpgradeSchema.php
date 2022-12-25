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

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            if (!$installer->tableExists('mageplaza_giftcard_history')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('mageplaza_giftcard_history')
                )
                    ->addColumn(
                        'history_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary' => true,
                            'unsigned' => true,
                        ],
                        'Gift Card History ID'
                    )
                    ->addColumn(
                        'giftcard_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'nullable' => false,
                            'unsigned' => true,
                        ],
                        'Gift Card ID Foreign Key'
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'mageplaza_giftcard_history',
                            'giftcard_id',
                            'mageplaza_simple_gift_card',
                            'giftcard_id'
                        ),
                        'giftcard_id',
                        $installer->getTable('mageplaza_simple_gift_card'),
                        'giftcard_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->addColumn(
                        'customer_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'nullable' => false,
                            'unsigned' => true,
                        ],
                        'Customer ID Foreign Key'
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'mageplaza_giftcard_history',
                            'customer_id',
                            'customer_entity',
                            'entity_id'
                        ),
                        'customer_id',
                        $installer->getTable('customer_entity'),
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->addColumn(
                        'amount',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        ['length' => 12, 4],
                        ['nullable' => false, 'default' => 0],
                        'Amount Changed'
                    )
                    ->addColumn(
                        'action',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['nullable' => false, 'default' => null],
                        'Action Taken'
                    )
                    ->addColumn(
                        'action_time',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                        'Action Taken Time'
                    );
                $installer->getConnection()->createTable($table);
            }

            if ($installer->tableExists('customer_entity')) {
                $installer->getConnection()->addColumn(
                    $installer->getTable('customer_entity'),
                    'giftcard_balance',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12, 4',
                        'nullable' => false,
                        'default' => 0,
                        'comment' => 'Gift Card Balance of Customer'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            if ($installer->tableExists('quote')) {
                $installer->getConnection()->addColumn(
                    $installer->getTable('quote'),
                    'coupon_code_custom',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => '255',
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'Coupon Code Custom'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $setup->getConnection()->renameTable($setup->getTable('mageplaza_simple_gift_card'), $setup->getTable('giftcard_code'));
        }

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $setup->getConnection()->renameTable($setup->getTable('mageplaza_giftcard_history'), $setup->getTable('giftcard_history'));
        }

        $installer->endSetup();
    }
}
