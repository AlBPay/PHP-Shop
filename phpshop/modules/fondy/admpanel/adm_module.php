<?php
PHPShopObj::loadClass('order');
// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['fondy']['fondy_system']);

// ������� ����������
function actionUpdate()
{
    global $PHPShopOrm, $PHPShopModules;
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ���������� ������ ������
function actionBaseUpdate()
{
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    return $action;
}

function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = '<script src="/phpshop/modules/fondy/admpanel/assets.js"></script>';
    $Tab1 .= $PHPShopGUI->setField('����� ������',
        $PHPShopGUI->setRadio('mode_work_new', 'work', '�������', $data['mode_work']) .
        $PHPShopGUI->setRadio('mode_work_new', 'test', '��������', $data['mode_work'])
    );

    $Tab1 .= $PHPShopGUI->setField('Merchant ID:', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 300));
    $Tab1 .= $PHPShopGUI->setField('Secret key:', $PHPShopGUI->setInputText(false, 'password_new', $data['password'], 300));

    $paymentType = array(
        array('Redirect to Fondy', 'redirect', $data['payment_type']),
//        array('In store payment page', 'in_store_payment_page', $data['payment_type']),
//        array('Built in checkout', 'built_in_checkout', $data['payment_type']),
        array('������ ����� ������������� ��������', 'checkout_after_status', $data['payment_type']),
    );
    $Tab1 .= $PHPShopGUI->setField('Payment type:', $PHPShopGUI->setSelect('payment_type_new', $paymentType, 300));

    // ������ ������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status_checkout']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status_checkout']);

    $Tab1 .= $PHPShopGUI->setField('������ ������ ��� ������:', $PHPShopGUI->setSelect('status_checkout_new', $order_status_value, 300), '', '', 'status-checkout');

    /*
    $transactionType = array(
        array('Authorization', 'authorization', $data['transaction_type']),
        array('Sale', 'sale', $data['transaction_type'])
    );
    $Tab1 .= $PHPShopGUI->setField('Transaction method:', $PHPShopGUI->setSelect('transaction_type_new', $transactionType, 300));
    */

    $Tab1 .= $PHPShopGUI->setField('��������� ��������������� ��������:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    $Tab1 .= $PHPShopGUI->setField('�������� ������:', $PHPShopGUI->setTextarea('title_payment_new', $data['title_payment']));

    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � ��������� ������� � <a href="https://example.com" target="_blank">Fondy</a></li>
<li>�� �������� ��������� ������ "Merchant ID" � "Secret key", ������� ����� ����� � <a href="https://portal.albpay.io/" target="_blank">������ ��������</a>, � ���� "��������� ��������" -> "����������� ���������".</li>
</ol>';

    $Tab3 = $PHPShopGUI->setPay(null, false, $data['version'], false);

    $PHPShopGUI->setTab(array("���������", $Tab1, true), array("����������", $info, true), array("� ������", $Tab3));

    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

$PHPShopGUI->getAction();
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');