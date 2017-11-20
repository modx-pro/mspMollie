<?php
$xpdo_meta_map['mspMollieOrder']= array (
  'package' => 'mspmollie',
  'version' => '1.1',
  'table' => 'ms2_mollie_orders',
  'extends' => 'xPDOObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'local_id' => NULL,
    'remote_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'local_id' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'int',
      'precision' => '10',
      'null' => false,
      'index' => 'pk',
    ),
    'remote_id' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'text',
      'precision' => '255',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'local_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'remote_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
