<?php

namespace App\Http\Livewire\Admin\DirectInvoicing\Comment;

use Livewire\Component;

class Show extends Component
{
    public $invoiceComment;

    protected $rules = [
        'invoiceComment.value' => ['required', 'string'],
    ];

    public function mount($invoiceComment): void
    {
        $this->invoiceComment = $invoiceComment;
    }

    public function render()
    {
        return view('livewire.admin.direct-invoicing.comment.show');
    }

    public function updated(): void
    {
        $this->validate();
        $this->invoiceComment->save();
    }

    public function delete(): void
    {
        $this->invoiceComment->delete();
        $this->emitUp('invoiceCommentDeleted');
    }
}
