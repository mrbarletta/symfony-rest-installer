<?php

namespace **NAMESPACEPREFIX**\Controller\Rest\v1;

use **NAMESPACEPREFIX**\Entity\User;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use FOS\RestBundle\Controller\FOSRestController;
/*
 * This Class generates automatic routes using the function name
 * getUsersAction (will be a get for Users)
 * getUserCommentAction (will be a get for Users with comments) users/USER_ID/comments/COMMENT_ID
 *
 * PLural means query everything (getUsersAction) , singular means getUserComments will expect parameters
 *
 */

// Controller name itself is not used in routes auto-generation process and can be any name you like.
class UserController extends FOSRestController
{
    private $repo_name = '**FULLBUNDLENAME**:User';

    private function getRepo()
    {
        return $this->getDoctrine()->getManager()->getRepository($this->repo_name);
    }

    private function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    private function getEM()
    {
        return $this->getEntityManager();
    }

// In this case, your UsersController MUST always have a single resource get... action: It's used to determine the parent collection name.
    public function getUserAction($user)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        $found = false;

        //Default serialization context (Check Entity.User.yml) when serializing.
        $context = \JMS\Serializer\SerializationContext::create()->setGroups(array('default'));
        if (is_null($user)) {
            $user = $this->get('security.context')->getToken()->getUser()->getId();
            $context = \JMS\Serializer\SerializationContext::create()->setGroups(array('user'));
            $found = true;
        }

        if ($this->get('security.context')->isGranted('ROLE_USER')) {

            $data = array();
            $em = $this->getDoctrine()->getManager()->getRepository($this->repo_name);
            $user = $em->findOneById($user);
            if (!$user instanceof User) {
                $data['user'] = "empty set";
                $view = $this->view($data, 200)
                    ->setSerializationContext($context);
                return $this->handleView($view);
            } else {
                $data['user'] = $user;
                $data['role'] = 'ROLE_USER';
            }
        }

        $view = $this->view($data, 200)
            ->setSerializationContext($context);

        return $this->handleView($view);
    } // "get_user"      [GET] /users/{slug}


    /**
     * @throws AccessDeniedException
     * @return array
     *
     *
     */
    public function getUsersAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        $data = array();
        $em = $this->getDoctrine()->getManager()->getRepository($this->repo_name);
        $users = $em->FindAll();
        $context = \JMS\Serializer\SerializationContext::create()->setGroups(array('default'));
        if (!$users) {
            $data['users'] = "empty set";
        } else {
            $data['users'] = $users;
        }

        $view = $this->view($data, 200)
            ->setSerializationContext($context);
        return $this->handleView($view);
    } // "get_users"     [GET] /users

    public function putUserAction($user, Request $request)
    {
        $response = new Response();
        if ($this->get('security.context')->isGranted('ROLE_USER') && $user == 0) {
            $db = $this->get('lxdb'); //This line works with service declared on config.yml
            $user = $this->get('security.context')->getToken()->getUser()->getId();
            $data = json_decode($request->getContent());

            // User Detail
            $sql = "
                update fos_user set
            ";

            if (property_exists($data->user, 'name')) {
                $sql .= " name = '{$data->user->name}',";
            }

            if (property_exists($data->user, 'lastname')) {
                $sql .= " lastname = '{$data->user->lastname}',";
            }

            if (property_exists($data->user, 'photo')) {
                $sql .= " photo = '{$data->user->photo}',";
            }

            if (property_exists($data->user, 'fullname')) {
                $sql .= " fullname = '{$data->user->fullname}',";
            }

            if (property_exists($data->user, 'country')) {
                $sql .= " country = '{$data->user->country}',";
            }

            if (property_exists($data->user, 'state')) {
                $sql .= " state = '{$data->user->state}',";
            }

            if (property_exists($data->user, 'city')) {
                $sql .= " city = '{$data->user->city}',";
            }

            if (property_exists($data->user, 'billingAddress1')) {
                $sql .= " billing_address_1 = '{$data->user->billingAddress1}',";
            }

            if (property_exists($data->user, 'billingAddress2')) {
                $sql .= " billing_address_2 = '{$data->user->billingAddress2}',";
            }

            if (property_exists($data->user, 'zipcode')) {
                $sql .= " zipcode = '{$data->user->zipcode}',";
            }

            if (property_exists($data->user, 'zipcodeExt')) {
                $sql .= " zipcode_ext = '{$data->user->zipcodeExt}',";
            }

            $sql = rtrim($sql, ",");
            $sql .= "where id = '{$user}'";
            $db->execute($sql, 'default');
            $statusCode = 204;
        } else {
            $statusCode = 404;
            $response->setContent(json_encode(array('info' => 'User not allowed to update')));
        }
        $response->setStatusCode($statusCode);
        return $response;
    }

}
