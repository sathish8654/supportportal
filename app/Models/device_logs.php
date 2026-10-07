<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class device_logs extends Model
{
    use HasFactory;
    protected $table = 'device_logs';
    public $timestamps = false;
    protected $fillable = [
        'id', 
        'image', 
        'user_id', 
        'imei_id', 
        'gsm_module',
        'sim_scope',
        'lora_module',
        'lora_freq',
        'lora_channel',
        'lora_SF',
        'lora_joint',
        'master',
        'master_serial_no',
        'master_imei_no',
        'master_os',
        'repeter',
        'valve_type',
        'valve_qty',
        'butterfly_valve',
        'two_inch_valve',
        'two_5_inch_valve',
        'three_inch_valve',
        'four_inch_valve',
        'six_inch_valve',
        'eight_inch_valve',
        'ten_inch_valve',
        'cable_mtr',
        'cable_brand',
        'high_gain',
        // 'eb_power_3p',
        // 'eb_power_1p',
        'solar_power',
        'level_sensor',
        'presere_sensor',
        'mois_sensor',
        'npk',
        'ec',
        'ph',
        'all_in',
        'filter',
        'flow_sensor',
        'weather_monitor',
        'fert_sys',
        'tank_count',
        'agitator_tape',
        'valve_fitting',
        'injectTape',
        'install_date',
        'prog_folder',
        'install_actuator',
        'panel',
        'install_done',
        'sales_person',
        'location',
        'customized',
        'created_by',
        'created_at',
        'note',
    ];
}
