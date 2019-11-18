<?php

namespace App\Controller;

use App\RemoteService\GoogleCalendar;
use App\Service\Schedule\Schedule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param GoogleCalendar $calendar
     * @return Response
     */
    public function index(GoogleCalendar $calendar): Response
    {
        return $this->render('schedule/index.html.twig', [
            'year' => date('Y'),
            'calendar' => $calendar->holiday(),
        ]);
    }

    /**
     * @Route("schedule", name="schedule", methods={"GET"})
     * @param Request $request
     * @param Schedule $schedule
     * @param GoogleCalendar $calendar
     * @return Response
     */
    public function schedule(Request $request, Schedule $schedule, GoogleCalendar $calendar): Response
    {
       $userId = $request->query->get('userId');
       $startDate = $request->query->get('startDate');
       $endDate = $request->query->get('endDate');

       $JsonResponse = $schedule->getJson($userId, $startDate, $endDate);

        return $this->render('schedule/index.html.twig', [
            'json' => $JsonResponse,
            'user' => $schedule->getUser($userId),
            'holidays' => $schedule->getHolidays($userId),
            'parties' => $schedule->getParties(),
            'year' => date('Y'),
            'calendar' => $calendar->holiday(),
        ]);
    }
}