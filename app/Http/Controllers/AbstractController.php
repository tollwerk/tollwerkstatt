<?php
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Klaus Fiedler <klaus@tollwerk.de>, tollwerk® GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace App\Http\Controllers;

use App\Http\Repositories\CalendarRepository;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

abstract class AbstractController extends Controller
{
    /** @var  \TYPO3Fluid\Fluid\View\TemplateView */
    protected $view;

    /**
     * @var \App\Http\Repositories\CalendarRepository CalendarRepository
     */
    protected $calendarRepository;

    /** @var \Illuminate\Http\Request */
    protected $request = null;

    /**
     * @var string
     */
    protected $controllerName = '';

    /**
     * AbstractController constructor.
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     */
    public function __construct(Request $request)
    {
        // Start user session
        session_start();


        $this->calendarRepository = new CalendarRepository();
        $this->request = $request;

        /**
         * Initializing the View: rendering in Fluid takes place through a View instance
         * which contains a RenderingContext that in turn contains things like definitions
         * of template paths, instances of variable containers and similar.
         */
        $this->view = new \TYPO3Fluid\Fluid\View\TemplateView();
        $this->view->getViewHelperResolver()->addNamespace('tw','App\\ViewHelpers');

        /**
         * Get and set controller name for fluid rendering
         * Inheriting controllers must overide protected $controlerName for setting of the fluid rendering context.
         * Only then fluid can find the template files for Controllers in the corresponding subdirectories of the $templateRootPath
         */
        if (!strlen($this->controllerName)) {
            throw new \Exception('No controller name set for fluid rendering context! Please override protected $controllerName inside your controller!');
        }

        if($this->controllerName !== null){
            $this->view->getRenderingContext()->setControllerName($this->controllerName);
        }



        /**
         * Set fluid root paths
         */
        // TODO: Make root paths extensible via .env configuration or something
        $viewsDirectory = (dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
        $templateRootPath = $viewsDirectory . DIRECTORY_SEPARATOR . 'Templates';
        $layoutRootPath = $viewsDirectory . DIRECTORY_SEPARATOR . 'Layouts';
        $partialRootPath = $viewsDirectory . DIRECTORY_SEPARATOR . 'Partials';
        $paths = $this->view->getTemplatePaths();
        $paths->setTemplateRootPaths([$templateRootPath]);
        $paths->setLayoutRootPaths([$layoutRootPath]);
        $paths->setPartialRootPaths([$partialRootPath]);

        /** Enable fluid template caching */
        $cacheDirectory = $viewsDirectory . DIRECTORY_SEPARATOR . 'Cache';
        $fluidCacheDirectory = isset($fluidCacheDirectory) ?: $cacheDirectory;
        if ($fluidCacheDirectory) {
            if (intval(env('FLUID_CACHE')) > 0) {
                $this->view->setCache(new \TYPO3Fluid\Fluid\Core\Cache\SimpleFileCache($fluidCacheDirectory));
            }
        }
    }

    /**
     * @param $actionName
     * @param $controllerName
     * @param array $arguments
     * @return string
     */
    public function renderStandaloneView($actionName, $controllerName, $arguments = []){
        $view = $this->view;
        $view->getRenderingContext()->setControllerName($controllerName);
        $view->assignMultiple($arguments);
        return $view->render($actionName);
    }

}