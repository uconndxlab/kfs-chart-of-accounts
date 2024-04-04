<?php 
require_once('../models/Account.php');


$db = new SQLite3('../kfs_chart_of_accounts.db');
$kuali_host = "https://dev.api.finance.uconn.edu/webapi/accounts/cider/accounts";
$params = array (
        'fromDate' => '2024-01-01',
        'toDate' => '2024-12-31',
        'subfunds' => 'PLRES,OPTUI,RSTSP',
        'orgcodes' => '1832,1731,1863,1874,1300,1202'
    );

$request_url = $kuali_host . '?' . http_build_query($params);

// submit a simple curl request to the Kuali API and get the responses

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $request_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
curl_close($ch);

// decode the xml response into an array

$xml = simplexml_load_string($response);

// loop through the array and insert the data into the accounts table or update the data if it already exists

foreach ($xml->account as $account) {

    $account_exists = Account::exists_by_number($db, $account->accountNumber);

    if ($account_exists) {
        $accountModel = Account::find_by_number($db, $account->accountNumber);
    } else {
        $accountModel = new Account($db);
    }

    $accountModel->name = $account->accountName;
    $accountModel->number = $account->accountNumber;
    $accountModel->status = $account->status;
    $accountModel->effective_date = $account->effectiveDate;
    $accountModel->expiration_date = $account->expiredDate;
    $accountModel->fiscalOfficer = $account->fiscalOfficerIdentifier;
    $accountModel->accountManager = $account->accountManagerSystemIdentifier;
    $accountModel->accountSupervisor = $account->accountsSupervisorySystemsIdentifier;

    $accountModel->save();

   
}
