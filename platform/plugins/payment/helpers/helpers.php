<?php

use Botble\Ecommerce\Models\Currency;
use Botble\Payment\Models\Payment;
use Botble\Stripe\Supports\StripeHelper;

if (!function_exists('convert_stripe_amount_from_api')) {
    /**
     * @param int|float $amount
     * @param Currency|null $currency
     * @return float|int
     */
    function convert_stripe_amount_from_api($amount, ?Currency $currency)
    {
        return $amount / StripeHelper::getStripeCurrencyMultiplier($currency);
    }
}

if (!function_exists('get_payment_setting')) {
    /**
     * @param string $key
     * @param null $type
     * @param null $default
     * @return string|null
     */
    function get_payment_setting(string $key, $type = null, $default = null): ?string
    {
        if (!empty($type)) {
            $key = 'payment_' . $type . '_' . $key;
        } else {
            $key = 'payment_' . $key;
        }

        return setting($key, $default);
    }
}

if (!function_exists('get_payment_is_support_refund_online')) {
    /**
     * @param Payment $payment
     * @return false|string
     */
    function get_payment_is_support_refund_online(Payment $payment)
    {
        $paymentService = $payment->payment_channel->getServiceClass();

        if ($paymentService && class_exists($paymentService)) {
            if (method_exists($paymentService, 'getSupportRefundOnline')) {
                try {
                    $isSupportRefund = (new $paymentService())->getSupportRefundOnline();

                    return $isSupportRefund ? $paymentService : false;
                } catch (Exception $exception) {
                    return false;
                }
            }
        }

        return false;
    }
}
