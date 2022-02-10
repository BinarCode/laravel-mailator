<?php

namespace Binarcode\LaravelMailator\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MailTemplate
 *
 * @property string $name
 * @property string $description
 * @property int $mail_template_id
 * @property-read MailTemplate $mailTemplate
 *
 * @package App\Models
 */
class MailTemplatePlaceholder extends Model
{
    protected $fillable = [
        'name',
        'description',
        'mail_template_id',
    ];

    public function mailTemplate()
    {
        return $this->belongsTo(
            config('mailator.templates.template_model') ?? MailTemplate::class,
            'mail_template_id'
        );
    }
}
