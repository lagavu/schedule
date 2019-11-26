<?php


namespace App\Controller\Api;


use App\RemoteService\GoogleCalendar;
use App\Repository\PartyRepository;
use App\Repository\UserRepository;
use App\Service\Schedule;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    /**
     * @Route("/api/schedule", name="api.schedule", methods={"GET"})
     * @throws Exception
     */
    public function index(Request $request, PartyRepository $partyRepository,
                          UserRepository $userRepository, GoogleCalendar $calendar): JsonResponse
    {
        $userId = $request->query->get('userId');
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        $user = $userRepository->findUser($userId);

                $schedule = new Schedule($user, $partyRepository, $calendar);
                $scheduleUser = $schedule->getSchedule(new \DateTime($startDate), new \DateTime($endDate));

        return $this->json($scheduleUser);

    }
}