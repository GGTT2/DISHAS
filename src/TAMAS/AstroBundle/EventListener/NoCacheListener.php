<?php
namespace TAMAS\AstroBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * This method prevents the page to reload forms with the cached information.
 * E.g : a user clicks on "submit" the form, and then clicks on the "left arrow"
 * (gets back to the previous page). We prevent the user to get back to the form filled with the previous data. 
 * Instead we generate a new form. 
 * This prevent the user to get mistaken and create a new entity while trying to update the data that was just persisted in the database.
 * 
 * */
class NoCacheListener
{
    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $route = $event->getRequest()->attributes->get('_route');

        if (!in_array($route, [ //TODO : change this list to regex : either "Add" or "Edit"
            
        'tamas_astro_adminAddMathematicalParameter',
            'tamas_astro_adminAddOriginalText',
            'tamas_astro_adminAddParameterSet',
            'tamas_astro_adminAddSecondarySource',
            'tamas_astro_adminAddEditedText',
            'tamas_astro_adminAddPlace',
            'tamas_astro_adminAddHistoricalActor',
            'tamas_astro_adminAddPrimarySource',
            'tamas_astro_adminAddLibrary',
            'tamas_astro_adminAddWork',
            'tamas_astro_adminAddHistorian',
            'tamas_astro_adminAddJournal',
            'tamas_astro_adminAddTableContent',
            'tamas_astro_adminAddTableContentAjax',
            'tamas_astro_adminAddMathematicalParameter',
            'tamas_astro_adminAddPythonScript',
            'tamas_astro_adminAddPDFFile',
            'tamas_astro_adminAddFormulaDefinition',
            /*And the edit route*/
            'tamas_astro_adminEditPlace',
            'tamas_astro_adminEditJournal',
            'tamas_astro_adminEditHistorian',
            'tamas_astro_adminEditLibrary',
            'tamas_astro_adminEditOriginalText',
            'tamas_astro_adminEditParameterSet',
            'tamas_astro_adminEditSecondarySource',
            'tamas_astro_adminEditPrimarySource',
            'tamas_astro_adminEditHistoricalActor',
            'tamas_astro_adminEditWork',
            'tamas_astro_adminEditEditedText',
            'tamas_astro_adminEditMathematicalParameter',
            'tamas_astro_adminEditTableContent',
            'tamas_astro_adminEditUserInterfaceText',
            'tamas_astro_adminEditPDFFile',
            'tamas_astro_adminEditFormulaDefinition'
        ])) {
            return;
        }

        $response = $event->getResponse();
        $response->setMaxAge(0);
        $response->headers->addCacheControlDirective('must-revalidate');
        $response->headers->addCacheControlDirective('no-store');
        $response->headers->addCacheControlDirective('no-cache');
    }
}
