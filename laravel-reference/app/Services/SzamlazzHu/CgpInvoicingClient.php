<?php

namespace App\Services\SzamlazzHu;

use zoparga\SzamlazzHu\Client\Client;

class CgpInvoicingClient extends Client
{
    public function validationRulesForSavingInvoice()
    {
        $rules = parent::validationRulesForSavingInvoice();

        unset($rules['orderNumber']);

        $rules['orderNumber'] = ['nullable', 'alpha_dash'];

        return $rules;
    }
}
