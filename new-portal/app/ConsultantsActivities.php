<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class ConsultantsActivities extends Model
{
    /**
     * Defines the model's table name
     *
     * @var string $table
     */
    public $table = 'ConsultantsActivities';

    /**
     * Defines the model's fillable properties
     *
     * @var array $fillable
     */
    public $fillable = [
        'description', 'table_affected', 'row_affected',
        'id_consultant', 'activity_type'
    ];

    /**
     * Defines model's timestamps
     *
     * @var boolean $timestamps
     */
    public $timestamps = false;

    /**
     * Defines model's dates
     *
     * @var array $dates
     */
    public $dates = [
        'created_at'
    ];

    /**
     * Defines the new name for 'CREATED_AT' model's property
     *
     * @var string CREATED_AT
     */
    const CREATED_AT = 'accomplished_at';

    /**
     * Generates a activity by the params given
     *
     * @return \App\ConsultantsActivities
     */
    public static function generateActivity(string $table_affected,
                                       string $row_affected,
                                       string $activity_type,
                                       int $id_consultant) {
        // put all params data into an array
        $activity_data = [
            'table_affected' => $table_affected,
            'row_affected' => $row_affected,
            'activity_type' => $activity_type,
            'id_consultant' => $id_consultant,
        ];

        // generate the description
        $activity_data['description'] = self::generateDescription($activity_data);

        // makes the validation data to view if it's correctly inputed
        $validation_result = self::validator($activity_data);

        // if not, return the validation result
        if($validation_result->fails())
            return $validation_result;

        // return the activity instance
        return self::create($activity_data);
    }

    /**
     * Generates a new description with the array activity_data
     *
     * @param array $activity_data
     * $activity_data has to have the 'table_affected', 'row_affected
     * 'activity_type' and 'id_consultant' values to generate the description
     *
     * @return string|null
     */
    public static function generateDescription(array $activity_data) {
        if(! array_key_exists('activity_type', $activity_data))
            return ;

        switch ($activity_data['activity_type']) {
            case '__activity_contract_creation':
                return self::getCreationDescription('contract', $activity_data);

            case '__activity_contract_disabled':
                return self::getDisableDescription('contract', $activity_data);

            case '__activity_withdrawn_creation':
                return self::getCreationDescription('withdrawn', $activity_data);

            case '__activity_withdrawn_disabled':
                return self::getDisableDescription('withdrawn', $activity_data);

            case '__activity_yield_approvation':
                return self::getApprovationDescription('yield', $activity_data);

            case '__activity_yield_disabled':
                return self::getDisableDescription('yield', $activity_data);

            default:
                throw new Exception('the activity type passed doesn\'t exists');
                break;
        }
    }

    /**
     * Validates the activity's data
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validator(array $data) {
        return Validator::make($data, [
            'description' => ['bail', 'required', 'string', 'max:300'],
            'table_affected' => ['bail', 'required', 'string', 'exists:information_schema.table,table_name'],
            'row_affected' => ['bail', 'required', 'integer', "exists:${data['table_affected']},id"],
            'id_consultant' => ['bail', 'required', 'integer', 'exists:Consultants,id'],
            'activity_type' => ['bail', 'required', 'string'],
        ]);
    }

    /**
     * Gets the creation description with the array data
     * passed by param and with the object type name
     *
     * @param string $object_type_name
     * @param array $activity_data
     * @return string
     */
    public static function getCreationDescription(string $object_type_name, array $activity_data) {
        return self::getGenericDescription($object_type_name, $activity_data, 'created');
    }

    /**
     * Gets the disable description with the array data
     * passed by param and with the object type name
     *
     * @param string $object_type_name
     * @param array $activity_data
     * @return string
     */
    public static function getDisableDescription(string $object_type_name, array $activity_data) {
        return self::getGenericDescription($object_type_name, $activity_data, 'disabled');
    }

    /**
     * Gets the approvation description with the array data
     * passed by param and with the object type name
     *
     * @param string $object_type_name
     * @param array $activity_data
     * @return string
     */
    public static function getApprovationDescription(string $object_type_name, array $activity_data) {
        return self::getGenericDescription($object_type_name, $activity_data, 'approved');
    }

    /**
     * Gets a generic activity description
     * with the params passed
     *
     * @param string $object_type_name
     * @param array $activity_data
     * @param string $action
     */
    public static function getGenericDescription(
        string $object_type_name,
        array $activity_data,
        string $action
        ) {
        return 'the ' . $object_type_name . ' ' . $activity_data['row_affected'] .
        ' has been ' . $action . ' in the current date by consultant' . $activity_data['id_consultant'];
    }
}
