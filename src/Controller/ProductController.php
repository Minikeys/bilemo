<?php

namespace App\Controller;

use App\Entity\Product;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
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
     * @Rest\View(serializerGroups={"list"})
     * @SWG\Tag(name="products")
     */
    public function getAllProducts()
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findAll();

        return $products;
    }

    /**
     * Get One Product
     * @Rest\Get(
     *     "/api/products/{id}",
     *     name = "get_products",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="products")
     * @SWG\Response(
     *     response=200,
     *     description="Returns one product.",
     *     @Model(type=Product::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Product not found.",
     * )
     * @Rest\View(serializerGroups={"details"})
     * @return Response
     */
    public function getOneProduct($id)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $product = $repository->find($id);
        if(!is_null($product)){
            return $product;
        }else{
            return $this->handleView($this->view(['status' => 'Product not found.'], Response::HTTP_NOT_FOUND));
        }

    }

}