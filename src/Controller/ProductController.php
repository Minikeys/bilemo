<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;

class ProductController extends AbstractFOSRestController
{

    /**
     * Get All Product
     * @Rest\Get(
     *     "/api/products",
     *      name = "products"
     * )
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns all products",
     *     @Model(type=Product::class)
     * )
     *
     * @SWG\Tag(name="products")
     */
    public function getAllProducts()
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findAll();

        return $this->handleView($this->view($products));
    }

    /**
     * Get One Product
     * @Rest\Get(
     *     "/api/products/{id}",
     *     name = "products_show",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="products")
     * @SWG\Response(
     *     response=200,
     *     description="Returns one product",
     *     @Model(type=Product::class)
     * )
     * @return Response
     */
    public function getOneProduct($id)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $product = $repository->find($id);
        if(!is_null($product)){
            return $this->handleView($this->view($product));
        }else{
            return $this->handleView($this->view(['status' => 'Product not found.'], Response::HTTP_NOT_FOUND));
        }

    }

}