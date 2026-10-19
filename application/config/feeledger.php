<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| FeeLedger settings
*/

// Shared secret for the payment gateway webhook (X-FeeLedger-Signature header)
$config['webhook_secret'] = getenv('WEBHOOK_SECRET') ?: 'whsec_local_7f3a9c';

// Late fee, see docs/FEE_POLICY.md
$config['late_fee_per_week'] = 50;

// TODO: move to a settings table
$config['gst_rate'] = 0.18;

$config['centre_name'] = 'Vidya Coaching Centre, Pune';
