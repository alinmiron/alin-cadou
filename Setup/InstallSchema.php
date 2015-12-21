<?php

namespace Alin\Cadou\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
                ->newTable($installer->getTable('alin_cadou'))
                ->addColumn('cadou_id', Table::TYPE_INTEGER, null, ['identity'=>true, 'unsigned' => true, 'nullable'=>false, 'primary'=>true], 'Cadou ID')
                ->addColumn('quote_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable'=>false], 'Quote Id')
                ->addColumn('store_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable'=>false], 'Store Id')
                ->addColumn('product_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable'=>false], 'Product Id')
                ->addColumn('product_type', Table::TYPE_TEXT, 30, ['nullable'=>false], 'Product Type')
                ->addColumn('cart_item_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable'=>false], 'Cart item Id')
                ->addColumn('child_cart_item_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable'=>true,'default'=>null], 'Child Cart item Id')
                ->addColumn('fullname', Table::TYPE_TEXT, 100, ['nullable' => false], 'Full Name')
                ->addColumn('email', Table::TYPE_TEXT, 150, ['nullable' => false], 'email')
                ->addColumn('birthdate', Table::TYPE_DATE, null, ['nullable' => false], 'Birthdate')
                ->addColumn('address', Table::TYPE_TEXT, 250, ['nullable' =>false],'Address')
                ->addColumn('notified', Table::TYPE_BOOLEAN, null, ['default'=>false, 'nullable'=>false], 'e-mail was sent?')
                ->addIndex('search',['fullname','email','address'], ['type'=>\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT])
                /*
                ->addIndex('email',['email'], ['type'=>\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT])
                ->addIndex('fullname',['fullname'], ['type'=>\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT])
                ->addIndex('address',['address'], ['type'=>\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT])
                */
                //->addForeignKey($installer->getFkName('alin_cadou','product_id', 'quote_item', 'product_id'), 'product_id', 'quote_item', 'product_id', Table::ACTION_CASCADE)
                ->setComment('Alin Cadou table');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();

    }
}

