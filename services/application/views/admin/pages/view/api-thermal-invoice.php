<style>
    @media print {
        /* body * {
            visibility: hidden;
        } */

        #section-not-to-print,
        #section-not-to-print * {
            display: none;
        }

        #section-to-print,
        #section-to-print * {
            visibility: visible;
        }

        #section-to-print {
            position: absolute;
            left: 0;
            top: 0;
        }
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .button {
        background-color: #4CAF50;
        /* Green */
        border: none;
        color: white;
        padding: 16px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        transition-duration: 0.4s;
        cursor: pointer;
        background-color: white;
        color: black;
        border: 2px solid #e7e7e7;
    }

    .button4:hover {
        background-color: #e7e7e7;
    }

    /* #products {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #products td,
    #products th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #products tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #products tr:hover {
        background-color: #ddd;
    }

    #products th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #04AA6D;
        color: white;
    } */
</style>
<html>

<head>
    <title>Thrmal Invoice</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="icon" href="<?= base_url() . get_settings('favicon') ?>" type="image/gif" sizes="16x16">
</head>
<!--Get your own code at fontawesome.com-->

<body style="font-size: 35px;font-family:Verdana;">
    <?php $partners = array_values(array_unique(array_column($order_detls, "partner_id"))); ?>
    <div id="section-to-print">
        <address style="text-align: center;">
            <strong><?= $settings['app_name'] ?></strong><br>
            Email: <?= $settings['support_email'] ?><br>
            Customer Care : <?= $settings['support_number'] ?><br>
            <b>Order No : </b>#
            <?= $order_detls[0]['id'] ?>
            <br> <b>Date: </b>
            <?= date("d-m-Y, g:i A - D", strtotime($order_detls[0]['date_added'])) ?>
            <br>
            <?php if (isset($settings['tax_name']) && !empty($settings['tax_name'])) { ?>
                <b><?= $settings['tax_name'] ?></b> : <?= $settings['tax_number'] ?><br>

            <?php } ?>
            <hr>
            <!-- ------------------------------------------------------- -->

        </address>
        <table>
            <tr>
                <td>
                    <div>Delivery Address<address>
                            <strong><?= ($order_detls[0]['user_name'] != "") ? $order_detls[0]['user_name'] : $order_detls[0]['uname'] ?></strong><br>
                            <?= $order_detls[0]['address'] ?><br>
                            <strong><?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['mobile']) - 3) . substr($order_detls[0]['mobile'], -3) : $order_detls[0]['mobile']; ?></strong><br>
                            <strong><?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['email']) - 3) . substr($order_detls[0]['email'], -3) : $order_detls[0]['email']; ?></strong><br>
                        </address>
                    </div>
                </td>
                <td>
                    <!-- here we have to add admin details for invoice         -->
                </td>
                <?php
                if (isset($is_add_ons) && in_array(true, $is_add_ons)) { ?>
                    <div class="row m-3">
                        <p>Add Ons Details:</p>
                    </div>
                    <div>
                        <table>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Add On</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                            <?php
                            $final_price_add_ons = 0;
                            $i = 1;
                            foreach ($items as $row) {
                                if (isset($row['add_ons']) && !empty($row['add_ons']) && $row['add_ons'] != "" && $row['add_ons'] != "[]") {
                                    $add_ons = json_decode($row['add_ons'], true);
                                    foreach ($add_ons as $row1) {
                                        $final_price_add_ons += intval($row1['qty']) * intval($row1['price']);
                            ?>
                                        <tr>
                                            <th><?= $i ?></th>
                                            <td><?= $row['pname'] ?></td>
                                            <td><?= $row1['title'] ?></td>
                                            <td><?= $row1['qty'] ?></td>
                                            <td><?= intval($row1['price']) ?></td>
                                            <td><?= intval($row1['qty']) * intval($row1['price']) ?></td>
                                        </tr>
                            <?php
                                        $i++;
                                    }
                                }
                            } ?>
                        </table>
                    </div>
                <?php } ?>
                <div>
                    <p><b>Payment Method : </b> <?= $order_detls[0]['payment_method'] ?></p>
                </div>
                <div>
                    <table align="right" style="width: 90%;">
                        <?php
                        $settings = get_settings('system_settings', true);
                        $tax = fetch_details(['id' => $settings['tax']], 'taxes', 'percentage,title');
                        $tax_per = ($tax[0]['percentage']);
                        $tax_name = ($tax[0]['title']);
                        $tax_amount = $tax_amount = intval($order_detls[0]['order_total']) * ($tax_per / 100);
                        ?>
                        <tr>
                            <th></th>
                        </tr>
                        <tr class="">
                            <th>Tax <?= $tax_name ?> (<?= $tax_per ?>%)</th>
                            <td>+
                                <?php
                                echo $settings['currency'] . ' ' . number_format($tax_amount, 2); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Order Price(<?= $settings['currency'] ?>)<br /><small> (including taxes) </small></th>
                            <td>+ <?= number_format($order_detls[0]['order_total'], 2) ?></td>
                        </tr>
                        <?php
                        if (isset($promo_code[0]['promo_code'])) { ?>
                            <tr>
                                <th>Promo Discount ( <?= floatval($promo_code[0]['discount']); ?>
                                    <?= ($promo_code[0]['discount_type'] == 'percentage') ? '%' : $settings['currency']; ?> )
                                </th>
                                <td>-
                                    <?php
                                    echo $order_detls[0]['promo_discount'];
                                    $total = $total - $order_detls[0]['promo_discount'];
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (isset($order_detls[0]['is_self_pick_up']) && empty($order_detls[0]['is_self_pick_up'])) { ?>

                            <tr>
                                <th>Delivery Charge (<?= $settings['currency'] ?>)</th>
                                <td>+ <?php $total += $order_detls[0]['delivery_charge'];
                                        echo number_format($order_detls[0]['delivery_charge'], 2); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th>Delivery Tip</th>
                            <td>+ <?= (isset($order_detls[0]['delivery_tip']) && !empty($order_detls[0]['delivery_tip'])) ? $settings['currency'] . $order_detls[0]['delivery_tip'] : "0"; ?></td>
                        </tr>
                        <tr>
                            <th>Wallet Used (<?= $settings['currency'] ?>)</th>
                            <td><?php $total -= $order_detls[0]['wallet_balance'];
                                echo  '- ' . number_format($order_detls[0]['wallet_balance'], 2); ?> </td>
                        </tr>
                        <?php
                        if (isset($order_detls[0]['discount']) && $order_detls[0]['discount'] > 0 && $order_detls[0]['discount'] != NULL) { ?>
                            <tr>
                                <th>Special Discount (<?= $settings['currency'] ?>)(<?= $order_detls[0]['discount'] ?> %)</th>
                                <td>- <?php echo $special_discount = round($total * $order_detls[0]['discount'] / 100, 2);
                                        $total = floatval($total - $special_discount);
                                        ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        <!-- <tr class="d-none">
                <th>Total Payable (<?= $settings['currency'] ?>)</th>
                <td>
                    <?= $settings['currency'] . '  ' . $total ?>
                </td>
            </tr> -->
                        <tr>
                            <th>Grand Total (<?= $settings['currency'] ?>)</th>
                            <td>
                                <?= $settings['currency'] . '  ' . number_format($order_detls[0]['total_payable'], 2) ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <hr style="margin-top: 450px">
                <div>
                    <p align="center">Thank You, Visit Us Again!</p>

                    <div id="section-not-to-print">
                        <button type='button' value='Print this page' onclick='{window.print()};' class="button button4"><i class="fas fa-print"></i> Print</button>
                    </div>
                </div>
                <!-- /.container-fluid -->

    </div>

</body>

</html>