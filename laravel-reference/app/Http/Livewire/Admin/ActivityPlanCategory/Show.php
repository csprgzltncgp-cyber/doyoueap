<?php

namespace App\Http\Livewire\Admin\ActivityPlanCategory;

use App\Models\ActivityPlanCategory;
use App\Models\ActivityPlanCategoryField;
use Illuminate\Support\Collection;
use Livewire\Component;

class Show extends Component
{
    public $opened = false;

    public ActivityPlanCategory $activity_plan_category;

    /** @var Collection<ActivityPlanCategoryField> */
    public Collection $activity_plan_category_fields;

    protected $listeners = ['add_new_field', 'edit_field', 'delete_field'];

    public function mount(ActivityPlanCategory $activity_plan_category): void
    {
        $this->activity_plan_category = $activity_plan_category;
        $this->activity_plan_category_fields = ActivityPlanCategoryField::query()
            ->where('activity_plan_category_id', $activity_plan_category->id)
            ->orderBy('id')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.activity-plan-category.show');
    }

    public function toggle_opened(): void
    {
        $this->opened = ! $this->opened;
    }

    public function add_new_field(string $name, string $type, int $activity_plan_category_id, bool $is_highlighted): void
    {
        // Need to check if the event is for the current activity plan category
        if ($activity_plan_category_id != $this->activity_plan_category->id) {
            return;
        }

        $activity_plan_category_field = ActivityPlanCategoryField::query()->create([
            'activity_plan_category_id' => $this->activity_plan_category->id,
            'name' => $name,
            'type' => $type,
            'is_highlighted' => $is_highlighted,
        ]);

        // Update the collection
        $this->activity_plan_category_fields->push($activity_plan_category_field);
    }

    public function edit_field(int $activity_plan_category_field_id, int $activity_plan_category_id, string $name, bool $is_highlighted): void
    {
        // Need to check if the event is for the current activity plan category
        if ($activity_plan_category_id != $this->activity_plan_category->id) {
            return;
        }

        $activity_plan_category_field = ActivityPlanCategoryField::query()->find($activity_plan_category_field_id);

        $activity_plan_category_field->update([
            'name' => $name,
            'is_highlighted' => $is_highlighted,
        ]);

        // Update the collection
        $this->activity_plan_category_fields = $this->activity_plan_category_fields
            ->reject(fn (ActivityPlanCategoryField $item): bool => $item->id == $activity_plan_category_field_id)
            ->push($activity_plan_category_field)
            ->sortBy('id');
    }

    public function delete_field(int $activity_plan_category_field_id, int $activity_plan_category_id): void
    {
        // Need to check if the event is for the current activity plan category
        if ($activity_plan_category_id != $this->activity_plan_category->id) {
            return;
        }

        ActivityPlanCategoryField::query()
            ->where('id', $activity_plan_category_field_id)->delete();

        // Update the collection
        $this->activity_plan_category_fields = $this->activity_plan_category_fields
            ->reject(fn (ActivityPlanCategoryField $item): bool => $item->id == $activity_plan_category_field_id)
            ->sortBy('id');
    }
}
