<?php

namespace App\Traits\EapOnline;

use Exception;

trait CategoryTrait
{
    public function setCategories($categories): void
    {
        if (! $this->getAttribute('eap_categories')) {
            throw new Exception('Property eap_categories does not exist');
        }

        if ($this->eap_categories->count()) {
            $this->eap_categories()->detach();
        }
        if (! empty($categories)) {
            foreach ($categories as $category) {
                $this->eap_categories()->attach((int) $category);
            }
        }
    }
}
