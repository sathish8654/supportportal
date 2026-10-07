<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class v2_asetss extends Model
{
    use HasFactory;

    protected $table = 'v2_asetss';


    protected $fillable = [
        'id', 'time', 'updated_by_app', 'connection_status', 'clientip_port', 'imei', 'motor', 'phase', 'l1_volt', 'l2_volt', 'l3_volt', 'motor_status', 'l1_amps', 'l2_amps', 'l3_amps', 'runtime', 'dueto_low_volt', 'dueto_high_volt', 'dueto_different_volt', 'dueto_amps_problem', 'dueto_dry_load', 'dueto_overload', 'error_amps_l1', 'error_amps_l2', 'error_amps_l3', 'mode', 'auto_status_mobilemode', '1p_status_mobilemode', 'auto_status_manualmode', '1p_status_manualmode', 'cyclic_timer_status', 'present_cyclic_timer', 'set_cyclic_timer_on', 'set_cyclic_timer_off', 'run_timer_status', 'present_run_timer', 'set_run_timer', 'l1_error_volt', 'l2_error_volt', 'l3_error_volt', 'stop_timer', 'dryrun_restart_timer_status', 'present_dryrun_timer', 'set_dryrun_timer', 'double_motor_status', 'runtime_swap_status', 'dryrun_timer_swap_status', 'number_of_motors', 'signal_strength', 'valve_status', 'mobile_operator_name', 'modem_model', 'mobile_network_type', 'fertilizer_status', 'updated_columns'
    ];
}
