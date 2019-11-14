<?php

namespace App\Controller\Shedule;

use App\Model\Holiday\Entity\Holiday\HolidayRepository;
use App\Model\Party\Entity\Party\Date;
use App\Model\Party\Entity\Party\PartyRepository;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Entity\User\Vacation;
use App\ReadModel\Holiday\HolidayFetcher;
use App\ReadModel\Party\PartyFetcher;
use App\ReadModel\User\Query\Form;
use App\ReadModel\User\Query\Json;
use App\ReadModel\User\Query\Query;
use App\ReadModel\User\UserFetcher;
use App\Service\GoogleСalendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SheduleController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function index(GoogleСalendar $сalendar): Response
    {
        $form = $this->createForm(Form::class);

        return $this->render('shedule/index.html.twig', [
            'year' => date('Y'),
            'calendar' => $сalendar->holiday(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/shedule", name="shedule")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function shedule(
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

        return $this->render('shedule/index.html.twig', [
            'json' => $getJson,
            'user' => $user,
            'holidays' => $getHoliday,
            'parties' => $parties->all(),
            'year' => date('Y'),
            'calendar' => $сalendar->holiday(),
            'form' => $form->createView()

        ]);
    }
}
