<?php

namespace App\Controller;

use App\RemoteService\GoogleCalendar;
use App\Repository\PartyRepository;
use App\Repository\UserRepository;
use App\Service\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    /**
     * @Route("/", name="index")
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
     * @throws Exception
     */
    public function schedule(
        Request $request, PartyRepository $partyRepository,
        UserRepository $userRepository, GoogleCalendar $calendar): Response
    {
       $userId = $request->query->get('userId');
       $startDate = Carbon::create($request->query->get('startDate'));
       $endDate = Carbon::create($request->query->get('endDate'));


       $user = $userRepository->findUser($userId);

       $schedule = new Schedule($user, $partyRepository, $calendar);
       $scheduleUser = $schedule->getSchedule($startDate, $endDate);

        return $this->render('schedule.html.twig', [
            'json' => $scheduleUser,
            'user' => $user,
            'vacations' => $user->getVacation()->toArray(),
            'parties' => $partyRepository->getParties(),
            'calendar' => $calendar->getHolidaysDateAndName(),
            'year' => date('Y'),
        ]);
    }
}