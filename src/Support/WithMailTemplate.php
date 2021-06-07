<?php

namespace Binarcode\LaravelMailator\Support;

use Binarcode\LaravelMailator\Actions\PersonalizeMailAction;
use Binarcode\LaravelMailator\Models\MailTemplateable;

trait WithMailTemplate
{
    public $template;

    public $slot;

    protected $layout;

    /**
     * Set the mailator template.
     *
     * @param MailTemplateable $template
     * @return $this
     */
    public function template(MailTemplateable $template)
    {
        $this->ensureValidView();

        $this->template = $template;

        /** * @var PersonalizeMailAction $replacerAction */
        $replacerAction = app(PersonalizeMailAction::class);

        // replace placehlders
        $this->slot = $replacerAction->execute(
            $template->getContent(),
            $this->getTemplate(),
            $this->getReplacers(),
        );

        $this->subject($template->getSubject());

        if ($from = $template->getFromEmail()) {
            $this->from(
                $from,
                $template->getFromName()
            );
        }

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
        return $this->layout ?? config('mailator.templates.template_layout', 'laravel-mailator::laravel');
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

    protected function ensureValidView()
    {
        if (! $this->markdown) {
            $this->markdown($this->getLayout());
        }

        return $this;
    }

    protected function ensureValidSlot()
    {
        if (! $this->slot) {
            $this->slot = $this->template->getContent() ?? '';
        }

        return $this;
    }
}
