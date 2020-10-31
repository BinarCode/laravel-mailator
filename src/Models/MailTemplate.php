<?php

namespace Binarcode\LaravelMailator\Models;

use Binarcode\LaravelMailator\Models\Concerns\WithUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Class MailTemplate
 * @property string name
 * @property string subject
 * @property string from_email
 * @property string from_name
 * @property string html
 * @property string email_html
 * @property string webview_html
 * @property string mailable_class
 * @property string webvimailable_classew_html
 * @package App\Models
 */
class MailTemplate extends Model implements MailTemplateable
{
    use WithUuid;

    protected $fillable = [
        'name',
        'from_email',
        'from_name',
        'subject',
        'html',
        'email_html',
        'webview_html',
        'mailable_class',
    ];

    public function placeholders()
    {
        return $this->hasMany(
            config('mailator.templates.placeholder_model') ?? MailTemplatePlaceholder::class,
            'mail_template_id'
        );
    }

    public function htmlWithInlinedCss(): string
    {
        return (new CssToInlineStyles())->convert($this->html ?? '');
    }

    public function getMailable(): Mailable
    {
        $mailableClass = $this->mailable_class;

        return app($mailableClass);
    }

    public function scopeWithMailable($query, string $mailable)
    {
        $query->where('mailable_class', $mailable);
    }

    public function preparePlaceholders(): Collection
    {
        return $this->placeholders->map->only('name');
    }

    public function getContent(): string
    {
        return $this->html;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getFromEmail(): ?string
    {
        return $this->from_email;
    }

    public function getFromName(): ?string
    {
        return $this->from_name;
    }
}
