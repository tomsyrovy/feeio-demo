<?php
/**
 * Project: feeio2
 * File: CRUDLDataObject.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 16.08.15
 * Version: 1.0
 */

namespace AppBundle\DataObject;

class CRUDLDataObject {

	/**
	 * @var mixed
	 */
	private $data;

	/**
	 * @var boolean
	 */
	private $canCreate;

	/**
	 * @var boolean
	 */
	private $canRead;

	/**
	 * @var boolean
	 */
	private $canUpdate;

	/**
	 * @var boolean
	 */
	private $canDelete;

	/**
	 * @var boolean
	 */
	private $canList;

	/**
	 * @return mixed
	 */
	public function getData(){
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData( $data ) {
		$this->data = $data;
	}

	/**
	 * @return boolean
	 */
	public function getCanCreate() {
		return $this->canCreate;
	}

	/**
	 * @param boolean $canCreate
	 */
	public function setCanCreate( $canCreate ) {
		$this->canCreate = $canCreate;
	}

	/**
	 * @return boolean
	 */
	public function getCanRead() {
		return $this->canRead;
	}

	/**
	 * @param boolean $canRead
	 */
	public function setCanRead( $canRead ) {
		$this->canRead = $canRead;
	}

	/**
	 * @return boolean
	 */
	public function getCanUpdate() {
		return $this->canUpdate;
	}

	/**
	 * @param boolean $canUpdate
	 */
	public function setCanUpdate( $canUpdate ) {
		$this->canUpdate = $canUpdate;
	}

	/**
	 * @return boolean
	 */
	public function getCanDelete() {
		return $this->canDelete;
	}

	/**
	 * @param boolean $canDelete
	 */
	public function setCanDelete( $canDelete ) {
		$this->canDelete = $canDelete;
	}

	/**
	 * @return boolean
	 */
	public function getCanList() {
		return $this->canList;
	}

	/**
	 * @param boolean $canList
	 */
	public function setCanList( $canList ) {
		$this->canList = $canList;
	}

}