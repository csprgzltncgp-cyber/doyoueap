<?php

namespace App\Enums;

enum DashboardDataType: string
{
    case TYPE_CGP_DATA = 'cgp_data';
    case TYPE_CONTRACT_HOLDER_DATA = 'contract_holder_data';
    case TYPE_COUNTRY_CASE_DATA = 'country_case_data';
    case TYPE_COUNTRY_INVOICE_DATA = 'country_invoice_data';
    case TYPE_AFFILIATE_DATA = 'affiliate_data';
    case TYPE_LIFEWORKS_DATA = 'lifeworks_data';
}
