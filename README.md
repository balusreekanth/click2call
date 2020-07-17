# click2call
free click2call web integration



# What is this for ?

click2call script allows your asterisk PBX to place a call between website visitor and your internal user (customer care /support etc.,)
without any manual intervention .



# How it works ?

User or website visitor  enters his phone number and click on submit. Phone number will be submitted to click2call script and 
asterisk wil initiate a call to defined extension . When extension is answered , asterisk will make a call to the visitor's number by creating a channel.

This script has google invisible recaptcha to prevent spam. Change site-key and reCaptcha secret with your own key and secret for google reCaptcha.





# How to use ?

Prerequisites :  

1.ASTERISK

- You must have asterisk installed 

2. WEB SERVER

- You need a web server to host these files. click2call.html file can be hosted anywhere but click2call.php file
should be hosted on asterisk server if you want to use CALLFILES method.


3. Configuration Changes

- I am using google invisible recaptcha in this script, replace secret and site-key with your own .
- Change constants to match with your configuration settings like AMI user name ,password ,port ,host ,destination extension etc., 
- If you want to use AMI (asterisk manager interface) method , then change  AMI_METHOD to TRUE. You can use only one method
at any time.
- If you choose to use AMI method and host all files on your remote website then make sure you open Asterisk manager interfacre port to your webserver IP.
- If you  choose to use CALLFILES METHOD , then make sure script has write permissions  to asterisk outgoing directory .
