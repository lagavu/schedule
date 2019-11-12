<?php

namespace App\Controller\Shedule;

use App\Form\SheduleType;
use App\Model\User\Entity\User\User;
use App\ReadModel\Shedule\SheduleFetcher;
use App\ReadModel\Shedule\Query;
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
    public function shedule(Request $request)
    {

        $user = new User();

        $now = new \DateTimeImmutable();

        $query = Query\Query::fromDate($now);

        $form = $this->createForm(Query\Form::class);

        return $this->render('shedule/index.html.twig', [
            'form' => $form->createView(),

        ]);
    }
}
