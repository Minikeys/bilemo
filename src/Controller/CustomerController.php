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
        $customers = $this->getUser()->getCustomers();
        return $this->handleView($this->view($customers));
    }

    /**
     * Get One Customer
     * @Rest\Get(
     *     "/api/customers/{id}",
     *     name = "customers_show",
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
            return $this->handleView($this->view($customer));
        }else{
            return $this->handleView($this->view(['status' => 'Customer not found.'], Response::HTTP_CREATED));
        }

    }

    /**
     * Create New Customer
     * @param Request $request
     * @Rest\Post(
     *     "/api/customers",
     *     name = "customers_create"
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
        $customer = $this->getUser()->addCustomer();
        $form = $this->createForm(CustomerType::class, $customer);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();
            return $this->handleView($this->view(['status' => 'Product create success.'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Delete product
     * @Rest\Delete(
     *     "/api/products/{id}",
     *     name = "products_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="products")
     * @SWG\Response(
     *     response=201,
     *     description="Returns success"
     * )
     * @return Response
     */
    public function deleteProduct($id)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $product = $repository->find($id);
        if(!is_null($product)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
            return $this->handleView($this->view(['status' => 'Product delete success.'], Response::HTTP_CREATED));
        }else{
            return $this->handleView($this->view(['status' => 'Product not found.'], Response::HTTP_CREATED));
        }

    }

}