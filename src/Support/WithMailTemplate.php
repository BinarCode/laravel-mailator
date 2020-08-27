<?php

namespace Binarcode\LaravelMailator\Support;

use Binarcode\LaravelMailator\Actions\PersonalizeMailAction;
use Binarcode\LaravelMailator\Exceptions\InvalidTemplateException;
use Binarcode\LaravelMailator\Models\MailTemplateable;

trait WithMailTemplate
{
    public $template;

    protected $layout;

    /**
     * Set the mailator template.
     *
     * @param MailTemplateable $template
     * @return $this
     */
    public function template(MailTemplateable $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Set the mailator template layout.
     *
     * @param string $layout
     * @return $this
     */
    public function useLayout(string $layout)
    {
        $this->layout = $layout;

        return $this;
    }

    public function getLayout()
    {
        return $this->layout ?? config('mailator.templates.layout', 'laravel-mailator::mails.template_layout');
    }

    public function getTemplate(): ?MailTemplateable
    {
        return $this->template;
    }

    /**
     * Return an array of instances of Replacer interface.
     * @return array
     */
    abstract public function getReplacers(): array;

    public function render()
    {
        $this->ensureValidView();

        // this will call the build method
        $rendered = parent::render();

        if (! $this->getTemplate()) {
            InvalidTemplateException::throw(get_class($this->getTemplate()));
        }

        /** * @var PersonalizeMailAction $replacerAction */
        $replacerAction = app(PersonalizeMailAction::class);

        return $replacerAction->execute(
            $rendered,
            $this->getTemplate(),
            $this->getReplacers(),
        );
    }

    private function ensureValidView()
    {
        if (! $this->markdown) {
            $this->markdown($this->getLayout());
        }

        return $this;
    }
}
