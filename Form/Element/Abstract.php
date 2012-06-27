<?php

/**
 * Unus
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://nwhiting.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@nwhiting.com so we can send you a copy immediately.
 *
 * DO NOT MODIFY this files contents if you wish to upgrade Unus in the future,
 * If there is a bug with this file address them at http://www.nwhiting.com/
 * so we can include this fix for future releases.
 *
 * For improvements please address them at http://www.nwhiting.com/
 * they will be greatly appreciated, while it is not required it would be good
 * to contribute. HAVE FUN and HAPPY CODING
 *
 */



/**
 * @category   Unus
 * @package    Unus
 * @version    $Rev: 1$
 * @author     Nickolas Whiting <admin@nwhiting.com>
 * @copyright  Copyright 2009 Nickolas Whiting
 */

abstract class Unus_Form_Element_Abstract extends Unus_Data implements Unus_Form_Element_Interface
{
	// We Dont need any parsing
    public function __construct()
    {}

	protected function _setType($type)
	{
		$this->setData('type', $type);
	}

	/**
	* Sets the name for element
	*
	* @param  string  name  Name of element
	*
	* @returns this
	**/

	public function setName($name)
	{
		$this->setData('name', $name);
		return $this;
	}

	/**
	* Returns the name of element
	*
	* @return  string
	**/

	public function getName()
	{
		return $this->getData('name');
	}

	/**
	* Returns the type of element input|select etc..
	*
	* @return  string
	**/

	public function getType()
	{
		return $this->getData('type');
	}

	/**
	* Returns the ID of the element
	*
	* @return  string
	**/

	public function getId()
	{
		return $this->getData('id');
	}

	/**
     * Sets the id for element, left blank the name will be used
     *
     * @param  mixed  string  Id of element
     *
     * @return  this
     */

    public function setId($str)
    {
        $this->setData('id', $str);
        return $this;
    }

	/**
	* Sets the label for element
	*
	* @param  string  $label  Label for element
	*
	* @return  string
	**/

	public function setLabel($label)
	{
		$this->setData('label', $label);
		return $this;
	}

	/**
     * Returns the element label
     *
     * @return  string
     */

    public function getLabel()
    {
        return $this->getData('label');
    }

	/**
     * Sets a element config option
     */
    public function setConfig($option, $value)
    {
		$options = $this->getData('options');

		$options[$option] = $value;

        $this->setData('options', $options);

        return $this;
    }

    /**
     * Sets element options
     */
    public function setConfigArray($array)
    {
		$options = $this->getData('options');

        if (!is_array($array)) {
            throw new Unus_Form_Element_Exception('setOptions() expected array '.gettype($array).' given');
        }

        foreach ($array as $k => $v) {
            $options[$k] = $v;
        }

        $this->setData('options', $options);

        return $this;
    }

	/**
	 * Sets element as required an adds validator
	 *
	 */

	public function addValidator($validator = null)
	{
		if (null == $this->getData('validate')) {
			$this->setData('validate', new Unus_Form_Validate($this));
			$this->setData('required', true);
		}

		if (null == $validator) {
            // automatically add the validator for this option as required by default
            $validator = new Unus_Form_Validator_Required();
        } else {
            $this->getData('validate')->addValidator($validator);
        }

		return $this;
	}

	public function addValidators($validators)
	{
		if (is_array($validators)) {
			foreach ($validators as $k => $v) {
				$this->addValidator($v);
			}
			return $this;
		}
		throw new Unus_Form_Element_Exception('addValidators() expected array input, recieved '.gettype($validators).'; To set a single validator use the plural method addValidator()');
	}

	/**
	 * Sets validation for jQuery Validate
	 *
	 * This will not validate information once the form is submitted server-side
	 * it is recommended to use this ONLY if you are 100% sure
	 * all users will have javascript enabled
	 *
	 * @param  type  name  desc
	 *
	 * @return
	 */
	public function setjQueryValidator($required = null, $messsages = null)
	{
		$jQuery = Unus_Form_jQuery_Validator::getInstance();
		$name = $this->getName();

		if (null == $required || $required == true) {
			$jQuery->addElement($name, array('required' => 'true'));
		}

		if (null != $messages) {
			if (!is_array($messages)) {
				$jQuery->addMessage($name, array('required' => __($messages)));
			} else {
				foreach ($messages as $k => $v) {
					$jQuery->addMessage($name, array('required' => __($v)));
				}
			}
		}

		return $this;
	}

	/**
     * Overloading
     *
     * Overloads calls to set/get element attributes
     *
     * Currently overloads:
     *
     * - get(Attribute)()
     * - set(Attribute)()
     *
     * @param  string $method
     * @param  array  $args
     *
     * @return mixed
     * @throws Unus_Form_Element_Exception for unsupported methods
     */
    public function __call($method, $args)
    {
        $tail = strtolower(substr($method, 3));
        $head = substr($method, 0, 3);

		if (in_array($head, array('get', 'set'))) {
			switch($head) {
				case 'set':
					if (0 === count($args)) {
                        throw new Unus_Form_Element_Exception(sprintf('Method "%s" requires at least one argument; none provided', $method));
                    }
					$this->setData('options', array($tail => $args[0]), true);
					return $this;
					break;
				case 'get':
					return $this->getData('options/'.$tail);
					break;
				default:
					throw new Unus_Form_Element_Exception(sprintf('Invalid method called "%s" for element "%s" attributes', $method, $this->getName()));
					break;
			}
		}

		throw new Unus_Form_Element_Exception(sprintf('Invalid method called "%s" for element "%s" attributes', $method, $this->getName()));
    }

	/**
     * Overloading
     *
     * Overloads gets to return element attributes
     *
     *
     * @param  string $name
     *
     * @return mixed
     */
	public function __get($name)
	{
		return $this->getData('options/'.$name);
	}

	/**
     * Overloading
     *
     * Overloads gets to return element attributes
     *
     *
     * @param  string $name
     *
     * @return mixed
     */
	public function __set($name, $value)
	{
		$this->setData('options', array($name => $value), true);
	}
}
