<?php

/** @var modX $modx */
define('MODX_API_MODE', true);
/** @noinspection PhpIncludeInspection */
require dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
$modx->getService('error', 'error.modError');

$modx->error->message = null;
/** @var miniShop2 $miniShop2 */
/** @var mspMollieOrder $record */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->loadCustomClasses('payment');
if (!class_exists('Mollie')) {
    exit('Error: could not load payment class "Mollie".');
} elseif (empty($_REQUEST['id'])) {
    exit('Error: the order id is not specified.');
} elseif (!$record = $modx->getObject('mspMollieOrder', ['remote_id' => $_REQUEST['id']])) {
    exit('Error: could not load specified order.');
}

/** @var msOrder $order */
if ($order = $record->getOne('Order')) {
    /** @var Mollie $handler */
    $handler = new Mollie($order);
    if ($payment = $handler->Mollie->payments->get($record->remote_id)) {
        if ($payment->isPaid()) {
            $response = $handler->receive($order, 2);
        } elseif ($payment->isCancelled()) {
            $response = $handler->receive($order, 4);
        } else {
            $response = 'Error: could not process order.';
        }
        exit($response !== true ? $response : 'Ok');
    }
}
exit('Error: unknown');

