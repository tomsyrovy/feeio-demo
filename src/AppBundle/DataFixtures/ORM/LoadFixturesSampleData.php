<?php
//	namespace AppBundle\DataFixtures\ORM;
//
//	use AppBundle\DependencyInjection\ContactInfoDownloader;
//	use AppBundle\Entity\Company;
//	use AppBundle\Entity\Contact;
//	use AppBundle\Entity\ContactCountry;
//	use AppBundle\Entity\ContactType;
//	use AppBundle\Entity\Factory\ActivityFactory;
//	use AppBundle\Entity\Factory\CompanyFactory;
//	use AppBundle\Entity\Factory\RoleFactory;
//	use AppBundle\Entity\Factory\UserCompanyTemporalityFactory;
//	use AppBundle\Entity\Factory\UserFactory;
//	use AppBundle\Entity\Role;
//	use AppBundle\Entity\UserCompany;
//	use Doctrine\Common\DataFixtures\AbstractFixture;
//	use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
//	use Doctrine\Common\Persistence\ObjectManager;
//	use UserBundle\Entity\User;
//
//	class LoadFixturesSampleData extends AbstractFixture implements OrderedFixtureInterface
//	{
//
//		/**
//		 * @var ObjectManager
//		 */
//		private $manager;
//
//		/**
//		 * @var UserFactory
//		 */
//		private $userFactory;
//
//		/**
//		 * @var RoleFactory
//		 */
//		private $roleFactory;
//
//		/**
//		 * @var UserCompanyTemporalityFactory
//		 */
//		private $userCompanyTemporalityFactory;
//
//		/**
//		 * @var CompanyFactory
//		 */
//		private $companyFactory;
//
//		/**
//		 * @var ActivityFactory
//		 */
//		private $activityFactory;
//
//		/**
//		 * Get the order of this fixture
//		 * @return integer
//		 */
//		public function getOrder() {
//			return 2;
//		}
//
//		/**
//		 * {@inheritDoc}
//		 */
//		public function load(ObjectManager $manager)
//		{
//
//			$this->manager = $manager;
//			$this->userFactory = new UserFactory($this->manager);
//			$this->roleFactory = new RoleFactory();
//			$this->userCompanyTemporalityFactory = new UserCompanyTemporalityFactory();
//			$this->companyFactory = new CompanyFactory();
//			$this->activityFactory = new ActivityFactory();
//
//			//Users
//			$user1 = $this->createUser('demo@feeio.com', 'Demo', 'Demo');
//			$user2 = $this->createUser();
//			$user3 = $this->createUser();
//			$user4 = $this->createUser();
//			$user5 = $this->createUser();
//			$user8 = $this->createUser();
//			$user10 = $this->createUser();
//			$user11 = $this->createUser();
//			$user12 = $this->createUser();
//			$user13 = $this->createUser();
//			$user14 = $this->createUser();
//			$user15 = $this->createUser();
//			$user16 = $this->createUser();
//			$user17 = $this->createUser();
//			$user18 = $this->createUser();
//
//			//Companies + owners
//			$company1 = $this->createCompany("Moje dobrá společnost", $user1);
//			$company2 = $this->createCompany("Moje ještě lepší společnost", $user1);
//
//
//			//Activities
//			$companies = array(
//				$company1,
//				$company2,
//			);
//			$activities = array(
//				'Komunikace s klientem',
//				'Interní komunikace',
//				'Komunikace s médii',
//				'Komunikace s třetí stranou',
//				'Tvorba textu',
//				'Editace / korektura textu',
//				'Překlad textu',
//				'Mítink s klientem',
//				'Interní mítink',
//				'Mítink s médii',
//				'Mítink s třetí stranou',
//				'Podklady',
//				'Správa sociálních médií',
//				'Plánování / strategie sociálních médií',
//				'Web',
//				'Kreativita',
//				'Cestování',
//				'Monitoring',
//				'Média',
//				'Akce',
//			);
//
//			//Roles + authorizations
//
//			$defaultAuthorizations = $this->manager->getRepository("AppBundle:Authorization")->findAll();
//
//			$role1 = $this->createRole("Vlastník (superadmin)", $company1, true, $defaultAuthorizations);
//			$role2 = $this->createRole("Člen společnosti", $company1, false, array());
//
//			$role3 = $this->createRole("Vlastník (superadmin)", $company2, true, $defaultAuthorizations);
//			$role4 = $this->createRole("Člen společnosti", $company2, false, array());
//
//			//UserCompany + UserCompanyTemporality
//			$users2 = array(
//				$user1,
//				$user2,
//				$user3,
//				$user4,
//				$user5,
//				$user8,
//				$user10,
//				$user11,
//				$user12,
//				$user13,
//				$user14,
//				$user15,
//				$user16,
//				$user17,
//				$user18
//			);
//			$companies2 = array(
//				$company1,
//				$company2,
//			);
//			foreach($users2 as $user){
//
//				foreach($companies2 as $company){
//
//					if($company->getName() === 'Moje dobrá společnost'){
//
//						if($user->getEmail() === 'demo@feeio.com'){
//							$role = $role1;
//						}else{
//							$role = $role2;
//						}
//
//					}
//
//					if($company->getName() === 'Moje ještě lepší společnost'){
//
//						if($user->getEmail() === 'demo@feeio.com'){
//							$role = $role3;
//						}else{
//							$role = $role4;
//						}
//
//					}
//
//					$userCompany = $this->createUserCompany($company, $user);
//					$userCompanyTemporality = $this->createUserCompanyTemporality($userCompany, $role, $this->getReference("userCompanyTemporalityStatus1"));
//
//					$manager->persist($userCompany);
//					$manager->persist($userCompanyTemporality);
//
//				}
//
//			}
//
//			$criteria = [
//				'iso' => 'CS'
//			];
//			$country = $this->manager->getRepository('AppBundle:ContactCountry')->findOneBy($criteria);
////			Contacts
//			$contact1 = $this->createContact('44947429', $this->getReference('contactType1'), $company1, $country);
//			$contact2 = $this->createContact('26055503', $this->getReference('contactType1'), $company1, $country);
//			$contact3 = $this->createContact('25788001', $this->getReference('contactType1'), $company1, $country);
//			$contact4 = $this->createContact('67985726', $this->getReference('contactType1'), $company1, $country);
//			$contact5 = $this->createContact('02748801', $this->getReference('contactType2'), $company1, $country);
//			$contact6 = $this->createContact('04208650', $this->getReference('contactType2'), $company1, $country);
//			$contact7 = $this->createContact('45280436', $this->getReference('contactType1'), $company1, $country);
//
//			$manager->persist($user1);
//			$manager->persist($user2);
//			$manager->persist($user3);
//			$manager->persist($user4);
//			$manager->persist($user5);
//			$manager->persist($user8);
//			$manager->persist($user10);
//			$manager->persist($user11);
//			$manager->persist($user12);
//			$manager->persist($user13);
//			$manager->persist($user14);
//			$manager->persist($user15);
//			$manager->persist($user16);
//			$manager->persist($user17);
//			$manager->persist($user18);
//			$manager->persist($company1);
//			$manager->persist($company2);
//			$manager->persist($role1);
//			$manager->persist($role2);
//			$manager->persist($role3);
//			$manager->persist($role4);
//			$manager->persist($contact1);
//			$manager->persist($contact2);
//			$manager->persist($contact3);
//			$manager->persist($contact4);
//			$manager->persist($contact5);
//			$manager->persist($contact6);
//			$manager->persist($contact7);
//			$this->createActivitiesAndPersist($companies, $activities);
//			$manager->flush();
//
//		}
//
//		/**
//		 * Create new user based on input data and return it
//		 *
//		 * @param $email
//		 * @param $firstname
//		 * @param $lastname
//		 * @param $password
//		 *
//		 * @return \UserBundle\Entity\User
//		 */
//		private function createUser($email = null, $firstname = null, $lastname = null, $password = '12345678')
//		{
//
//			if($email === null and $firstname === null and $lastname === null){
//				$gender = 'male';
//				if(random_int(0, 100) > 50){
//					$gender = 'female';
//				}
//
//				$name = $this->getName($gender);
//
//				list($firstname, $lastname) = explode(' ', $name);
//
//				$email = strtolower($this->webalize($firstname)."@".$this->webalize($lastname).".cz");
//			}
//
//			$user = $this->userFactory->createUser($email, $firstname, $lastname, $password);
//
//			return $user;
//		}
//
//		private function createCompany($name, User $owner){
//			$company = $this->companyFactory->createCompany($name, $owner);
//			return $company;
//		}
//
//		private function createRole($name, Company $company, $noneditable, array $defaultAuthorizations){
//			return $this->roleFactory->createRole($this->manager, $name, $company, $noneditable, $defaultAuthorizations);
//		}
//
//		private function createUserCompany(Company $company, User $user)
//		{
//			$userCompany = new UserCompany();
//			$userCompany->setCompany($company);
//			$userCompany->setUser($user);
//			$userCompany->setCreated(new \DateTime());
//
//			return $userCompany;
//		}
//
//		private function createUserCompanyTemporality(UserCompany $userCompany, Role $role, $status){
//
//			return $this->userCompanyTemporalityFactory->createUserCompanyTemporality($userCompany, $role, $status, 0, 0, 0);
//
//		}
//
//		private function createContact($ico, ContactType $type, Company $company, ContactCountry $country){
//
//			$contact = new Contact();
//			$contact->setType($type);
//			$contact->setCompany($company);
//			$contact->setCountry($country);
//
//			$info = ContactInfoDownloader::getInfoAboutContact($ico);
//
//			if($info['stav'] === 'ok'){
//
//				$contact->setTitle($info['title']);
//				$contact->setVatnumber($info['vatnumber']);
//				$contact->setTaxnumber($info['taxnumber']);
//				$contact->setStreet($info['street']);
//				$contact->setNumber($info['number']);
//				$contact->setCity($info['city']);
//				$contact->setZipcode($info['zipcode']);
//
//				return $contact;
//
//			}
//
//		}
//
//		private function createActivitiesAndPersist($companies, $activities){
//
//			foreach($companies as $company){
//
//				foreach($activities as $activity){
//
//					$activity = $this->activityFactory->createActivity($activity, $company);
//					$this->manager->persist($activity);
//
//				}
//
//			}
//
//		}
//
//		private function getName($gender){
//
//			$names = array(
//				"male" => array(
//					"Roman Zbudil",
//					"Martin Műller",
//					"Luboš Václavík",
//					"Tomáš Tomaštík",
//					"Petr Beran",
//					"Václav Hála",
//					"Josef Hrabčák",
//					"Rudolf Darvaš",
//					"Karel Kolla",
//					"Miloslav Mašek",
//					"Jiří Sousedík",
//					"Jan Slavíček",
//					"Oskar Boštík",
//					"Lukáš Dvořák",
//					"Libor Rychna",
//					"Zdeněk Šrůtka",
//					"Jindřich Linsmaier",
//					"Vladimír Červený",
//					"Pavel Bukovský",
//					"Miroslav Meloun",
//					"David Ščudla",
//					"Jaroslav Kučera",
//					"František Holub",
//					"Antonín Bernášek",
//					"Miloš Pláteník",
//					"Jakub Hink",
//					"Vasyl Válek",
//					"Ferdinand Štětina",
//					"Stanislav Bažík",
//					"Michal Papež",
//					"Volodymyr Hermann",
//					"Milan Silvestr",
//					"Oldřich Okapal",
//					"Yuan Bednář",
//					"Richard Bauer",
//					"Filip Nádeníček",
//					"Radek Fikejz",
//					"Mykhaylo Bartoš",
//					"Kamil Kolman",
//					"Ladislav Vorel",
//					"Jaromír Vencl",
//					"Ondřej Vokurka",
//					"Tibor Samek",
//					"Vojtěch Jareš",
//					"Ralph Alan Luca",
//					"Vlastimil Kaněra",
//					"Adam Sedlák",
//					"Dominik Pelíšek",
//					"Ivan Havrlant",
//					"Kurt Bůžek",
//					"Marek Souček",
//					"Rostislav Farský",
//					"Vladislav Novák",
//					"Adolf Žižka",
//					"Daniel Vašátko",
//					"Luděk Hubáček",
//					"Ivo Dubský",
//					"Max Ševčík",
//					"Aleš Ježek",
//					"Dušan Dostál",
//					"Bohdan Kalmus",
//					"Anh Phi Odstrčil",
//					"Matěj Dirda",
//					"Lubomír Valdman",
//					"Bohumil Štěpán",
//					"Ján Oudrnický",
//					"Matyáš Jan Urban",
//					"Dalibor Humhal",
//					"Adrian Mareš",
//					"Viktor Štarha",
//					"Radko David",
//					"Patrik Dittrich",
//					"Emil Vilímovský",
//					"Eduard Marek",
//					"Bohuslav Trojan",
//					"Břetislav Beňo",
//					"Erik Petrásek",
//					"Andriy Zavadil",
//					"Robert Tyle",
//					"Otakar Cícha",
//					"Alexandr Vlasák",
//					"Radim Marcin",
//					"Gheorghe Hrabkovský",
//					"Jozef Verner",
//					"Wlodzimierz Crha",
//					"Igor Zástěra",
//					"Bedřich Volný",
//					"Oto Zeman",
//					"Loc Hanek",
//					"Štěpán Pospíchal",
//					"Sergey Hůry",
//					"Radomír Cepek",
//					"Erich Vojtíšek",
//					"Vratislav Chromý",
//					"Viliam Švandrlík",
//					"Vít Bureš",
//					"Alois Rod",
//					"Alexej Levý",
//					"Ludvík Šenk",
//					"Karol Frelich",
//				),
//				"female" => array(
//					"Květuše Vomelová",
//					"Aneta Boušková",
//					"Tereza Haubertová",
//					"Radka Hegerová",
//					"Hana Konývková",
//					"Veronika Soldátová",
//					"Barbora Kolářová",
//					"Olga Dlasková",
//					"Argentina Havířová",
//					"Kateřina Vajglová",
//					"Marie Kadlecová",
//					"Jana Peterová",
//					"Dana Wolfová",
//					"Petra Dokoupilová",
//					"Věra Mirgová",
//					"Martina Kovařičová",
//					"Ludmila Černá",
//					"Pavlína Petrášková",
//					"Denisa Koplová",
//					"Anna Nohavcová",
//					"Helena Grunová",
//					"Markéta Fořtová",
//					"Eva Przeczková",
//					"Alena Kopecká",
//					"Zuzana Ptáčková",
//					"Lucie Palánová",
//					"Dagmar Tremmelová",
//					"Elfrieda Buková",
//					"Anastázie Blahušová",
//					"Zdeňka Krasnodebská",
//					"Miroslava Skupinová",
//					"Milada Kodetová",
//					"Lenka Štěpánková",
//					"Vlasta Raková",
//					"Věroslava Petrusová",
//					"Pavla Lechová",
//					"Adela Sinová",
//					"Monika Parýsková",
//					"Erika Harantová",
//					"Vendula Hladká",
//					"Jaroslava Bažantová",
//					"Jitka Bayerová",
//					"Drahomíra Šulcová",
//					"Jarmila Brázdová",
//					"Božena Bidlová",
//					"Květa Valíčková",
//					"Eliška Miková",
//					"Jiřina Rysková",
//					"Michaela Frouzová",
//					"Růžena Piksová",
//					"Bára Loucká",
//					"Ivana Lišková",
//					"Bohdana Vaníčková",
//					"Libuše Žywiaková",
//					"Renata Soldánová",
//					"Pravoslava Sýkorová",
//					"Marcela Kocurová",
//					"Mariana Dvořáková",
//					"Štěpánka Kozáková",
//					"Xenie Mazancová",
//					"Rina Karásková",
//					"Ilona Šváchová",
//					"Andrea Kostrhounová",
//					"Zdenka Pártlová",
//					"Vilma Nováková",
//					"Vladimíra Vejmolová",
//					"Miluška Maryšková",
//					"Irena Luhanová",
//					"Kristýna Rucká",
//					"Hilda Šestáková",
//					"Iveta Zárubová",
//					"Ingrid Hartvichová",
//					"Leona Miksová",
//					"Marta Hozáková",
//					"Daria Zbořilová",
//					"Katrin Trnková",
//					"Emilie Holeňová",
//					"Gabriela Krištová",
//					"Naděžda Meyerová",
//					"Šárka Laňková",
//					"Miriam Kociánová",
//					"Josefa Morozova",
//					"Mirka Novotná",
//					"Klára Pastorčáková",
//					"Jaromíra Forejtová",
//					"Eugenie Kovačíková",
//					"Adriana Virtová",
//					"Romana Beňušová",
//					"Ladislava Korciánová",
//					"Vanesa Jochymková",
//					"Nikola Jalovecká",
//					"Stanislava Dušková",
//					"Blanka Ostřížková",
//					"Iva Koubová",
//					"Jena Milotová",
//					"Danuše Montagová",
//					"Adéla Stejskalová",
//					"Taťána Radovnická",
//					"Melanie Stachová",
//					"Simona Moravcová",
//				)
//			);
//
//			return $names[$gender][random_int(0, count($names[$gender])-1)];
//
//		}
//
//		/**
//		 * @param string $title
//		 * @return string
//		 */
//		private function webalize($title) {
//			static $convertTable = array (
//				'á' => 'a', 'Á' => 'A', 'ä' => 'a', 'Ä' => 'A', 'č' => 'c',
//				'Č' => 'C', 'ď' => 'd', 'Ď' => 'D', 'é' => 'e', 'É' => 'E',
//				'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'í' => 'i',
//				'Í' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ľ' => 'l', 'Ľ' => 'L',
//				'ĺ' => 'l', 'Ĺ' => 'L', 'ň' => 'n', 'Ň' => 'N', 'ń' => 'n',
//				'Ń' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ö' => 'o', 'Ö' => 'O',
//				'ř' => 'r', 'Ř' => 'R', 'ŕ' => 'r', 'Ŕ' => 'R', 'š' => 's',
//				'Š' => 'S', 'ś' => 's', 'Ś' => 'S', 'ť' => 't', 'Ť' => 'T',
//				'ú' => 'u', 'Ú' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ü' => 'u',
//				'Ü' => 'U', 'ý' => 'y', 'Ý' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y',
//				'ž' => 'z', 'Ž' => 'Z', 'ź' => 'z', 'Ź' => 'Z',
//			);
//			$title = strtolower(strtr($title, $convertTable));
//			$title = preg_replace('/[^a-zA-Z0-9]+/u', '-', $title);
//			$title = str_replace('--', '-', $title);
//			$title = trim($title, '-');
//			return $title;
//		}
//
//	}