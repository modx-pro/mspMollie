<?php

$xpdo_meta_map = array (
  'xPDOObject' =>
  array (
    0 => 'mspMollieOrder',
  ),
);

if (!class_exists('msOrder')) {
    $this->loadClass('msOrder');
}
$this->map['msOrder']['composites']['MollieOrder'] = array(
    'class' => 'mspMollieOrder',
    'local' => 'id',
    'foreign' => 'local_id',
    'cardinality' => 'one',
    'owner' => 'local',
);