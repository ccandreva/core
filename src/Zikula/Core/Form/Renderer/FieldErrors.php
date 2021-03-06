<?php

namespace Zikula\Core\Form\Renderer;

use Symfony\Component\Form\FormView;
use Zikula\Core\Form\RendererInterface;
use Zikula\Core\Form\FormRenderer;

/**
 *
 */
class FieldErrors implements RendererInterface
{
    public function getName()
    {
        return 'field_errors';
    }

    protected function getDivClassName()
    {
        return 'z-formnote';
    }

    public function render(FormView $form, $variables, FormRenderer $renderer)
    {
        $html = '';

        if ($variables['errors'] && !$this->getRoot($form)->get('omit_errors', false)) {
            $html .= '<div class="' . $this->getDivClassName() . ' z-errormsg"><ul>';

            foreach ($variables['errors'] as $error) {
                $html .= '<li>' . $error->getMessageTemplate() . '</li>'; //TODO: $error->getMessageParameters()
            }

            $html .= '</ul></div>';
        }

        return $html;
    }

    private function getRoot(FormView $form)
    {
        while($form->getParent() != null) {
            $form = $form->getParent();
        }

        return $form;
    }
}
