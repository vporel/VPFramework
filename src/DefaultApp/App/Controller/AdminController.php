<?php
namespace VPFramework\DefaultApp\App\Controller;

use VPFramework\Core\Controller;
use Doctrine\ORM\EntityManager;
use PDOException;
use VPFramework\DefaultApp\App\Repository\AdminRepository;
use VPFramework\Model\Repository\Repository;

class AdminController extends Controller
{
	private $em;
	public function __construct(EntityManager $em){
		$this->em = $em;
	}

	public function index(){
		return "Administration";
	}

	public function login(AdminRepository $repo){
		$admins = $repo->findAll();
		
		return "Connexion administration";
	}
}