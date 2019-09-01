<?php

namespace App\Controller;

use App\Entity\Product;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Contracts\Cache\ItemInterface;

class ProductController extends AbstractFOSRestController
{

    /**
     * Get All Product
     * @Rest\Get(
     *     "/api/products",
     *      name = "products"
     * )
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
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

        $cache = new FilesystemAdapter();
        $value = $cache->get('products', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $repository = $this->getDoctrine()->getRepository(Product::class);
            $products = $repository->findAll();

            return $products;
        });

        return $value;
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
        $cache = new FilesystemAdapter();
        $product = $cache->getItem('product.'.$id);

        if (!$product->isHit()) {
            $repository = $this->getDoctrine()->getRepository(Product::class);
            $product->set($repository->find($id));
            $product = $cache->getItem('product.'.$id);
            $value = $product->get();
            return $value;
        }else{

            $product = $cache->getItem('product.'.$id);
            $value = $product->get();

            if(!is_null($value)){
                return $value;
            }else {
                return $this->handleView($this->view(['status' => 'Product not found.'], Response::HTTP_NOT_FOUND));
            }

        }






    }

}