<?php

namespace App\Controller;

use App\RemoteService\GoogleCalendar;
use App\Repository\HolidayRepository;
use App\Repository\PartyRepository;
use App\Repository\UserRepository;
use App\Service\Schedule\Schedule;
use Doctrine\ORM\EntityNotFoundException;
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
     * @Route("/schedule22, name="shedule")     *
     */
/*
    public function shedule(
        User $user,
        Request $request,
        HolidayFetcher $holiday,
        PartyFetcher $party,
        UserFetcher $userFetcher,
        GoogleСalendar $сalendar,
        UserRepository $userRepository,
        HolidayRepository $holidayRepository,
        PartyRepository $parties
        ): Response
    {
        dd($user);
        $query = $request->query->get('form');
        $id = $query['userId'];
        $user = $userRepository->findUser($id);
        $getHoliday = $holidayRepository->all($id);

        $party = new Date($party);
        $vacation = new Vacation($id, $holiday);
        $date = new Query ($query['userId'], $query['start_date'], $query['end_date'], $vacation, $сalendar, $party);
        $json = new Json($party, $user, $parties, $userFetcher);
        $getJson = $json->getJSON($date->shedule());

        $form = $this->createForm(Form::class);

        return $this->render('schedule/index.html.twig', [
            'json' => $getJson,
            'user' => $user,
            'holidays' => $getHoliday,
            'parties' => $parties->all(),
            'year' => date('Y'),
            'calendar' => $сalendar->holiday(),
            'form' => $form->createView()

        ]);
    }
*/

    /**
     * @Route("schedule", name="schedule", methods={"GET"})
     * @param Request $request
     * @param Schedule $schedule
     * @return Response
     */
    public function schedule(Request $request, Schedule $schedule): Response
    {
       $userId = $request->query->get('userId');
       $startDate = $request->query->get('startDate');
       $endDate = $request->query->get('endDate');

       $user = $schedule->getUser($userId);
      // $holidays = $holidayRepository->all($userId);
      // $parties = $partyRepository->all();

      //  $scheduleUser = new Schedule($user);
        $schedule->getSchedule();

        $JsonResponse = $schedule->getJson($userId, $startDate, $endDate);

        dd($userId, $startDate, $endDate, $user, $schedule->getSchedule(), $JsonResponse);
    }

}






// http://127.0.0.1:8080/schedule?userId=5&startDate=2019-01-22&startDate=2019-01-25
// * @Route("/schedule", name="first_route", defaults={"type" = null})
// <li class="breadcrumb-item"><a href="{{ path('work.projects.project.show', {'id': project.id}) }}">{{ project.name }}</a></li>