<?php

/**
 * @file
 * This code is never called, it's just information about to use the hooks.
 */

/**
 * Alter the metadata sent to Wirecard.
 */
function hook_wirecard_wpp_payload_alter(&$payload, $order) {
  $payload['payment']['order-number'] = $order->order_id;
}
