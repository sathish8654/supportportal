<style>
    * {
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }
    body {
        margin: 0;
        padding: 20px;
        background: #f5f5f5;
    }

    .print-page {
        width: 210mm;           /* A4 */
        min-height: 297mm;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #ddd;
        position: relative;
        overflow: hidden;
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

    /* Label : ________ style */
    .field-line {
        display: flex;
        align-items: center;
        font-size: 12px;
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
        font-size: 12px;
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
    }
    .logo {
    position: absolute;
    top: 25px;
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
}

.checked {
    background: #000;
}
</style>

<div class="print-page">
    <div class="header-bg"></div>
    <div class="logo">
    <img src="https://images.seeklogo.com/logo-png/36/2/apple-photos-logo-png_seeklogo-360372.png" alt="Company Logo">
</div>
    <div class="content">

        <div class="form-title">Manage IMEI</div>

        <!-- Top: IMEI / Log Days -->
        <div class="grid-2">
            <div class="field-line">
                <span class="label-inline">IMEI :</span>
                <span class="line"><span><!-- <?= $imei ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Log Days :</span>
                <span class="line"><span><!-- <?= $logDays ?> --></span></span>
            </div>
        </div>

        <!-- GSM / LORA / Master -->
        <div class="section-title">Module Configuration</div>

        <div class="grid-2">
            <div class="field-line">
                <span class="label-inline">GSM Module Type :</span>
                <span class="line"><span><!-- <?= $gsmModuleType ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Sim Scope :</span>
                <span class="line"><span><!-- <?= $simScope ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">LORA Module :</span>
                <span class="line"><span><!-- <?= $loraModule ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">LORA Frequency :</span>
                <span class="line"><span><!-- <?= $loraFrequency ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">LORA Channel :</span>
                <span class="line"><span><!-- <?= $loraChannel ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">LORA SF :</span>
                <span class="line"><span><!-- <?= $loraSf ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">LORA Joint :</span>
                <span class="line"><span><!-- <?= $loraJoint ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Master :</span>
                <span class="line"><span><!-- <?= $master ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">OTAA :</span>
                <span class="line"><span><!-- <?= $otaa ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">SSC :</span>
                <span class="line"><span><!-- <?= $ssc ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">Master IMEI No :</span>
                <span class="line"><span><!-- <?= $masterImei ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Master OS Version :</span>
                <span class="line"><span><!-- <?= $masterOsVersion ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">Repeater :</span>
                <span class="line"><span><!-- <?= $repeater ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Valve Type :</span>
                <span class="line"><span><!-- <?= $valveTypeMain ?> --></span></span>
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
                <!-- Example static rows; in PHP loop through your valve list -->
                <tr>
                    <td>2.5 Inch</td>
                    <td>5</td>
                </tr>
                <tr>
                    <td>8 Inch</td>
                    <td>10</td>
                </tr>
                <tr>
                    <td>4 Inch</td>
                    <td>58</td>
                </tr>
            </tbody>
        </table>

        <!-- Hardware & Sensors -->
        <div class="section-title">Hardware &amp; Sensors</div>

        <div class="grid-2">
            <div class="field-line">
                <span class="label-inline">Cable - Meter :</span>
                <span class="line"><span><!-- <?= $cableMeter ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Cable Brand :</span>
                <span class="line"><span><!-- <?= $cableBrand ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">High Gain :</span>
                <span class="line"><span><!-- <?= $highGain ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">EB Power 3P :</span>
                <span class="line"><span><!-- <?= $ebPower3p ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">EB Power 1P :</span>
                <span class="line"><span><!-- <?= $ebPower1p ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Solar Power :</span>
                <span class="line"><span><!-- <?= $solarPower ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">Level Sensor :</span>
                <span class="line"><span><!-- <?= $levelSensor ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Pressure Sensor Qty :</span>
                <span class="line"><span><!-- <?= $pressureSensorQty ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">Filter :</span>
                <span class="line"><span><!-- Yes / No --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Flow Sensor :</span>
                <span class="line"><span><!-- Yes / No --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">Weather Monitor :</span>
                <span class="line"><span><!-- Yes / No --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Sensor Moisture Qty :</span>
                <span class="line"><span><!-- <?= $sensorMoisture ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">NPK Qty :</span>
                <span class="line"><span><!-- <?= $npkQty ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">EC Qty :</span>
                <span class="line"><span><!-- <?= $ecQty ?> --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">pH Qty :</span>
                <span class="line"><span><!-- <?= $phQty ?> --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">All in Qty :</span>
                <span class="line"><span><!-- <?= $allInQty ?> --></span></span>
            </div>

           <div class="field-line">
    <span class="label-inline">Fertilizer System :</span>
    <div class="radio-group">
        <span>
            <span class="radio-box <?= ($fertilizer == 'yes') ? 'checked' : '' ?>"></span> Yes
        </span>
        <span>
            <span class="radio-box <?= ($fertilizer == 'no') ? 'checked' : '' ?>"></span> No
        </span>
    </div>
</div>

            <div class="field-line">
                <span class="label-inline">Tank Count :</span>
                <span class="line"><span><!-- Yes / No or number --></span></span>
            </div>

            <div class="field-line">
                <span class="label-inline">Agitator Type :</span>
                <span class="line"><span><!-- Air / Motor --></span></span>
            </div>
            <div class="field-line">
                <span class="label-inline">Injector Type :</span>
                <span class="line"><span><!-- Ventori / Normal --></span></span>
            </div>
        </div>

        <!-- Note -->
        <div class="section-title">Note</div>
        <div class="field-line">
            <span class="label-inline">Note :</span>
            <span class="line line-multi"><span><!-- <?= $note ?> --></span></span>
        </div>
    </div>

    <div class="footer-bg"></div>
</div>
