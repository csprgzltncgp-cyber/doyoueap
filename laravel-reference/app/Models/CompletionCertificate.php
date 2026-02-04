<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CompletionCertificate
 *
 * @property int $id
 * @property int $direct_invoice_id
 * @property string|null $filename
 * @property bool $with_header
 * @property string|null $path
 * @property Carbon|null $printed_at
 * @property Carbon|null $sent_at
 * @property string|null $uploaded_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DirectInvoice|null $direct_invoice
 *
 * @method static Builder|CompletionCertificate newModelQuery()
 * @method static Builder|CompletionCertificate newQuery()
 * @method static Builder|CompletionCertificate query()
 * @method static Builder|CompletionCertificate whereCreatedAt($value)
 * @method static Builder|CompletionCertificate whereDirectInvoiceId($value)
 * @method static Builder|CompletionCertificate whereFilename($value)
 * @method static Builder|CompletionCertificate whereId($value)
 * @method static Builder|CompletionCertificate wherePath($value)
 * @method static Builder|CompletionCertificate wherePrintedAt($value)
 * @method static Builder|CompletionCertificate whereSentAt($value)
 * @method static Builder|CompletionCertificate whereUpdatedAt($value)
 * @method static Builder|CompletionCertificate whereUploadedAt($value)
 * @method static Builder|CompletionCertificate whereWithHeader($value)
 *
 * @mixin \Eloquent
 */
class CompletionCertificate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'with_header' => 'boolean',
        'printed_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function direct_invoice(): BelongsTo
    {
        return $this->belongsTo(DirectInvoice::class);
    }
}
