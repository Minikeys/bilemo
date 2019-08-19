<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Product;
use App\Form\CustomerType;
use App\Form\ProductType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Hateoas\Configuration\Annotation as Hateoas;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class CustomerController
 * @package App\Controller
 */
class CustomerController extends AbstractFOSRestController
{

    /**
     * Get All Customer
     * @Rest\Get(
     *     "/api/customers",
     *      name = "customers"
     * )
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns all customers",
     *     @Model(type=Customer::class)
     * )
     *
     * @SWG\Tag(name="customers")
     */
    public function getAllCustomer()
    {
        $repository = $this->getDoctrine()->getRepository(Customer::class);
        $customers = $repository->findByUser($this->getUser());
        return $this->handleView($this->view($customers));
    }

    /**
     * Get One Customer
     * @Rest\Get(
     *     "/api/customers/{id}",
     *     name = "get_customers",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="customers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns one customer",
     *     @Model(type=Customer::class)
     * )
     * @return Response
     */
    public function getOneCustomer($id)
    {
        $repository = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repository->find($id);
        if(!is_null($customer)){
            if($customer->getUser() == $this->getUser()) {
                return $this->handleView($this->view($customer));
            }
            return $this->handleView($this->view(['status' => 'Customer is not linked to your account.'], Response::HTTP_NOT_FOUND));

        }else{
            return $this->handleView($this->view(['status' => 'Customer not found.'], Response::HTTP_NOT_FOUND));
        }

    }

    /**
     * Create New Customer
     * @param Request $request
     * @Rest\Put(
     *     "/api/customers",
     *     name = "new_custromers"
     * )
     * @Rest\View(StatusCode = 201)
     * @SWG\Response(
     *     response=201,
     *     description="Returns customer created",
     *     @Model(type=Customer::class)
     * )
     * @SWG\Tag(name="customers")
     * @return Response
     */
    public function createCustomer(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $customer->setUser($this->getUser());
            $em->persist($customer);
            $em->flush();
            return $this->handleView($this->view(['status' => 'Customer create success.'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Delete customer
     * @Rest\Delete(
     *     "/api/customers/{id}",
     *     name = "delete_customers",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="customers")
     * @SWG\Response(
     *     response=201,
     *     description="Returns success"
     * )
     * @return Response
     */
    public function deleteCustomer($id)
    {
        $repository = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repository->find($id);
        if(!is_null($customer)){
            if($customer->getUser() == $this->getUser()){
                $em = $this->getDoctrine()->getManager();
                $em->remove($customer);
                $em->flush();
                return $this->handleView($this->view(['status' => 'Customer delete success.'], Response::HTTP_CREATED));
            }

            return $this->handleView($this->view(['status' => 'Customer is not linked to your account.'], Response::HTTP_NOT_FOUND));

        }else{
            return $this->handleView($this->view(['status' => 'Customer not found.'], Response::HTTP_NOT_FOUND));
        }

    }

}