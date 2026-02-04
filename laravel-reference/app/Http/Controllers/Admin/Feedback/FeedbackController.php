<?php

namespace App\Http\Controllers\Admin\Feedback;

use App\Http\Controllers\Controller;
use App\Mail\FeedbackReplyMail;
use App\Models\Feedback\Feedback;
use App\Models\Feedback\Message;
use Mail;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::query()->get();

        return view('admin.feedback.index', ['feedbacks' => $feedbacks]);
    }

    public function show(Feedback $feedback)
    {
        $feedback->load('messages');
        $feedback->update([
            'viewed_at' => now(),
        ]);

        return view('admin.feedback.show', ['feedback' => $feedback]);
    }

    public function delete(Feedback $feedback)
    {
        $feedback->delete();

        return redirect()->route('admin.feedback.index');
    }

    public function set_unread(Feedback $feedback)
    {
        $feedback->update([
            'viewed_at' => null,
        ]);

        return redirect()->route('admin.feedback.index');
    }

    public function reply(Feedback $feedback)
    {
        request()->validate([
            'message' => ['required'],
        ]);

        $feedback->messages()->save(new Message([
            'value' => nl2br((string) request()->input('message')),
            'type' => Message::TYPE_ADMIN,
        ]));

        Mail::to($feedback->email)->send(new FeedbackReplyMail(request()->input('message')));

        return redirect()->route('admin.feedback.index');
    }

    public function filter_view()
    {
        return view('admin.feedback.filter.index');
    }

    public function filter()
    {
        $filters = array_filter(request()->all());
        $query = Feedback::query();

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'company':
                    $query = $query->where('company', 'LIKE', "%{$value}%");
                    break;
                case 'expert':
                    $query = $query->where('expert', 'LIKE', "%{$value}%");
                    break;
                case 'email':
                    $query = $query->where('email', 'LIKE', "%{$value}%");
                    break;
                case 'date':
                    if (! empty($value[0]) && ! empty($value[1])) {
                        $query = $query->whereBetween('date', [$value[0], $value[1]]);
                    }
                    break;
            }
        }

        $feedbacks = $query->get();

        return view('admin.feedback.filter.result', ['feedbacks' => $feedbacks]);
    }
}
