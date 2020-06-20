<?php

namespace App\Controller;

header("Access-Control-Allow-Origin: *");

use App\Repository\WaitingListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class WaitingListController extends AbstractController
{
    private $waitingListRepository;
    public function __construct(WaitingListRepository $waitingListRepository)
    {
        $this->waitingListRepository=$waitingListRepository;
    }

    /**
     * Create waiting
     *
     * This API is used for creating a new waiting
     *
     * @SWG\Response(
     *     response=200,
     *     description="Waiting sucessfully added",
     * )
     *@SWG\Parameter(
     *     name="nameOfWaiting",
     *     in="formData",
     *     type="string",
     *     description="Waiting's name"
     * )
     *@SWG\Parameter(
     *     name="dateOfWaiting",
     *     in="formData",
     *     type="string",
     *     description="Date of waiting"
     * )
     *@SWG\Parameter(
     *     name="timeOfWaiting",
     *     in="formData",
     *     type="string",
     *     description="Time of waiting"
     * )
     *@SWG\Parameter(
     *     name="numberOfGuests",
     *     in="formData",
     *     type="string",
     *     description="Number of guests in one waiting"
     * )
     *@SWG\Parameter(
     *     name="latitude",
     *     in="formData",
     *     type="string",
     *     description="Geo latitude of waiting"
     * )
     *@SWG\Parameter(
     *     name="longitude",
     *     in="formData",
     *     type="string",
     *     description="Geo longitude of waiting"
     * )
     * @SWG\Tag(name="Waiting")
     * @Route("/waiting/add", name="create_waiting", methods={"POST"})
     */
    public function createWaiting(Request $request):
    JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nameOfWaiting = $data['nameOfWaiting'];
        $dateOfWaiting = $data['dateOfWaiting'];
        $timeOfWaiting = $data['timeOfWaiting'];
        $numberOfGuests = $data['numberOfGuests'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        $nameOfWaiting = htmlspecialchars(strip_tags($nameOfWaiting));
        $dateOfWaiting = htmlspecialchars(strip_tags($dateOfWaiting));
        $timeOfWaiting = htmlspecialchars(strip_tags($timeOfWaiting));
        $numberOfGuests = htmlspecialchars(strip_tags($numberOfGuests));
        $latitude = htmlspecialchars(strip_tags($latitude));
        $longitude = htmlspecialchars(strip_tags($longitude));
        if (empty($nameOfWaiting or $timeOfWaiting or $numberOfGuests)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        if ($this->waitingListRepository->saveWaiting($nameOfWaiting, $dateOfWaiting, $timeOfWaiting, $numberOfGuests, $latitude, $longitude)){
            return new JsonResponse(['response' => 'Waiting table added!'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['response' => 'Waiting table not added!'], Response::HTTP_BAD_REQUEST);
    }
    /**
     *
     *Get all waiting
     *
     * This API returns all waiting in JSON
     *
     *@SWG\Response(
     *response=200,
     *description="Waiting successfully returned",
     * )
     * @SWG\Tag(name="Waiting")
     * @Route("/waiting/all", name="get_all_waitings", methods={"GET"})
     */
    public function getAllWaitings(): JsonResponse
    {
        $waitings = $this->waitingListRepository->findAll();
        foreach ($waitings as $waiting) {
            $data[] = [
                'id' => $waiting->getId(),
                'nameOfWaiting' => $waiting->getName(),
                'dateOfWaiting' => $waiting->getDate(),
                'timeOfWaiting' => $waiting->getTime()->format("h:i"),
                'dateCreated' => $waiting->getDateCreated(),
                'dateModified' => $waiting->getDateModified(),
                'numberOfGuests' => $waiting->getNumberOfGuests(),
                'latitude' => $waiting->getLatitude(),
                'longitude' => $waiting->getLongitude()
            ];
        }
        return new JsonResponse(['waitings' => $data], Response::HTTP_OK);
    }
    /**
     * Find specific waiting
     *
     * This API returns only one waiting in JSON
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns data of only one waiting",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="No such waiting in DB",
     * )
     * @SWG\Tag(name="Waiting")
     * @Route("/waiting/{id}", name="get_one_waiting", methods={"GET"})
     */
    public function getOneWaiting($id): JsonResponse
    {
        $waiting = $this->waitingListRepository->find($id);
        if ($waiting==null){
            return new JsonResponse(['status' => "waiting not found"], Response::HTTP_BAD_REQUEST);
        }

        $data[] = [
            'id' => $waiting->getId(),
            'nameOfWaiting' => $waiting->getName(),
            'dateOfWaiting' => $waiting->getDate(),
            'timeOfWaiting' => $waiting->getTime(),
            'dateCreated' => $waiting->getDateCreated(),
            'dateModified' => $waiting->getDateModified(),
            'numberOfGuests' => $waiting->getNumberOfGuests(),
            'latitude' => $waiting->getLatitude(),
            'longitude' => $waiting->getLongitude()
        ];
        return new JsonResponse(['waiting' => $data], Response::HTTP_OK);
    }

    /**
     * Update waiting
     *
     * This API updates specific waiting
     * @SWG\Response(
     *     response=400,
     *     description="Waiting was not updated",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Waiting sucefully updated",
     * )
     * @SWG\Tag(name="Waiting")
     * @Route("/waiting/update/{id}", name="update_waiting", methods={"PUT"})
     */
    public function updateWaiting($id, Request $request): JsonResponse
    {
        $waiting = $this->waitingListRepository->find($id);
        $data = json_decode($request->getContent(), true);
        if ($this->waitingListRepository->updateWaiting($waiting, $data)){
            return new JsonResponse(['status' => 'waiting updated!'], Response::HTTP_OK);
        }
        else {return new JsonResponse(['status' => 'waiting not updated!'], Response::HTTP_BAD_REQUEST);}
    }

    /**
     * Delete waiting
     *
     * This API deletes specific waiting
     * @SWG\Response(
     *     response=400,
     *     description="No such waiting found in DB",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Waiting sucefully deleted",
     * )
     * @SWG\Tag(name="Waiting")
     * @Route("waiting/delete/{id}", name="delete_waiting", methods={"DELETE"})
     */
    public function deleteWaiting($id): JsonResponse
    {
        $waiting = $this->waitingListRepository->find($id);
        if (empty($waiting)) {
            return new JsonResponse(['response' => "waiting not found"], Response::HTTP_BAD_REQUEST);
        }
        $this->waitingListRepository->deleteWaiting($waiting);
        return new JsonResponse(['status' => 'waiting deleted'], Response::HTTP_OK);

    }
}
