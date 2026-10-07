<div id="imeiPrintPage">
<style>
   /* * {
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    } */
    /* body {
        margin: 0;
        padding: 20px;
        background: #f5f5f5;
    } */

    .print-page {
        width: 100%;
        max-width: 210mm;           /* A4 */
        min-height: 297mm;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #ddd;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
    }

    /* Orange header / footer */
    .header-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 70px;
        background: #f7941d;
    }
    .footer-bg {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 35px;
        background: #f7941d;
    }

    .content {
        position: relative;
        padding: 25mm 18mm 20mm;
    }

    .form-title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        color: #f7941d;
        letter-spacing: 1px;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .section-title {
        font-weight: bold;
        margin: 18px 0 8px;
        padding-bottom: 2px;
        font-size: 13px;
        color: #f7941d;
        border-bottom: 1px solid #f7941d;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 25px;
    }

    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
        .content {
            padding: 80px 15px 45px;
        }
        .field-line {
            flex-wrap: wrap;
        }
    }

    /* Label : ________ style */
    .field-line {
        display: flex;
        align-items: center;
        font-size: 14px;
        margin-bottom: 6px;
    }
    .label-inline {
        min-width: 135px;      /* adjust to taste */
        font-weight: bold;
        white-space: nowrap;
    }
    .line {
        flex: 1;
        border-bottom: 1px solid #000;
        min-height: 14px;
        padding: 1px 2px 0;
    }
    .line span {
        font-weight: normal;
    }
    /* multi-line box (for note) */
    .line-multi {
        border: 1px solid #000;
        min-height: 40px;
        padding: 4px 4px 2px;
    }

    /* Valve table */
    table {
        width: 100%;
        border-collapse: collapse;
        /* font-size: 12px; */
        margin-top: 6px;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 4px 6px;
        text-align: left;
    }
    th {
        background: #f9f2e8;
        font-weight: bold;
    }

    @media print {
        body {
            background: #fff;
            padding: 0;
        }
        .print-page {
            border: none;
            margin: 0;
            width: auto;
            min-height: auto;
        }
        .content {
            padding: 20mm 15mm 15mm;
        }
        .no-print {
        display: none !important;
    }
    }
    .logo {
    position: absolute;
    top: 12px;
    right: 15mm;
}

.logo img {
    height: 45px;     /* adjust size if needed */
    max-width: 120px;
}

.radio-group {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 12px;
}

.radio-box {
    width: 12px;
    height: 12px;
    border: 1px solid #000;
    display: inline-block;
    margin-right: 4px;
    position: relative;      /* needed for ::after */
    vertical-align: middle;
}

/* Checked state – puts a ✓ or X inside the box */
.radio-box.checked::after {
    content: '✓';            /* or 'X' if you prefer */
    position: absolute;
    top: -3px;               /* adjust these 2 values */
    left: 1px;               /* to center the mark */
    font-size: 12px;
    line-height: 12px;
}
</style>
<!-- {{ $response }} -->
<div class="print-page" >
    <div class="header-bg"></div>
    <div class="logo">
    <img src="/img/logo.png" alt="Company Logo">
</div>
    <div class="content">

        <div class="form-title">JOB CARD</div>

        <!-- Top: IMEI / Log Days -->
        <div class="grid-2">
            <div class="field-line">
                <span class="label-inline">IMEI :</span>
                <span class="line"><span><?=$response['imei'] ?? '' ?></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Master Serial No :</span>
                <span class="line"><span><?=$response['log_days'] ?? '' ?></span></span>
            </div>
        </div>

        <!-- GSM / LORA / Master -->
        <div class="section-title">Module Configuration</div>

        <div class="grid-2">
            <div class="field-line">
                <span class="label-inline">GSM Module Type :</span>
                <span class="line"><span> <?=$response['gsm_module'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Sim Scope :</span>
                <span class="line"><span> <?=$response['sim_scope'] ?? '' ?> </span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">LORA Module :</span>
                <span class="line"><span> <?=$response['lora_module'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">LORA Frequency :</span>
                <span class="line"><span> <?=$response['lora_freq'] ?? '' ?> </span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">LORA Channel :</span>
                <span class="line"><span> <?=$response['lora_channel'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">LORA SF :</span>
                <span class="line"><span> <?=$response['lora_SF'] ?? '' ?> </span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">LORA Joint :</span>
                <span class="line"><span> <?=$response['lora_joint'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Master :</span>
                <span class="line"><span> <?=$response['master'] ?? '' ?> </span></span>
            </div>

            <!-- <div class="field-line">
                <span class="label-inline">OTAA :</span>
                <span class="line"><span> <$response['log_days'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">SSC :</span>
                <span class="line"><span> <$response['log_days'] ?? '' ?> </span></span>
            </div> -->

            <!-- <div class="field-line">
                <span class="label-inline">Master IMEI No :</span>
                <span class="line"><span> <?=$response['master_imei_no'] ?? '' ?> </span></span>
            </div> -->
            <div class="field-line">
                <span class="label-inline">Master OS Version :</span>
                <span class="line"><span> <?=$response['master_os'] ?? '' ?> </span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">Repeater :</span>
                <span class="line"><span> <?=$response['repeter'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Valve Type :</span>
                <span class="line"><span> <?=$response['valve_type'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Valve Qty :</span>
                <span class="line"><span> <?=$response['valve_qty'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Butterfly Valve Qty :</span>
                <span class="line"><span> <?=$response['butterfly_valve'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Valve Fitting :</span>
                <span class="line"><span> <?=$response['valve_fitting'] ?? '' ?> </span></span>
            </div>
        </div>

        <!-- Valve table -->
        <div class="section-title">Valve Details</div>
        <table>
            <thead>
                <tr>
                    <th style="width:50%;">Valve Type (Inch)</th>
                    <th style="width:50%;">Valve Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($response['valves'])): ?>
                    <?php foreach ($response['valves'] as $row): ?>
                        <tr>
                            <td><?= $row['type'].'"' ?? '' ?></td>
                            <td><?= $row['qty'] ?? '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>

          <!-- Valve table -->
        <div class="section-title">EBPower Details</div>
        <table>
            <thead>
                <tr>
                    <th style="width:50%;">Power Type (Inch)</th>
                    <th style="width:50%;">Value</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($response['ebPower'])): ?>
                    <?php foreach ($response['ebPower'] as $row): ?>
                        <tr>
                            <td><?= $row['type'].'"' ?? '' ?></td>
                            <td><?= $row['qty'] ?? '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>

        <!-- Hardware & Sensors -->
        <div class="section-title">Hardware &amp; Sensors</div>

        <div class="grid-2">
            <div class="field-line">
                <span class="label-inline">Cable - Meter :</span>
                <span class="line"><span> <?=$response['cable_mtr'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Cable Brand :</span>
                <span class="line"><span> <?=$response['cable_brand'] ?? '' ?> </span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">High Gain :</span>
                <span class="line"><span> <?=$response['high_gain'] ?? '' ?> </span></span>
            </div>
            
            <div class="field-line">
                <span class="label-inline">Solar Power :</span>
                <span class="line"><span> <?=$response['solar_power'] ?? '' ?> </span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">Level Sensor :</span>
                <span class="line"><span> <?=$response['level_sensor'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Pressure Sensor Qty :</span>
                <span class="line"><span> <?=$response['presere_sensor'] ?? '' ?> </span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">Filter :</span>
                <div class="radio-group">
                    <span>
                        <span class="radio-box <?= ($response['filter'] == 'yes') ? 'checked' : '' ?>"></span> Yes
                    </span>
                    <span>
                        <span class="radio-box <?= ($response['filter'] == 'no') ? 'checked' : '' ?>"></span> No
                    </span>
                </div>
            </div>
            <div class="field-line">
                <span class="label-inline">Flow Sensor :</span>
                <div class="radio-group">
                    <span>
                        <span class="radio-box <?= ($response['flow_sensor'] == 'yes') ? 'checked' : '' ?>"></span> Yes
                    </span>
                    <span>
                        <span class="radio-box <?= ($response['flow_sensor'] == 'no') ? 'checked' : '' ?>"></span> No
                    </span>
                </div>
            </div>

            <div class="field-line">
            <span class="label-inline">Weather Monitor :</span>
                <div class="radio-group">
                    <span>
                        <span class="radio-box <?= ($response['weather_monitor'] == 'yes') ? 'checked' : '' ?>"></span> Yes
                    </span>
                    <span>
                        <span class="radio-box <?= ($response['weather_monitor'] == 'no') ? 'checked' : '' ?>"></span> No
                    </span>
                </div>
            </div>

            <div class="field-line">
            <span class="label-inline">Fertilizer System :</span>
            <div class="radio-group">
                <span>
                    <span class="radio-box <?= ($response['fert_sys'] == 'yes') ? 'checked' : '' ?>"></span> Yes
                </span>
                <span>
                    <span class="radio-box <?= ($response['fert_sys'] == 'no') ? 'checked' : '' ?>"></span> No
                </span>
            </div>
        </div>
            
            <div class="field-line">
                <span class="label-inline">Sensor Moisture:</span>
                <span class="line"><span> <?=$response['mois_sensor'] ?? '' ?> </span></span>
            </div>

            <!-- @if (!empty($response['fert_sys']) && $response['fert_sys'] === 'yes') -->
            <?php if (!empty($response['fert_sys']) && $response['fert_sys'] === 'yes') { ?>
               
                <div class="field-line">
                <span class="label-inline">NPK Qty :</span>
                <span class="line"><span> <?=$response['npk'] ?? '' ?> </span></span>
                </div>
            
            <div class="field-line">
                <span class="label-inline">NPK Qty :</span>
                <span class="line"><span> <?=$response['npk'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">EC Qty :</span>
                <span class="line"><span> <?=$response['ec'] ?? '' ?> </span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">pH Qty :</span>
                <span class="line"><span> <?=$response['ph'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">All in Qty :</span>
                <span class="line"><span> <?=$response['all_in'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Tank Count :</span>
                <span class="line"><span> <?=$response['tank_count'] ?? '' ?> </span></span>
            </div>

        <!-- <div class="field-line">
            <span class="label-inline">Tank Count :</span>
            <div class="radio-group">
                <span>
                    <span class="radio-box <($response['tank_count'] == 'yes') ? 'checked' : '' ?>"></span> Yes
                </span>
                <span>
                    <span class="radio-box <($response['tank_count'] == 'no') ? 'checked' : '' ?>"></span> No
                </span>
            </div>
        </div> -->
        <div class="field-line">
        <span class="label-inline">Agitator Type :</span>
            <div class="radio-group">
                <span>
                    <span class="radio-box <?= ($response['agitator_tape'] == 'air') ? 'checked' : '' ?>"></span> Air
                </span>
                <span>
                    <span class="radio-box <?= ($response['agitator_tape'] == 'motor') ? 'checked' : '' ?>"></span> Motor
                </span>
            </div>
        </div>
        <div class="field-line">
        <span class="label-inline">Injector Type :</span>
            <div class="radio-group">
                <span>
                    <span class="radio-box <?= ($response['injectTape'] == 'vento') ? 'checked' : '' ?>"></span> Ventori
                </span>
                <span>
                    <span class="radio-box <?= ($response['injectTape'] == 'normal') ? 'checked' : '' ?>"></span> Normal
                </span>
            </div>
        </div>
        <?php } ?>

        <div class="field-line">
            <span class="label-inline">Install Date:</span>
                <span class="line"><span> <?=$response['install_date'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Program Folder:</span>
                <span class="line"><span> <?=$response['prog_folder'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Install Actuator:</span>
                <span class="line"><span> <?=$response['install_actuator'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Panel:</span>
                <span class="line"><span> <?=$response['panel'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Install Done by:</span>
                <span class="line"><span> <?=$response['install_done'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Sales Person:</span>
                <span class="line"><span> <?=$response['sales_person'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Location:</span>
                <span class="line"><span> <?=$response['location'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Customized:</span>
                <span class="line"><span> <?=$response['customized'] ?? '' ?> </span></span>
            </div>
        </div>
        <!-- Hardware & Sensors -->
        <div class="section-title">Customer Details:</div>
        <div class="grid-2">
        <div class="field-line">
            <span class="label-inline">Customer:</span>
                <span class="line"><span> <?=$response['customer_name'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Mobile:</span>
                <span class="line"><span> <?=$response['customer_mobile'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">Village:</span>
                <span class="line"><span> <?=$response['customer_village'] ?? '' ?> </span></span>
            </div>
            <div class="field-line">
            <span class="label-inline">City:</span>
                <span class="line"><span> <?=$response['customer_city'] ?? '' ?> </span></span>
            </div>
        </div>
        <!-- Note -->
        <div class="section-title">Note</div>
        <div class="field-line">
            <span class="label-inline">Note :</span>
            <span class="line line-multi"><span> <?=$response['note'] ?? '' ?> </span></span>
        </div>
        <button type="button" class="btn btn-primary btn-sm float-right no-print" onclick="printImeiPage()">Print</button>
    </div>

    <div class="footer-bg"></div>
</div>
</div>
<script>

function printImeiPage() {
    const printContents = document.getElementById('imeiPrintPage').innerHTML;

    const printWindow = window.open('', '', 'height=900,width=800');
    printWindow.document.write('<html><head><title>Manage IMEI</title>');

    // Copy <style> and <link rel="stylesheet"> from current page
    const styles = document.querySelectorAll('style, link[rel="stylesheet"]');
    styles.forEach(node => {
        printWindow.document.write(node.outerHTML);
    });

    printWindow.document.write('</head><body>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    printWindow.focus();
    printWindow.print();
    printWindow.close();
}
</script>
