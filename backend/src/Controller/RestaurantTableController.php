<?php

namespace App\Controller;
header("Access-Control-Allow-Origin: *");

use App\Repository\RestaurantRepository;
use App\Repository\RestaurantTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class RestaurantTableController extends AbstractController
{
    private $tableRepository;
    private $restaurantRepository;
    public function __construct(RestaurantTableRepository $tableRepository, RestaurantRepository $restaurantRepository)
    {
        $this->tableRepository = $tableRepository;
        $this->restaurantRepository = $restaurantRepository;
    }

    /**
     * Create table
     *
     * This API is used for creating a new table for users to be seated on
     *
     * @SWG\Response(
     *     response=200,
     *     description="Table sucessfully added",
     * )
     *@SWG\Parameter(
     *     name="tableName",
     *     in="formData",
     *     type="string",
     *     description="Table's name"
     * )
     *@SWG\Parameter(
     *     name="numberOfSeats",
     *     in="formData",
     *     type="string",
     *     description="Table's profile image"
     * )
     *@SWG\Parameter(
     *     name="tableType",
     *     in="formData",
     *     type="integer",
     *     description="Determines what type of table this table is"
     * )
     *@SWG\Parameter(
     *     name="restaurantId",
     *     in="formData",
     *     type="integer",
     *     description="Id of restaurant that owns this table"
     * )
     * @SWG\Tag(name="Table")
     * @Route("/table/add", name="create_table", methods={"POST"})
     */
    public function createTable(Request $request):
    JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tableName = $data['tableName'];
        $numberOfSeats = $data['numberOfSeats'];
        $tableType = $data['tableType'];
        $restaurantId = $data['restaurantId'];
        $tableName = htmlspecialchars(strip_tags($tableName));
        $numberOfSeats = htmlspecialchars(strip_tags($numberOfSeats));
        $tableType = htmlspecialchars(strip_tags($tableType));
        $restaurantId = htmlspecialchars(strip_tags($restaurantId));
        if (empty($tableName or $tableType or $tableName or $numberOfSeats or $restaurantId)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $restaurant = $this->restaurantRepository->find($restaurantId);
        $this->tableRepository->saveTable($tableName, $numberOfSeats,$tableType, $restaurant);
        return new JsonResponse(['response' => 'Restaurant table added!'], Response::HTTP_CREATED);
    }

    /**
     *
     *Get all tables
     *
     * This API returns all tables in JSON
     *
     *@SWG\Response(
     *response=200,
     *description="Tables successfully returned",
     * )
     * @SWG\Tag(name="Table")
     * @Route("/table/all", name="get_all_tables", methods={"GET"})
     */
    public function getAllTables(): JsonResponse
    {
        $tables = $this->tableRepository->findAll();
        foreach ($tables as $table) {
            $restaurantId = $table->getRestaurant();
            $restaurant = $this->restaurantRepository->find($restaurantId);
                $restaurantData = [
                    'id' => $restaurant->getId(),
                    'restaurantName' => $restaurant->getRestaurantName(),
                    'restaurantImage' => $restaurant->getRestaurantImage(),
                    'dateCreated' => $restaurant->getDateCreated()->format("d/m/yy"),
                    'dateModified' => $restaurant->getDateModified()->format("d/m/yy")
                ];
            $data[] = [
                'id' => $table->getId(),
                'tableName' => $table->getTableName(),
                'numberOfSeats' => $table->getNumberOfSeats(),
                'tableType' => $table->getTableType(),
                'restaurant' => $restaurantData
            ];
        }
        return new JsonResponse(['tables' => $data], Response::HTTP_OK);
    }

    /**
     * Find specific table
     *
     * This API returns only one table in JSON
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns data of one table",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="No such table in DB",
     * )
     * @SWG\Tag(name="Table")
     * @Route("/table/{id}", name="get_one_table", methods={"GET"})
     */
    public function getOneTable($id): JsonResponse
    {
        $table = $this->tableRepository->find($id);
        if ($table==null){
            return new JsonResponse(['status' => "table not found"], Response::HTTP_BAD_REQUEST);
        }
        $restaurantData = [];
        $restaurant = $table->getRestaurant();
        $restaurantData[] = [
                'id' => $restaurant->getId(),
                'restaurantName' => $restaurant->getRestaurantName(),
                'restaurantImage' => $restaurant->getRestaurantImage(),
                'dateCreated' => $restaurant->getDateCreated(),
                'dateModified' => $restaurant->getDateModified()
            ];

        $data[] = [
            'id' => $table->getId(),
            'tabletName' => $table->getTableName(),
            'numberOfSeats' => $table->getNumberOfSeats(),
            'tableType' => $table->getTableType(),
            'dateCreated' => $table->getDateCreated(),
            'dateModified' => $table->getDateModified(),
            'restaurant' => $restaurantData
        ];
        return new JsonResponse(['tables' => $data], Response::HTTP_OK);
    }

    /**
     * Update table
     *
     * This API updates specific table
     * @SWG\Response(
     *     response=400,
     *     description="No such table in DB or PUT data is is such format that nothing was updated",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="User sucefully updated",
     * )
     * @SWG\Tag(name="Table")
     * @Route("/table/update/{id}", name="update_table", methods={"PUT"})
     */
    public function updateTable($id, Request $request): JsonResponse
    {
        $table = $this->tableRepository->find($id);
        $data = json_decode($request->getContent(), true);
        if ($this->tableRepository->updateTable($table, $data)){
            return new JsonResponse(['status' => 'table updated!'], Response::HTTP_OK);
        }
        else {return new JsonResponse(['status' => 'table not updated!'], Response::HTTP_BAD_REQUEST);}
    }
    /**
     * Delete table
     *
     * This API deletes specific table
     * @SWG\Response(
     *     response=400,
     *     description="No such table in DB",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Group sucefully deleted",
     * )
     * @SWG\Tag(name="Table")
     * @Route("table/delete/{id}", name="delete_table", methods={"DELETE"})
     */
    public function deleteTable($id): JsonResponse
    {
        $table = $this->tableRepository->find($id);
        if (empty($table)) {
            return new JsonResponse(['response' => "table not found"], Response::HTTP_BAD_REQUEST);
        }
            $this->tableRepository->deleteTable($table);
            return new JsonResponse(['status' => 'table deleted'], Response::HTTP_OK);
    }
}
