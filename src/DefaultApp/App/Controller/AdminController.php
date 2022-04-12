<?php
namespace VPFramework\DefaultApp\App\Controller;

use Doctrine\ORM\EntityManager;
use PDOException;
use VPFramework\Core\Configuration\ServiceConfiguration;
use VPFramework\Core\Constants;
use VPFramework\Core\Request;
use VPFramework\DefaultApp\App\Entity\Admin;
use VPFramework\DefaultApp\App\Repository\AdminRepository;
use VPFramework\Model\Repository\Repository;
use VPFramework\Service\Security\Security;

class AdminController extends DefaultAppController
{
	private $em, $entitiesAdmin;
	public function __construct(EntityManager $em, ServiceConfiguration $serviceConfig){
		$this->em = $em;
		$this->entitiesAdmin = $serviceConfig->getService("admin");
	}

	public function index(){
		return $this->render("admin/home.php", ["entitiesAdmin" => $this->entitiesAdmin]);
	}

	public function login(Request $request,AdminRepository $repo){
		if(count($repo->findAll()) > 0){
			$error = "";
			if($request->get("username") != null){
				$admin = $repo->findOneBy(["userName" => $request->get("username")]);
				if($admin != null){
					if($admin->getPassword() == sha1($request->get("password"))){
						Security::login($admin, AdminRepository::class);
						$this->redirectRoute("admin");
					}else{
						$error = "Mot de passe incorrect";
					}
				}else{
					$error = "Utilisateur inexistant";
				}
			}	
			return $this->render("admin/login.php", compact("error"));
		}else{
			$this->redirectRoute("admin-first-dmin");
		}
	}

	public function firstAdmin(Request $request,AdminRepository $repo){
		if(count($repo->findAll()) == 0){
			$error = "";
			if($request->get("username") != null){
				if($request->get("password") == $request->get("confirm-password")){
					$admin = new Admin(true);
					$admin->setUserName($request->get("username"))->setPassword(sha1($request->get("password")));
					$this->em->persist($admin);
					$this->em->flush();
					$this->redirectRoute("adminLogin");
				}else{
					$error = "Les mots de passe ne sont pas identiques";
				}
			}	
			return $this->render("admin/first-admin.php", compact("error"));
		}else{
			$this->redirectRoute("admin-login");
		}
	}

	public function logout(){
		Security::logout();
		$this->redirectRoute("admin-login");
	}

}