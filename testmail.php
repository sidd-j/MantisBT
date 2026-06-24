<?php
require 'core.php';

$t_email = 'founder@mysportsphere.com';

if( email_send( $t_email, 'Mantis Test', 'Testing SMTP' ) ) {
    echo "Mail sent";
} else {
    echo "Mail failed";
}