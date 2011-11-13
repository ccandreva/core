<?php

namespace SystemPlugin\Symfony2Forms\Renderer;

use Symfony\Component\Form\FormView;
use SystemPlugin\Symfony2Forms\RendererInterface;
use SystemPlugin\Symfony2Forms\FormRenderer;

/**
 *
 */
class FieldRows implements RendererInterface
{
    public function getName()
    {
        return 'field_rows';
    }
    
    public function render(FormView $form, $variables, FormRenderer $renderer)
    {
        $html = $renderer->renderErrors(array('form' => $form));
        
        foreach ($form as $child) {
            $html .= $renderer->renderRow(array('form' => $child));
        }
        
        return $html;
    }
}