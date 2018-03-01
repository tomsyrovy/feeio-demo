<?php
	namespace AppBundle\DataFixtures\ORM;

	use AppBundle\DependencyInjection\ContactInfoDownloader;
	use AppBundle\DependencyInjection\ProfileImageCreator;
	use AppBundle\Entity\Company;
	use AppBundle\Entity\CompanyGroup;
	use AppBundle\Entity\Contact;
	use AppBundle\Entity\ContactCountry;
	use AppBundle\Entity\ContactType;
	use AppBundle\Entity\Factory\ActivityFactory;
	use AppBundle\Entity\Factory\CompanyFactory;
	use AppBundle\Entity\Factory\RoleFactory;
	use AppBundle\Entity\Factory\UserCompanyTemporalityFactory;
	use AppBundle\Entity\Factory\UserFactory;
	use AppBundle\Entity\Role;
	use AppBundle\Entity\UserCompany;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\DataFixtures\AbstractFixture;
	use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
	use Doctrine\Common\Persistence\ObjectManager;
	use UserBundle\Entity\User;

	class LoadFixturesData extends AbstractFixture implements OrderedFixtureInterface
	{

		/**
		 * @var ObjectManager
		 */
		private $manager;

		/**
		 * @var UserFactory
		 */
		private $userFactory;

		/**
		 * @var RoleFactory
		 */
		private $roleFactory;

		/**
		 * @var UserCompanyTemporalityFactory
		 */
		private $userCompanyTemporalityFactory;

		/**
		 * @var CompanyFactory
		 */
		private $companyFactory;

		/**
		 * @var ActivityFactory
		 */
		private $activityFactory;

		/**
		 * Get the order of this fixture
		 * @return integer
		 */
		public function getOrder() {
			return 2;
		}

		/**
		 * {@inheritDoc}
		 */
		public function load(ObjectManager $manager)
		{

			$this->manager = $manager;
			$this->userFactory = new UserFactory($this->manager);
			$this->roleFactory = new RoleFactory();
			$this->userCompanyTemporalityFactory = new UserCompanyTemporalityFactory();
			$this->companyFactory = new CompanyFactory();
			$this->activityFactory = new ActivityFactory();

			//Users
			$user1 = $this->createUser("michal.uryc@taktiq.com", "Řehoř", "Šišla", "password");
			$user2 = $this->createUser("michaela.lunakova@taktiq.com", "Michaela", "Luňáková", "password");
			$user3 = $this->createUser("karel.wolf@taktiq.com", "Karel", "Wolf", "password");
			$user4 = $this->createUser("jasna.sykorova@taktiq.com", "Jasna", "Sýkorová", "password");
			$user5 = $this->createUser("magda.zeithammelova@taktiq.com", "Magda", "Zeithammelová", "password");
//			$user6 = $this->createUser("jan.rihak@eternia.cz", "Jan", "Rihak", "password");
//			$user7 = $this->createUser("vojtech.ruzicka@eternia.cz", "Vojtěch", "Růžička", "password");
			$user8 = $this->createUser("aneta.poklopova@taktiq.com", "Aneta", "Poklopová", "password");
//			$user9 = $this->createUser("jiri@query.cz", "Jiří", "Šebek", "password");
			$user10 = $this->createUser("jan.kubat@taktiq.com", "Jan", "Kubát", "password");
			$user11 = $this->createUser("daniela.vinsova@taktiq.com", "Daniela", "Vinšová", "password");
			$user12 = $this->createUser("leona.dankova@taktiq.com", "Leona", "Daňková", "password");
			$user13 = $this->createUser("ivan.sobicka@taktiq.com", "Ivan", "Sobička", "password");
			$user14 = $this->createUser("juraj.redeky@taktiq.com", "Juraj", "Redeky", "password");
			$user15 = $this->createUser("jan.potucek@taktiq.com", "Jan", "Potůček", "password");
			$user16 = $this->createUser("alex.rohrich@taktiq.com", "Alex", "Röhrich", "password");
			$user17 = $this->createUser("jan.bohdal@taktiq.com", "Jan", "Bohdal", "password");
			$user18 = $this->createUser("lucie.adamiecova@taktiq.com", "Lucie", "Adamiecová", "password");
//			$user19 = $this->createUser('tomas@query.cz', 'Tomáš', 'Syrový', 'password');

			//Companies + owners
			$company1 = $this->createCompany("Taktiq", $user1); //MU
			$company2 = $this->createCompany("Heretic PR", $user1); //MU
//			$company3 = $this->createCompany("Eternia", $user6); //JR
//			$company4 = $this->createCompany("Query", $user9); //JŠ
			$company5 = $this->createCompany("DataConsult", $user1); //MU

			//Activities
			$companies = array(
				$company1,
				$company2,
//				$company3,
//				$company4,
				$company5,
			);
			$activities = array(
				'Komunikace s klientem',
				'Interní komunikace',
				'Komunikace s médii',
				'Komunikace s třetí stranou',
				'Tvorba textu',
				'Editace / korektura textu',
				'Překlad textu',
				'Mítink s klientem',
				'Interní mítink',
				'Mítink s médii',
				'Mítink s třetí stranou',
				'Podklady',
				'Správa sociálních médií',
				'Plánování / strategie sociálních médií',
				'Web',
				'Kreativita',
				'Cestování',
				'Monitoring',
				'Média',
				'Akce',
			);

			//Roles + authorizations

			$defaultAuthorizations = $this->manager->getRepository("AppBundle:Authorization")->findAll();

			$role1 = $this->createRole("Vlastník (superadmin)", $company1, true, $defaultAuthorizations);
			$role2 = $this->createRole("Člen společnosti", $company1, false, array());

			$role3 = $this->createRole("Vlastník (superadmin)", $company2, true, $defaultAuthorizations);
			$role4 = $this->createRole("Člen společnosti", $company2, false, array());

//			$role5 = $this->createRole("Vlastník (superadmin)", $company3, true, $defaultAuthorizations);
//			$role6 = $this->createRole("Člen společnosti", $company3, false, array());
//
//			$role7 = $this->createRole("Vlastník (superadmin)", $company4, true, $defaultAuthorizations);
//			$role8 = $this->createRole("Člen společnosti", $company4, false, array());

			$role9 = $this->createRole("Vlastník (superadmin)", $company5, true, $defaultAuthorizations);
			$role10 = $this->createRole("Člen společnosti", $company5, false, array());

			//UserCompany + UserCompanyTemporality
			$users2 = array(
				$user1,
				$user2,
				$user3,
				$user4,
				$user5,
				$user8,
				$user10,
				$user11,
				$user12,
				$user13,
				$user14,
				$user15,
				$user16,
				$user17,
				$user18
			);
			$companies2 = array(
				$company1,
				$company2,
				$company5,
			);
			foreach($users2 as $user){

				foreach($companies2 as $company){

					if($company->getName() === 'Taktiq'){

						if($user->getEmail() === 'michal.uryc@taktiq.com'){
							$role = $role1;
						}else{
							$role = $role2;
						}

					}

					if($company->getName() === 'Heretic PR'){

						if($user->getEmail() === 'michal.uryc@taktiq.com'){
							$role = $role3;
						}else{
							$role = $role4;
						}

					}

					if($company->getName() === 'DataConsult'){

						if($user->getEmail() === 'michal.uryc@taktiq.com'){
							$role = $role9;
						}else{
							$role = $role10;
						}

					}

					$userCompany = $this->createUserCompany($company, $user);
					$userCompanyTemporality = $this->createUserCompanyTemporality($userCompany, $role, $this->getReference("userCompanyTemporalityStatus1"));

					$manager->persist($userCompany);
					$manager->persist($userCompanyTemporality);

				}

			}

//			$userCompany1 = $this->createUserCompany($company1, $user1);
//			$userCompanyTemporality1 = $this->createUserCompanyTemporality($userCompany1, $role1, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany2 = $this->createUserCompany($company1, $user2);
//			$userCompanyTemporality2 = $this->createUserCompanyTemporality($userCompany2, $role2, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany3 = $this->createUserCompany($company1, $user3);
//			$userCompanyTemporality3 = $this->createUserCompanyTemporality($userCompany3, $role2, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany4 = $this->createUserCompany($company1, $user4);
//			$userCompanyTemporality4 = $this->createUserCompanyTemporality($userCompany4, $role2, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany5 = $this->createUserCompany($company1, $user5);
//			$userCompanyTemporality5 = $this->createUserCompanyTemporality($userCompany5, $role2, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany6 = $this->createUserCompany($company2, $user1);
//			$userCompanyTemporality6 = $this->createUserCompanyTemporality($userCompany6, $role3, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany7 = $this->createUserCompany($company2, $user2);
//			$userCompanyTemporality7 = $this->createUserCompanyTemporality($userCompany7, $role4, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany8 = $this->createUserCompany($company2, $user3);
//			$userCompanyTemporality8 = $this->createUserCompanyTemporality($userCompany8, $role4, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany9 = $this->createUserCompany($company2, $user4);
//			$userCompanyTemporality9 = $this->createUserCompanyTemporality($userCompany9, $role4, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany10 = $this->createUserCompany($company2, $user5);
//			$userCompanyTemporality10 = $this->createUserCompanyTemporality($userCompany10, $role4, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany11 = $this->createUserCompany($company3, $user6);
//			$userCompanyTemporality11 = $this->createUserCompanyTemporality($userCompany11, $role5, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany12 = $this->createUserCompany($company3, $user7);
//			$userCompanyTemporality12 = $this->createUserCompanyTemporality($userCompany12, $role6, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany13 = $this->createUserCompany($company1, $user8);
//			$userCompanyTemporality13 = $this->createUserCompanyTemporality($userCompany13, $role2, $this->getReference("userCompanyTemporalityStatus1"));
//
//			$userCompany14 = $this->createUserCompany($company4, $user9);
//			$userCompanyTemporality14 = $this->createUserCompanyTemporality($userCompany14, $role7, $this->getReference("userCompanyTemporalityStatus1"));


			//Company Groups
//			$members = array(
//				$user1, $user3, $user4, $user5
//			);
//			$companyGroup1 = $this->createCompanyGroup($company1, 'IT divize', $members);
//
//			$members = array(
//				$user1, $user5, $user8
//			);
//			$companyGroup2 = $this->createCompanyGroup($company1, 'Office management', $members);

			//Contacts
			$contact1 = $this->createContact('44947429', $this->getReference('contactType1'), $company1);
			$contact2 = $this->createContact('26055503', $this->getReference('contactType1'), $company2);
			$contact3 = $this->createContact('25788001', $this->getReference('contactType1'), $company5);
			$contact4 = $this->createContact('67985726', $this->getReference('contactType1'), $company1);
			$contact5 = $this->createContact('02748801', $this->getReference('contactType1'), $company2);
			$contact6 = $this->createContact('04208650', $this->getReference('contactType1'), $company5);
			$contact7 = $this->createContact('45280436', $this->getReference('contactType1'), $company1);

			$manager->persist($user1);
			$manager->persist($user2);
			$manager->persist($user3);
			$manager->persist($user4);
			$manager->persist($user5);
//			$manager->persist($user6);
//			$manager->persist($user7);
			$manager->persist($user8);
//			$manager->persist($user9);
			$manager->persist($user10);
			$manager->persist($user11);
			$manager->persist($user12);
			$manager->persist($user13);
			$manager->persist($user14);
			$manager->persist($user15);
			$manager->persist($user16);
			$manager->persist($user17);
			$manager->persist($user18);
			$manager->persist($company1);
			$manager->persist($company2);
//			$manager->persist($company3);
//			$manager->persist($company4);
			$manager->persist($company5);
			$manager->persist($role1);
			$manager->persist($role2);
			$manager->persist($role3);
			$manager->persist($role4);
//			$manager->persist($role5);
//			$manager->persist($role6);
//			$manager->persist($role7);
//			$manager->persist($role8);
			$manager->persist($role9);
			$manager->persist($role10);
//			$manager->persist($userCompany1);
//			$manager->persist($userCompany2);
//			$manager->persist($userCompany3);
//			$manager->persist($userCompany4);
//			$manager->persist($userCompany5);
//			$manager->persist($userCompany6);
//			$manager->persist($userCompany7);
//			$manager->persist($userCompany8);
//			$manager->persist($userCompany9);
//			$manager->persist($userCompany10);
//			$manager->persist($userCompany11);
//			$manager->persist($userCompany12);
//			$manager->persist($userCompany13);
//			$manager->persist($userCompany14);
//			$manager->persist($userCompanyTemporality1);
//			$manager->persist($userCompanyTemporality2);
//			$manager->persist($userCompanyTemporality3);
//			$manager->persist($userCompanyTemporality4);
//			$manager->persist($userCompanyTemporality5);
//			$manager->persist($userCompanyTemporality6);
//			$manager->persist($userCompanyTemporality7);
//			$manager->persist($userCompanyTemporality8);
//			$manager->persist($userCompanyTemporality9);
//			$manager->persist($userCompanyTemporality10);
//			$manager->persist($userCompanyTemporality11);
//			$manager->persist($userCompanyTemporality12);
//			$manager->persist($userCompanyTemporality13);
//			$manager->persist($userCompanyTemporality14);
//			$manager->persist($companyGroup1);
//			$manager->persist($companyGroup2);
			$manager->persist($contact1);
			$manager->persist($contact2);
			$manager->persist($contact3);
			$manager->persist($contact4);
			$manager->persist($contact5);
			$manager->persist($contact6);
			$manager->persist($contact7);
			$this->createActivitiesAndPersist($companies, $activities);
			$manager->flush();

		}

		/**
		 * Create new user based on input data and return it
		 *
		 * @param $email
		 * @param $firstname
		 * @param $lastname
		 * @param $password
		 *
		 * @return \UserBundle\Entity\User
		 */
		private function createUser($email, $firstname, $lastname, $password)
		{
			$user = $this->userFactory->createUser($email, $firstname, $lastname, $password);
			return $user;
		}

		private function createCompany($name, User $owner){
			$company = $this->companyFactory->createCompany($name, $owner);
			return $company;
		}

		private function createRole($name, Company $company, $noneditable, array $defaultAuthorizations){
			return $this->roleFactory->createRole($this->manager, $name, $company, $noneditable, $defaultAuthorizations);
		}

		private function createUserCompany(Company $company, User $user)
		{
			$userCompany = new UserCompany();
			$userCompany->setCompany($company);
			$userCompany->setUser($user);
			$userCompany->setCreated(new \DateTime());

			return $userCompany;
		}

		private function createUserCompanyTemporality(UserCompany $userCompany, Role $role, $status){

			return $this->userCompanyTemporalityFactory->createUserCompanyTemporality($userCompany, $role, $status, 0, 0, 0);

		}

		private function createCompanyGroup(Company $company, $name, array $members){

			$companyGroup = new CompanyGroup();
			$companyGroup
				->setEnabled(true)
				->setOwner($company->getOwner())
				->setCreated(new \DateTime())
				->setName($name)
				->setCompany($company);

			foreach($members as $member){

				$companyGroup->addMember($member);
				$member->addCompanyGroup($companyGroup);

			}

			return $companyGroup;

		}

		private function createContact($ico, ContactType $type, Company $company, ContactCountry $country = null){

			$contact = new Contact();
			$contact->setType($type);
			$contact->setCompany($company);
			$contact->setEnabled(true);
			$contact->setClosed(false);
			if($country === null){
				$criteria = [
					'iso' => 'CS'
				];
				$country = $this->manager->getRepository('AppBundle:ContactCountry')->findOneBy($criteria);
			}
			$contact->setCountry($country);

			$info = ContactInfoDownloader::getInfoAboutContact($ico);

			if($info['stav'] === 'ok'){

				$contact->setTitle($info['title']);
				$contact->setVatnumber($info['vatnumber']);
				$contact->setTaxnumber($info['taxnumber']);
				$contact->setStreet($info['street']);
				$contact->setNumber($info['number']);
				$contact->setCity($info['city']);
				$contact->setZipcode($info['zipcode']);

				return $contact;

			}

		}

		private function createActivitiesAndPersist($companies, $activities){

			foreach($companies as $company){

				foreach($activities as $activity){

					$activity = $this->activityFactory->createActivity($activity, $company);
					$this->manager->persist($activity);

				}

			}

		}

	}