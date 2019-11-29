<?php

namespace App\Controller;

use App\RemoteApi\GoogleCalendarApi;
use App\Repository\PartyRepository;
use App\Repository\UserRepository;
use App\Service\Schedule;
use Carbon\Carbon;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{

    private $userRepository;
    private $partyRepository;
    private $calendarApi;

    public function __construct(UserRepository $userRepository, PartyRepository $partyRepository, GoogleCalendarApi $calendarApi)
    {
        $this->userRepository = $userRepository;
        $this->partyRepository = $partyRepository;
        $this->calendarApi = $calendarApi;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('schedule.html.twig', [
            'year' => date('Y'),
            'users' => $this->userRepository->all(),
            'calendar' => $this->calendarApi->getHolidaysDateAndName(),
            'parties' => $this->partyRepository->getParties(),
        ]);
    }

    /**
     * @Route("schedule", name="schedule", methods={"GET"})
     * @throws Exception
     */
    public function schedule(Request $request): Response
    {
       $userId = $request->query->get('userId');
       $startDate = Carbon::create($request->query->get('startDate'));
       $endDate = Carbon::create($request->query->get('endDate'));
       $user = $this->userRepository->findUser($userId);

       $schedule = new Schedule($user, $this->partyRepository, $this->calendarApi);
       $scheduleUser = $schedule->getSchedule($startDate, $endDate);

        return $this->render('schedule.html.twig', [
            'json' => json_encode($scheduleUser, JSON_PRETTY_PRINT),
            'user' => $user,
            'vacations' => $user->getVacation()->toArray(),
            'parties' => $this->partyRepository->getParties(),
            'calendar' => $this->calendarApi->getHolidaysDateAndName(),
            'year' => date('Y'),
        ]);
    }
}