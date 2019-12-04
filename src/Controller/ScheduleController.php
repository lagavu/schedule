<?php

namespace App\Controller;

use App\RemoteApi\GoogleCalendarApi;
use App\Repository\PartyRepository;
use App\Repository\UserRepository;
use App\Service\ScheduleFactory;
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
            'users' => $this->userRepository->findAll(),
            'calendar' => $this->calendarApi->getHolidaysDateAndName(),
            'parties' => $this->partyRepository->findAll(),
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
       $user = $this->userRepository->findById($userId);

       $scheduler = new ScheduleFactory($this->partyRepository, $this->calendarApi);
       $userSchedule = $scheduler->createUserSchedule($user, $startDate, $endDate);

       return $this->render('schedule.html.twig', [
           'json' => json_encode($userSchedule, JSON_PRETTY_PRINT),
           'user' => $user,
           'vacations' => $user->getVacation()->toArray(),
           'parties' => $this->partyRepository->findAll(),
           'calendar' => $this->calendarApi->getHolidaysDateAndName(),
           'year' => date('Y'),
       ]);
    }
}
