<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Payment Status</title>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Payment Status</h5>
        </div>
        <div class="card-body">

        <?php

        //INCLUDE ACCESS TOKEN FILE 
        include 'accessToken.php';
        date_default_timezone_set('Africa/Nairobi');
        $query_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
        $BusinessShortCode = '174379';
        $Timestamp = date('YmdHis');
        $passkey = "YOUR_PASS_KEY";
        // ENCRIPT  DATA TO GET PASSWORD
        $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
        //THIS IS THE UNIQUE ID THAT WAS GENERATED WHEN STK REQUEST INITIATED SUCCESSFULLY
        $CheckoutRequestID = $_GET['checkout_request_id'];
        $queryheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
        # initiating the transaction
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $query_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $queryheader); //setting custom header
        $curl_post_data = array(
          'BusinessShortCode' => $BusinessShortCode,
          'Password' => $Password,
          'Timestamp' => $Timestamp,
          'CheckoutRequestID' => $CheckoutRequestID
        );
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        $data_to = json_decode($curl_response);
        // Check the ResultCode and set appropriate message and background color
        // Display messages based on ResultCode
        $message1037 = "Timeout in completing transaction";
        $message0 = "Transaction was successful";
        $message1032 = "Transaction cancelled by the user";
        $message1 = "Insufficient Balance for the transaction";
        
        echo '<div class="alert alert-secondary" role="alert">Please wait as your transaction is being processed. </div>';
        
            if (isset($data_to->ResultCode)) {
                $ResultCode = $data_to->ResultCode;
                switch ($ResultCode) {
                    case '1037':
                        echo '<div class="alert alert-primary" role="alert">' . $message1037 . '</div>';
                        break;
                    case '0':
                        echo '<div class="alert alert-success" role="alert">' . $message0 . '</div>';
                        break;
                    case '1032':
                        echo '<div class="alert alert-danger" role="alert">' . $message1032 . '</div>';
                        break;
                    case '1':
                        echo '<div class="alert alert-warning" role="alert">' . $message1 . '</div>';
                        break;
                    default:
                        echo '<div class="alert alert-info" role="alert">Unknown ResultCode</div>';
                        break;
                }
                // / Add the "Finish Transaction" button
                echo '<a href="stkPush.php" class="btn btn-success">Finish Transaction</a>';
            }
        ?>
</div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
