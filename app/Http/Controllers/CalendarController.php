<?php

namespace App\Http\Controllers;

use App\Http\Repositories\CalendarRepository;
use App\Utility\Event;
use Faker\Provider\DateTime;

class CalendarController extends AbstractController
{
    protected $controllerName = 'Calendar';

    /**
     * @return Response
     */
    public function showAction()
    {
        // We could just do "return $this->view->render .." as well,
        // but by returning a full response object, we can modify headers etc.
        return response($this->view->render('show'));
    }

    public function jsonFeedAction()
    {
        $start = null;
        if($this->request->input('start')){
            $start = explode('T',$this->request->input('start'));
            $start = is_array($start) ? \DateTime::createFromFormat('Y-m-d', $start[0])->modify('midnight') : null;
       }

       $end = null;
        if($this->request->input('end')){
            $end = explode('T',$this->request->input('end'));
            $end = is_array($end) ? \DateTime::createFromFormat('Y-m-d', $end[0])->modify('midnight') : null;
       }

        return response()->json(Event::serialize($this->calendarRepository->findByDate($start, $end)));
    }
}