<?php

namespace App\Controller\Shedule;

use App\Model\Party\Entity\Party\Date;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Vacation;
use App\ReadModel\Holiday\Filter\Filter;
use App\ReadModel\Holiday\HolidayFetcher;
use App\ReadModel\Party\PartyFetcher;
use App\ReadModel\User\Query\Json;
use App\ReadModel\User\Query\Query;
use App\ReadModel\User\UserFetcher;
use App\Service\GoogleСalendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SheduleController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function index()
    {
        return $this->render('shedule/index.html.twig', [
            'controller_name' => 'SheduleController',
        ]);
    }
/*
    public function show(Project $project, Request $request, CalendarFetcher $calendar): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        $now = new \DateTimeImmutable();

        $query = Query\Query::fromDate($now)->forProject($project->getId()->getValue());

        $form = $this->createForm(Query\Form::class, $query);
        $form->handleRequest($request);

        $result = $calendar->byMonth($query);

        return $this->render('shedule/index.html.twig', [
            'project' => $project,
            'dates' => iterator_to_array(new \DatePeriod($result->start, new \DateInterval('P1D'), $result->end)),
            'now' => $now,
            'result' => $result,
            'next' => $result->month->modify('+1 month'),
            'prev' => $result->month->modify('-1 month'),
            'form' => $form->createView(),
        ]);
    }
*/
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
        GoogleСalendar $сalendar,
        UserFetcher $fetcher

        )
    {
        $query = $request->query->get('form');
        // $date = Query\Query::date($query['start_date'], $query['end_date']);
        $id = 5;
        $vacation = new Vacation($id, $holiday);
        $party = new Date($party);
        $user = new User();

        $date = new Query ($query['userId'], $query['start_date'], $query['end_date'], $vacation, $сalendar, $party);


        $json = new Json($party, $user);


        dd($json->getJSON($date->shedule()));




        $filter = new Filter();
/*

        "start_date" => "January 1, 2019"
      "end_date" => "January 31, 2019"

*/
        $result = 1;

        $now = new \DateTimeImmutable();





        dd($request);
        return $this->render('shedule/index.html.twig', [
            'form' => $form->createView(),

        ]);
    }
}
