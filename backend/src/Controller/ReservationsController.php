<?php
namespace App\Controller;
header("Access-Control-Allow-Origin: *");
use App\Repository\ReservationsRepository;
use App\Repository\RestaurantRepository;
use App\Repository\RestaurantTableRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

class ReservationsController extends AbstractController
{
    private $reservationsRepository;
    private $restaurantRepository;
    private $restaurantTableRepository;
    public function __construct(ReservationsRepository $reservationsRepository, RestaurantRepository $restaurantRepository,RestaurantTableRepository $restaurantTableRepository)
    {
        $this->reservationsRepository=$reservationsRepository;
        $this->restaurantRepository=$restaurantRepository;
        $this->restaurantTableRepository=$restaurantTableRepository;
    }

    /**
     * Create reservation
     *
     * This API is used for creating reservations
     *
     * @SWG\Response(
     *     response=200,
     *     description="Reservation sucessfully added",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="POST data is either in bad format or empty",
     * )
     *@SWG\Parameter(
     *     name="reservationName",
     *     in="formData",
     *     type="string",
     *     description="Name of reservation"
     * )
     *@SWG\Parameter(
     *     name="dateOfReservation",
     *     in="formData",
     *     type="string",
     *     description="Date of reservation"
     * )
     *@SWG\Parameter(
     *     name="timeOfReservation",
     *     in="formData",
     *     type="string",
     *     description="Time of reservation"
     * )
     *@SWG\Parameter(
     *     name="numberOfGuests",
     *     in="formData",
     *     type="integer",
     *     description="Number of guests"
     * )
     * @SWG\Tag(name="Reservation")
     * @Route("/reservation/add", name="create_reservation", methods={"POST"})
     */
    public function createReservation(Request $request):JsonResponse
    {
        $data = json_decode($request->getContent() , true);

        $reservationName = $data['reservationName'];
        $dateOfReservation = $data['dateOfReservation'];
        $timeOfReservation = $data['timeOfReservation'];
        $numberOfGuests =  $data['numberOfGuests'];
        $restaurantId = $data['restaurantId'];
        $restaurantTableId = $data['restaurantTableId'];

        $reservationName = htmlspecialchars(strip_tags($reservationName));
        $dateOfReservation = htmlspecialchars(strip_tags($dateOfReservation));
        $timeOfReservation = htmlspecialchars(strip_tags($timeOfReservation));
        $numberOfGuests = htmlspecialchars(strip_tags($numberOfGuests));
        $restaurantId = htmlspecialchars(strip_tags($restaurantId));
        $restaurantTableId = htmlspecialchars(strip_tags($restaurantTableId));

        if (empty($reservationName)||empty($dateOfReservation)||empty($timeOfReservation)||empty($numberOfGuests)||empty($restaurantId)||empty($restaurantTableId))
        {
            return new JsonResponse(['status' => "Reservation was not created, some parameters are missing or are in wrong format"], Response::HTTP_BAD_REQUEST);
        }
        try {
            $restaurant = $this->restaurantRepository->find($restaurantId);
            $restaurantTable = $this->restaurantTableRepository->find($restaurantTableId);
            $this->reservationsRepository->saveReservation($reservationName, $dateOfReservation,$timeOfReservation,$numberOfGuests, $restaurant, $restaurantTable);
        } catch (\Exception $exception){
            return new JsonResponse("Krivi format datuma, vremena ili ne postoji user. Treba biti u formatu 2021-01-28 za datum i 22:38:00 ili 22:38 ili samo 22", Response::HTTP_BAD_REQUEST);
        }


        return new JsonResponse(['status' => 'reservation added!'], Response::HTTP_CREATED);
    }

    /**
     *
     *Get all reservations
     *
     * This API returns all reservations in JSON
     *
     *@SWG\Response(
     *response=200,
     *description="Reservation successfully returned",
     * )
     * @SWG\Tag(name="Reservation")
     * @Route("/reservations/all", name="get_all_reservations", methods={"GET"})
     */
    public function getAllReservations(): JsonResponse
    {
        $reservations = $this->reservationsRepository->findAll();

        $data = [];

        foreach ($reservations as $reservation) {
            $temp= (array)$reservation->getDateOfReservation();
            $date = new DateTime($temp["date"]);
            $date= $date->format('d-m-Y');
            $temp= (array)$reservation->getTimeOfReservation();
            $time = new DateTime($temp["date"]);
            $time= $time->format('H:i');

            $restaurant =$this->restaurantRepository->find($reservation->getRestaurant()->getId());
            $restaurant->getTablesOfRestaurant();
            $tables =$this->restaurantTableRepository->findAll();
            $tableData=[];
            foreach ($tables as $table){
                if ($table->getRestaurant()->getId()==$restaurant->getId()){
                    $tableData[]=[
                      $table->getTableType()
                    ];
                }
            }
            $data[] = [
                'id' => $reservation->getId(),
                'reservationName' => $reservation->getName(),
                'dateOfReservation' => $date,
                'timeOfReservation' => $time,
                'numberOfGuests' => $reservation->getNumberOfGuests(),
                'restaurantId' => $reservation->getRestaurant()->getId(),
                'tableType' =>$tableData
            ];
        }

        return new JsonResponse(['reservations' => $data], Response::HTTP_OK);
    }
    /**
     * Find specific reservation by id
     *
     * This API returns only one reservation in JSON
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns data of one reservation",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="No such reservation in DB",
     * )
     * @SWG\Tag(name="Reservation")
 * @Route("reservations/get/{id}", name="get_one_reservation", methods={"GET"})
 */
    public function getOneReservation($id): JsonResponse
    {
        $reservation = $this->reservationsRepository->findOneBy(['id' => $id]);
        if (empty($reservation)) {
            return new JsonResponse(['status' => "reservation not found"], Response::HTTP_BAD_REQUEST);
        }
        $temp= (array)$reservation->getDateOfReservation();
        $date = new DateTime($temp["date"]);
        $date= $date->format('d-m-Y');
        $temp= (array)$reservation->getTimeOfReservation();
        $time = new DateTime($temp["date"]);
        $time= $time->format('H:i');
        $data[] = [
            'id' => $reservation->getId(),
            'reservationName' => $reservation->getName(),
            'dateOfReservation' => $date,
            'timeOfReservation' => $time,
            'numberOfGuests' => $reservation->getNumberOfGuests(),
            'restaurantId' => $reservation->getRestaurant()->getId()
        ];

        return new JsonResponse(['reservation' => $data], Response::HTTP_OK);
    }

    /**
     * Find specific reservation by date
     *
     * This API returns only one reservation in JSON
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns data of one reservation",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="No such reservation in DB",
     * )
     * @SWG\Tag(name="Reservation")
     * @Route("reservations/date/{date}", name="get_reservations_by_date", methods={"GET"})
     */
    public function getReservationsByDate($date): JsonResponse
    {
        $reservations = $this->reservationsRepository->findByDate($date);
        if (count($reservations)==0) {
            return new JsonResponse(['status' => "reservation not found"], Response::HTTP_NOT_FOUND);
        }

        $reservationData=[];
        foreach ($reservations as $reservation){
            $restaurant =$this->restaurantRepository->find($reservation->getRestaurant()->getId());
            $restaurant->getTablesOfRestaurant();
            $restaurantTable=$this->restaurantTableRepository->find($reservation->getRestaurant()->getId());
            //$restaurantTable->getReservations();
            $tables =$this->restaurantTableRepository->findAll();
            $tableData=[];
            foreach ($tables as $table){
                if ($table->getRestaurant()->getId()==$restaurant->getId()){
                    if ($table->getId()==$reservation->getRestaurantTable()->getId()){
                        $tableData[]=[
                            $table->getTableType()
                        ];
                    }

                }
            }
            $reservationData[] = [
                "id" => $reservation->getId(),
                "nameOfReservation" => $reservation->getName(),
                "dateOfReservation" => $reservation->getDateOfReservation()->format("d/m/yy"),
                "timeOfReservation" => $reservation->getTimeOfReservation()->format("m:i"),
                "numberOfGuests" => $reservation->getNumberOfGuests(),
                "dateCreated" => $reservation->getDateCreated()->format("d/m/yy m:i"),
                "dateModified" => $reservation->getDateModified()->format("d/m/yy m:i"),
                'restaurantId' => $reservation->getRestaurant()->getId(),
                'tableType' =>$tableData
            ];
        }
        return new JsonResponse(['reservations' => $reservationData], Response::HTTP_OK);
    }

    /**
     * Update reservation
     *
     * This API updates specific reservation
     * @SWG\Response(
     *     response=400,
     *     description="No such reservation in DB or PUT data is in bad format",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Reservation sucefully updated",
     * )
     * @SWG\Tag(name="Reservation")
     * @Route("reservation/update/{id}", name="update_reservation", methods={"PUT"})
     */
    public function updateReservation($id, Request $request): JsonResponse
    {
        $reservation = $this->reservationsRepository->findOneBy(['id' => $id]);
        if (empty($reservation)) {
            return new JsonResponse(['status' => "reservation not found"], Response::HTTP_BAD_REQUEST);
        }
        $data = json_decode($request->getContent(), true);
        $this->reservationsRepository->updateReservation($reservation, $data);
        return new JsonResponse(['status' => 'reservation updated!'], Response::HTTP_OK);
    }
    /**
     * Delete reservation
     *
     * This API deletes specific reservation
     * @SWG\Response(
     *     response=400,
     *     description="No such reservation in DB",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Reservation sucefully deleted",
     * )
     * @SWG\Tag(name="Reservation")
     * @Route("reservation/delete/{id}", name="delete_reservation", methods={"DELETE"})
     */
    public function deleteUser($id): JsonResponse
    {
        $reservation = $this->reservationsRepository->findOneBy(['id' => $id]);
        if (empty($reservation)) {
            return new JsonResponse(['status' => "reservation not found"], Response::HTTP_BAD_REQUEST);
        }
        $this->reservationsRepository->removeReservation($reservation);
        return new JsonResponse(['status' => 'user deleted']);
    }
}
