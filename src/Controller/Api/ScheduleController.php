<?php

namespace App\Controller\Api;

use App\RemoteService\GoogleCalendar;
use App\Repository\PartyRepository;
use App\Repository\UserRepository;
use App\Service\Schedule;
use App\Service\ScheduleQuery;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ScheduleController extends AbstractController
{
    /**
     * @Route("/api/schedule", name="api.schedule", methods={"GET"})
     * @throws Exception
     */
    public function index(Request $request, PartyRepository $partyRepository,
                          UserRepository $userRepository, GoogleCalendar $calendar, ValidatorInterface $validator): Response
    {
        $scheduleQuery = new ScheduleQuery();
        $scheduleQuery->userId = $request->query->get('userId');
        $scheduleQuery->startDate = $request->query->get('startDate');
        $scheduleQuery->endDate = $request->query->get('endDate');

        $errors = $validator->validate($scheduleQuery);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            $response = $this->json($errorsString);
            $response->setStatusCode(422);

            return $response;
        }

        $user = $userRepository->findUser($scheduleQuery->userId);
        $schedule = new Schedule($user, $partyRepository, $calendar);
        $scheduleUser = $schedule->getSchedule(new \DateTime($scheduleQuery->startDate), new \DateTime($scheduleQuery->endDate));

        return $this->json($scheduleUser);
    }
}