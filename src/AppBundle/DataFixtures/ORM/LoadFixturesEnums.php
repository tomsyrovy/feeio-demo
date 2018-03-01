<?php
	namespace AppBundle\DataFixtures\ORM;

	use AppBundle\Entity\Authorization;
	use AppBundle\Entity\CommissionUserCompanyRelationType;
	use AppBundle\Entity\CompanyGroupUserCompanyRelationTemporalityType;
	use AppBundle\Entity\ContactCountry;
	use AppBundle\Entity\ContactType;
	use AppBundle\Entity\Factory\ReportConfigurationFactory;
	use AppBundle\Entity\Factory\YearMonthFactory;
	use AppBundle\Entity\UserCompanyTemporalityStatus;
	use AppBundle\Entity\Widget;
	use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
	use Doctrine\Common\DataFixtures\AbstractFixture;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DependencyInjection\ContainerAwareInterface;
	use Symfony\Component\DependencyInjection\ContainerInterface;
	use Symfony\Component\Finder\Finder;
	use TableBundle\Entity\TableDefaultColumn;
	use TableBundle\Entity\TableEntity;

	class LoadFixturesEnums extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
	{

		/**
		 * @var ContainerInterface
		 */
		private $container;

		/**
		 * Get the order of this fixture
		 * @return integer
		 */
		public function getOrder() {
			return 1;
		}

		/**
		 * {@inheritDoc}
		 */
		public function load(ObjectManager $manager)
		{

			$ymFactory = new YearMonthFactory();

			$reportConfigurationFactory = new ReportConfigurationFactory();

			//Authorizations
//			$authorization1 = $this->createAuthorization("manage-company", "Správa společnosti");
			$authorization2 = $this->createAuthorization("contact-create", "Tvorba kontaktu");
			$authorization3 = $this->createAuthorization("contact-read", "Zobrazení detailu kontaktu");
			$authorization4 = $this->createAuthorization("contact-update", "Editace kontaktu");
			$authorization5 = $this->createAuthorization("contact-delete", "Odstranění kontaktu");
			$authorization6 = $this->createAuthorization("contact-list", "Zobrazení seznamu kontaktu");
			$authorization7 = $this->createAuthorization("companygroup-create", "Tvorba skupiny ve společnosti");
			$authorization8 = $this->createAuthorization("companygroup-update", "Editace skupiny ve společnosti");
			$authorization9 = $this->createAuthorization("commission-create", "Tvorba zakázky");
			$authorization10 = $this->createAuthorization("commission-admin-always", "Plná správa všech nových zakázek");
			$authorization11 = $this->createAuthorization("commission-observer-always", "Zobrazení všech nových zakázek");
			$authorization12 = $this->createAuthorization("timesheet-create", "Tvorba timesheetů");
			$authorization13 = $this->createAuthorization("timesheet-delete-own", "Odstranění vlastních timesheetů");
			$authorization14 = $this->createAuthorization("cost-manage", "Správa všech nákladů napříč všemi zakázkami společnosti");
			$authorization15 = $this->createAuthorization("report-timesheet_IN_people_VS_commissions", "Zobrazení reportu: Odpracovaný čas lidí na zakázkách");
			$authorization16 = $this->createAuthorization("client-create", "Tvorba klienta");
			$authorization17 = $this->createAuthorization("client-read", "Zobrazení detailu klienta");
			$authorization18 = $this->createAuthorization("client-update", "Editace klienta");
			$authorization19 = $this->createAuthorization("client-delete", "Odstranění klienta");
			$authorization20 = $this->createAuthorization("client-list", "Zobrazení seznamu klientů");

			//UserCompanyTemporalityStatuses
			$userCompanyTemporalityStatus1 = $this->createUserCompanyTemporalityStatus("enabled", "povolený", false);
			$this->setReference("userCompanyTemporalityStatus1", $userCompanyTemporalityStatus1);
			$userCompanyTemporalityStatus2 = $this->createUserCompanyTemporalityStatus("disabled", "zakázaný", false);

			//ContactTypes
			$contactType1 = $this->createContactType("odběratel", "subscriber");
			$this->setReference('contactType1', $contactType1);
			$contactType2 = $this->createContactType("dodavatel", "supplier");
			$this->setReference('contactType2', $contactType2);

			//ContactCountries
//			$contactCountry1 = $this->createContactCountry("CZ", "CZE", "Česká republika");
//			$this->setReference('contactCountry1', $contactCountry1);
//			$contactCountry2 = $this->createContactCountry("SK", "SVK", "Slovensko");
//			$this->setReference('contactCountry2', $contactCountry2);

			//CommissionUserCompanyRelationType
			$cucrt1 = $this->createCommissionUserCompanyRelationType('plná správa zakázky', 'admin');
			$cucrt2 = $this->createCommissionUserCompanyRelationType('pouze zobrazení zakázky', 'observer');

			//CompanyGroupUserCompanyRelationTemporalityType
			$cgucrtt1 = $this->createCompanyGroupUserCompanyRelationTemporalityType('Správce skupiny', 'admin');
			$cgucrtt2 = $this->createCompanyGroupUserCompanyRelationTemporalityType('Člen skupiny', 'member');

			//YearMonths
			$collection = $ymFactory->generateYears(2000, 2299);

			$widget1 = $this->createWidget('Odpracované hodiny (reál vs. plán)', 'timesheet_duration_real', 'Skutečně odpracované hodiny na zakázce v měsíci dle z vyplněných timesheetů versus plánované hodiny zakázky.', 'SELECT (SUM(t.duration)/60) AS result FROM AppBundle:Timesheet t WHERE t.commission = :commission AND t.yearmonth = :yearmonth GROUP BY t.commission, t.yearmonth', 'SELECT sct.hoursPlan AS result FROM AppBundle:SubcommissionTemporality sct JOIN sct.subcommission sc WHERE sct.dateUntil IS NULL AND sc.commission = :commission AND sc.yearmonth = :yearmonth', '&nbsp;hod');

			$table1 = $this->createTableEntity('table-subcommissions', 'Tabulka subzakázek');
			$t1col1 = $this->createTableColumn($table1, 'A0', 'Fixní fee (plán)', 'feeFixPlan');
			$t1col2 = $this->createTableColumn($table1, 'B0', 'Fixní fee (reál)', 'feeFixReal');
			$t1col3 = $this->createTableColumn($table1, 'C0', 'Success fee (plán)', 'feeSuccessPlan');
			$t1col4 = $this->createTableColumn($table1, 'D0', 'Success fee (reál)', 'feeSuccessReal');
			$t1col5 = $this->createTableColumn($table1, 'E0', 'Hodiny (plán)', 'hoursPlan');
			$t1col6 = $this->createTableColumn($table1, 'F0', 'Hodiny (reál)', 'hoursReal');
			$t1col7 = $this->createTableColumn($table1, 'G0', 'Hodinová externí sazba týmu', 'rateExternal');
			$t1col8 = $this->createTableColumn($table1, 'H0', 'Hodinová interní sazba týmu', 'rateInternal');
			$t1col9 = $this->createTableColumn($table1, 'I0', 'Subzakázková externí sazba (plán)', 'totalRateExternalPlan');
			$t1col10 = $this->createTableColumn($table1, 'J0', 'Subzakázková externí sazba (reál)', 'totalRateExternalReal');
			$t1col11 = $this->createTableColumn($table1, 'K0', 'Subzakázková interní sazba (plán)', 'totalRateInternalPlan');
			$t1col12 = $this->createTableColumn($table1, 'L0', 'Subzakázková interní sazba (reál)', 'totalRateInternalReal');
			$t1col13 = $this->createTableColumn($table1, 'M0', 'Hodiny týmu (plán)', 'team_hoursPlan');
			$t1col14 = $this->createTableColumn($table1, 'N0', 'Hodiny týmu (reál)', 'team_hoursReal');
			$t1col15 = $this->createTableColumn($table1, 'O0', 'Velikost týmu', 'members');

			$table2 = $this->createTableEntity('table-budgets', 'Tabulka rozpočtů');
			$t2col1 = $this->createTableColumn($table2, 'A0', 'Název', 'title');
			$t2col2 = $this->createTableColumn($table2, 'B0', 'Verze', 'version');
			$t2col3 = $this->createTableColumn($table2, 'C0', 'Částka celkem (plán)', 'priceSumPlan');
			$t2col4 = $this->createTableColumn($table2, 'D0', 'Částka celkem (reál)', 'priceSumReal');
			$t2col5 = $this->createTableColumn($table2, 'E0', 'Autor', 'author');
			$t2col6 = $this->createTableColumn($table2, 'F0', 'Datum vytvoření', 'created');

			$table3 = $this->createTableEntity('table-timesheetlist', 'Tabulka timesheetů při zakázce');
			$t3col1 = $this->createTableColumn($table3, 'A0', 'Časové určení', 'yearmont');
			$t3col2 = $this->createTableColumn($table3, 'B0', 'Celkový strávený čas', 'duration_sum');
			$t3col3 = $this->createTableColumn($table3, 'C0', 'Počet timesheetů', 'timesheet_count');

			$table4 = $this->createTableEntity('table-timesheetlistyearmonth', 'Tabulka timesheetů při zakázce a časovém určení');
			$t4col1 = $this->createTableColumn($table4, 'A0', 'Autor', 'author');
			$t4col2 = $this->createTableColumn($table4, 'B0', 'Datum', 'date');
			$t4col3 = $this->createTableColumn($table4, 'C0', 'Aktivita', 'activity');
			$t4col4 = $this->createTableColumn($table4, 'D0', 'Popis', 'description');
			$t4col5 = $this->createTableColumn($table4, 'E0', 'Strávený čas', 'duration');

			$table5 = $this->createTableEntity('table-companyusercompanytimesheetlist', 'Tabulka timesheetů při uživateli');
			$t5col1 = $this->createTableColumn($table5, 'A0', 'Časové určení', 'yearmonth');
			$t5col2 = $this->createTableColumn($table5, 'B0', 'Celkový strávený čas', 'duration_sum');
			$t5col3 = $this->createTableColumn($table5, 'C0', 'Počet timesheetů', 'timesheet_count');

			$table6 = $this->createTableEntity('table-companyusercompanytimesheetlistyearmonth', 'Tabulka timesheetů při uživateli a časovém určení');
			$t6col1 = $this->createTableColumn($table6, 'A0', 'Autor', 'author');
			$t6col2 = $this->createTableColumn($table6, 'B0', 'Datum', 'date');
			$t6col3 = $this->createTableColumn($table6, 'C0', 'Aktivita', 'activity');
			$t6col4 = $this->createTableColumn($table6, 'D0', 'Popis', 'description');
			$t6col5 = $this->createTableColumn($table6, 'E0', 'Strávený čas', 'duration');

			$table7 = $this->createTableEntity('table-costsofcommission', 'Tabulka nákladů při zakázce');
			$t7col1 = $this->createTableColumn($table7, 'A0', 'Zakázka', 'commission');
			$t7col2 = $this->createTableColumn($table7, 'B0', 'Název', 'title');
			$t7col3 = $this->createTableColumn($table7, 'C0', 'Dodavatel', 'supplier');
			$t7col4 = $this->createTableColumn($table7, 'D0', 'Datum uskutečnění (plán)', 'yearmonthPlan');
			$t7col5 = $this->createTableColumn($table7, 'E0', 'Nákupní cena bez DPH (plán)', 'priceNonVatPlan');
			$t7col6 = $this->createTableColumn($table7, 'F0', 'Sazba DPH nákupní ceny (plán)', 'vatRatePlan');
			$t7col7 = $this->createTableColumn($table7, 'G0', 'Prodejní cena bez DPH (plán)', 'rebillingPriceNonVatPlan');
			$t7col8 = $this->createTableColumn($table7, 'H0', 'Sazba DPH prodejní ceny (plán)', 'rebillingVatRatePlan');
			$t7col9 = $this->createTableColumn($table7, 'I0', 'Datum uskutečnění (reál)', 'yearmonthReal');
			$t7col10 = $this->createTableColumn($table7, 'J0', 'Nákupní cena bez DPH (reál)', 'priceNonVatReal');
			$t7col11 = $this->createTableColumn($table7, 'K0', 'Sazba DPH nákupní ceny (reál)', 'vatRateReal');
			$t7col12 = $this->createTableColumn($table7, 'L0', 'Prodejní cena bez DPH (reál)', 'rebillingPriceNonVatReal');
			$t7col13 = $this->createTableColumn($table7, 'M0', 'Sazba DPH prodejní ceny (reál)', 'rebillingVatRateReal');
			$t7col14 = $this->createTableColumn($table7, 'N0', 'Ev. číslo přijatého dokladu', 'receivedDocumentNumber');
			$t7col15 = $this->createTableColumn($table7, 'O0', 'Ev. číslo vydaného dokladu', 'issuedDocumentNumber');

			$fields = [
				'indicator' => 'timesheet',
				'fields' => [
					'ym_year',
					'ym_month',
					'u_fullname',
					'c_name',
					'cl_title',
					'co_name',
					'cg_name',
					't_duration',
					't_description'
				],
				'options' => [
					'convertToInt' => [
						't_duration'
					]
				]
			];
			$reportConfiguration1 = $reportConfigurationFactory->createReportConfiguration('report-timesheet_IN_people_VS_commissions', 'Odpracovaný čas lidí na zakázkách', 'Tabulka zobrazující odpracované hodiny vybraných lidí na vybraných zakázkách ve vybraném časovém období.', $fields);

//			$manager->persist($authorization1);
			$manager->persist($authorization2);
			$manager->persist($authorization3);
			$manager->persist($authorization4);
			$manager->persist($authorization5);
			$manager->persist($authorization6);
			$manager->persist($authorization7);
			$manager->persist($authorization8);
			$manager->persist($authorization9);
			$manager->persist($authorization10);
			$manager->persist($authorization11);
			$manager->persist($authorization12);
			$manager->persist($authorization13);
			$manager->persist($authorization14);
			$manager->persist($authorization15);
			$manager->persist($authorization16);
			$manager->persist($authorization17);
			$manager->persist($authorization18);
			$manager->persist($authorization19);
			$manager->persist($authorization20);
			$manager->persist($userCompanyTemporalityStatus1);
			$manager->persist($userCompanyTemporalityStatus2);
			$manager->persist($contactType1);
			$manager->persist($contactType2);
			$manager->persist($cucrt1);
			$manager->persist($cucrt2);
			$manager->persist($cgucrtt1);
			$manager->persist($cgucrtt2);
			$ymFactory->persistCollection($manager, $collection);
			$manager->persist($widget1);
			$manager->persist($table1);
			$manager->persist($t1col1);
			$manager->persist($t1col2);
			$manager->persist($t1col3);
			$manager->persist($t1col4);
			$manager->persist($t1col5);
			$manager->persist($t1col6);
			$manager->persist($t1col7);
			$manager->persist($t1col8);
			$manager->persist($t1col9);
			$manager->persist($t1col10);
			$manager->persist($t1col11);
			$manager->persist($t1col12);
			$manager->persist($t1col13);
			$manager->persist($t1col14);
			$manager->persist($t1col15);
			$manager->persist($table2);
			$manager->persist($t2col1);
			$manager->persist($t2col2);
			$manager->persist($t2col3);
			$manager->persist($t2col4);
			$manager->persist($t2col5);
			$manager->persist($t2col6);
			$manager->persist($table3);
			$manager->persist($t3col1);
			$manager->persist($t3col2);
			$manager->persist($t3col3);
			$manager->persist($table4);
			$manager->persist($t4col1);
			$manager->persist($t4col2);
			$manager->persist($t4col3);
			$manager->persist($t4col4);
			$manager->persist($t4col5);
			$manager->persist($table5);
			$manager->persist($t5col1);
			$manager->persist($t5col2);
			$manager->persist($t5col3);
			$manager->persist($table6);
			$manager->persist($t6col1);
			$manager->persist($t6col2);
			$manager->persist($t6col3);
			$manager->persist($t6col4);
			$manager->persist($t6col5);
			$manager->persist($table7);
			$manager->persist($t7col1);
			$manager->persist($t7col2);
			$manager->persist($t7col3);
			$manager->persist($t7col4);
			$manager->persist($t7col5);
			$manager->persist($t7col6);
			$manager->persist($t7col7);
			$manager->persist($t7col8);
			$manager->persist($t7col9);
			$manager->persist($t7col10);
			$manager->persist($t7col11);
			$manager->persist($t7col12);
			$manager->persist($t7col13);
			$manager->persist($t7col14);
			$manager->persist($t7col15);
			$manager->persist($reportConfiguration1);
			$manager->flush();

			$this->loadSQL('countries_cs.sql');

		}

		/**
		 * Sets the container.
		 *
		 * @param ContainerInterface|null $container A ContainerInterface instance or null
		 */
		public function setContainer( ContainerInterface $container = null ){
			$this->container = $container;
		}

		private function createAuthorization($code, $name){
			$authorization = new Authorization();
			$authorization->setCode($code);
			$authorization->setName($name);

			return $authorization;
		}

		private function createUserCompanyTemporalityStatus($code, $name, $automation){

			$ucts = new UserCompanyTemporalityStatus();
			$ucts->setCode($code);
			$ucts->setName($name);
			$ucts->setAutomation($automation);

			return $ucts;

		}

		private function createContactType($title, $code){
			$contactType = new ContactType();
			$contactType->setCode($code);
			$contactType->setTitle($title);

			return $contactType;
		}

		private function createCommissionUserCompanyRelationType($name, $code){
			$cucrt = new CommissionUserCompanyRelationType();
			$cucrt->setName($name);
			$cucrt->setCode($code);

			return $cucrt;
		}

		private function createCompanyGroupUserCompanyRelationTemporalityType($name, $code){
			$cgucrtt = new CompanyGroupUserCompanyRelationTemporalityType();
			$cgucrtt->setName($name);
			$cgucrtt->setCode($code);

			return $cgucrtt;
		}

		private function createWidget($name, $code, $description, $dql, $dql2 = null, $unit = null){
			$entity = new Widget();
			$entity->setName($name);
			$entity->setCode($code);
			$entity->setDQL($dql);
			$entity->setDQL2($dql2);
			$entity->setDescription($description);
			$entity->setUnit($unit);

			return $entity;
		}

		private function createContactCountry($iso, $iso3, $name){
			$contactCountry = new ContactCountry();
			$contactCountry->setIso($iso);
			$contactCountry->setIso3($iso3);
			$contactCountry->setName($name);

			return $contactCountry;
		}

		private function createTableEntity($code, $title){

			$tableEntity = new TableEntity();
			$tableEntity->setCode($code);
			$tableEntity->setTitle($title);

			return $tableEntity;

		}

		private function createTableColumn(TableEntity $tableEntity, $code, $title, $property){

			$tableColumn = new TableDefaultColumn();
			$tableColumn->setCode($code);
			$tableColumn->setTitle($title);
			$tableColumn->setTableEntity($tableEntity);
			$tableColumn->setProperty($property);

			return $tableColumn;

		}

		private function loadSQL($fileName){

			$finder = new Finder();
			$finder->in('src/AppBundle/DataFixtures/Data');
			$finder->name($fileName);

			foreach($finder as $file){

				$content = $file->getContents();

				$stmt = $this->container->get('doctrine.orm.entity_manager')->getConnection()->prepare($content);
				$stmt->execute();

			}

		}

	}