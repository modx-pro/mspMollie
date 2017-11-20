<?php

if (!class_exists('msPaymentInterface')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(__FILE__))) . '/minishop2/model/minishop2/mspaymenthandler.class.php';
}

class Mollie extends msPaymentHandler implements msPaymentInterface
{
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config;
    /** @var Mollie_API_Client $Mollie */
    public $Mollie;


    /**
     * @param xPDOObject $object
     * @param array $config
     */
    function __construct(xPDOObject $object, $config = [])
    {
        parent::__construct($object, $config);

        if (!class_exists('Mollie_API_Client')) {
            require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
        }
        $this->Mollie = new Mollie_API_Client;
        try {
            $this->Mollie->setApiKey($this->modx->getOption('mspmollie_api_key'));
        } catch (Mollie_API_Exception $e) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, '[mspMollie] Error on load API: ' . $e->getMessage());
        }

        $this->config = array_merge([
            'paymentUrl' => MODX_ASSETS_URL . 'components/mspmollie/payment.php',
            'successId' => $this->modx->getOption('mspmollie_success_id', null, $this->modx->getOption('site_start'), true),
        ], $config);

        //$this->modx->addPackage('mspmollie', MODX_CORE_PATH. 'components/mspmollie/model/');
    }


    /**
     * @param msOrder $order
     *
     * @return array|string
     */
    public function send(msOrder $order)
    {
        $response = $this->success('', [
            'redirect' => $this->getPaymentLink($order),
        ]);

        return $response;
    }


    /**
     * @param msOrder $order
     *
     * @return string
     */
    public function getPaymentLink(msOrder $order)
    {
        $payment = null;
        /** @var mspMollieOrder $record */
        $record = $this->modx->getObject('mspMollieOrder', ['local_id' => $order->id]);
        if (!empty($record)) {
            $payment = $this->Mollie->payments->get($record->remote_id);
        } else {
            try {
                $payment = $this->Mollie->payments->create([
                    'amount' => $order->get('cost'),
                    'description' => 'Order #' . $order->get('num'),
                    'redirectUrl' => $this->modx->makeUrl($this->config['successId'], $order->get('context'), ['msorder' => $order->id], 'full'),
                    'webhookUrl' => rtrim($this->modx->getOption('site_url'), '/') . $this->config['paymentUrl'],
                    'metadata' => [
                        'order_id' => $order->id,
                    ],
                ]);
                $record = $this->modx->newObject('mspMollieOrder');
                $record->fromArray([
                    'local_id' => $order->id,
                    'remote_id' => $payment->id,
                ], '', true);
                $record->save();
            } catch (Mollie_API_Exception $e) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, '[mspMollie] Error on create payment with API: ' . $e->getMessage());
            }
        }

        return !empty($payment)
            ? $payment->getPaymentUrl()
            : '';
    }


    /**
     * @param msOrder $order
     * @param int $status
     *
     * @return bool
     */
    public function receive(msOrder $order, $status = 2)
    {
        if ($order->get('status') == $status) {
            return true;
        }
        /* @var miniShop2 $miniShop2 */
        $miniShop2 = $this->modx->getService('miniShop2');
        $ctx = $order->get('context');
        if ($ctx != 'web') {
            $this->modx->switchContext($ctx);
        }

        return $miniShop2->changeOrderStatus($order->id, $status);
    }

}