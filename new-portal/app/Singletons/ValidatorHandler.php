<?php

namespace App\Singletons;

/**
 * General Purpose Class: ValidatorHandler
 *
 * Singleton Class that will be responsible
 * by handle with ValidatorHandlers around of the program
 *
 */

use Closure;
use Illuminate\Validation\Validator;

const NONE = null;  // represents nothing
const SUCCESS = true;   // represents success
const FAIL = false; // represents fail

class ValidatorHandler {
  private static $validatorHandler = null;

  private $last_validation_result = NONE;

  public function handleThis($validator) {

    if($validator instanceof Validator)
      if($validator->fails())
        $this->last_validation_result = FAIL;
      else
        $this->last_validation_result = SUCCESS;

    return $this;
  }

  /**
   * Verify if the last validation has invalid inputs, if yes, redirects with given params
   * if not, returns this ValidatorHandler object
   *
   * @param string $redirect_url
   * @param array $params
   * @return null|\Singletons\ValidatorHandler
   */
  public function ifValidationFailsRedirect(string $redirect_url, array $params = []) {
    // verify if the last validation exists and if has fail
    if($this->last_validation_result !== NONE && $this->last_validation_result === FAIL) {
      return redirect($redirect_url)->with('params', $params);
    }

    // if has no failures occurred, returns null
    return null;
  }

  /**
   * Verify if the last validation has invalid inputs, if yes, executes the closure
   * if not, returns this ValidatorHandler object
   *
   * @param Closure $fail_function
   * @return null|\Singletons\ValidatorHandler
   */
  public function ifValidationFails(Closure $fail_function) {
    // verify if the last validation exists and if has fail
    if($this->last_validation_result != NONE && ! $this->last_validation_result)
      $fail_function();

    // if has no failures occurred, returns null
    return null;
  }

  public static function __callstatic($arg1, $arg2) {
    if(self::$validatorHandler === null)
      self::$validatorHandler = new ValidatorHandler();

    return self::$validatorHandler;
  }
}

?>
