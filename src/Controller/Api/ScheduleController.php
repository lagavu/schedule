<?php

namespace App\Controller\Api;

use App\RemoteApi\GoogleCalendarApi;
use App\Repository\PartyRepository;
use App\Repository\UserRepository;
use App\Service\ScheduleFactory;
use App\Service\ScheduleQuery;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @Route("/api/schedule", name="api.schedule", methods={"GET"})
     * @throws Exception
     */
    public function index(Request $request, ValidatorInterface $validator): Response
    {
        $scheduleQuery = new ScheduleQuery();
        $scheduleQuery->userId = $request->query->get('userId');
        $scheduleQuery->startDate = $request->query->get('startDate');
        $scheduleQuery->endDate = $request->query->get('endDate');

        $errors = $validator->validate($scheduleQuery);

        if (count($errors)) {
            return $this->json((string) $errors, 422);
        }

        $user = $this->userRepository->findById($scheduleQuery->userId);

        $scheduler = new ScheduleFactory($this->partyRepository, $this->calendarApi);
        $userSchedule = $scheduler->createUserSchedule($user, new \DateTime($scheduleQuery->startDate), new \DateTime($scheduleQuery->endDate));

        return $this->json($userSchedule);
    }
}
