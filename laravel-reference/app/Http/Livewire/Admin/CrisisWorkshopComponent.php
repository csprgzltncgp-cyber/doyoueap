<?php

namespace App\Http\Livewire\Admin;

use App\Models\CrisisCase;
use App\Models\OrgData;
use App\Models\WorkshopCase;
use Illuminate\Support\Str;
use Livewire\Component;

class CrisisWorkshopComponent extends Component
{
    public $company;

    public $country;

    public $opened = false;

    public $model;

    public $type;

    public $model_type;

    protected $listeners = [
        'closeAll' => 'closeAll',
    ];

    protected function rules(): array
    {
        return [
            'model.activity_id' => ['required'],
            'model.free' => ['boolean'],
            'model.valuta' => ['required_unless:model.free'],
            'model.'.$this->type.'_price' => ['required_unless:model.free'],
        ];
    }

    public function mount(): void
    {
        if ($this->type == 'workshop') {
            $this->model_type = $this->model->free && $this->model->gift ? 'gift' : ($this->model->free ? 'free' : null);
        }
    }

    public function render()
    {
        return view('livewire.admin.crisis-workshop-component');
    }

    public function updated($propertyName): void
    {
        if (Str::contains($propertyName, 'activity_id')) {
            $activity_id = $this->model->getOriginal('activity_id');
            if ($this->type == 'workshop') {
                WorkshopCase::query()->where('activity_id', $activity_id)->update([
                    'activity_id' => $this->model->activity_id,
                ]);
            }

            if ($this->type == 'crisis') {
                CrisisCase::query()->where('activity_id', $activity_id)->update([
                    'activity_id' => $this->model->activity_id,
                ]);
            }
        }

        if (Str::contains($propertyName, 'model_type')) {
            switch ($this->model_type) {
                case 'free':
                    $this->model->free = true;
                    $this->model->gift = false;
                    $this->model->valuta = null;
                    $this->model->{$this->type.'_price'} = null;

                    break;
                case 'gift':
                    $this->model->gift = true;
                    $this->model->free = true;
                    $this->model->valuta = null;
                    $this->model->{$this->type.'_price'} = null;

                    break;
                default:
                    $this->model->free = false;
                    $this->model->gift = false;
                    break;
            }
        }

        $this->model->save();
    }

    public function toggleOpen(): void
    {
        $this->opened = ! $this->opened;
    }

    public function closeAll(): void
    {
        $this->opened = false;
    }

    public function deleteModel()
    {
        if (! $this->model->active) {
            return $this->emit('modelCannotDeleted');
        }

        $this->model->delete();

        if ($this->type == 'workshop') {
            OrgData::query()
                ->where('company_id', $this->company->id)
                ->where('country_id', $this->country->id)
                ->decrement('workshops_number');
        } else {
            OrgData::query()
                ->where('company_id', $this->company->id)
                ->where('country_id', $this->country->id)
                ->decrement('crisis_number');
        }

        return $this->emit('refreshModels');
    }
}
