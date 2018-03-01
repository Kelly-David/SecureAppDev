# SecureAppDev
### Personally Identifiable Information Retention 
In the following project no personally identifiable information regarding users should be stored in a human readable form in the underlying database.  			(20 Marks)
### Register
A user will be required to initially register with the system using a chosen UserID, Password, email address and date of birth. Once registered the user can authenticate. 		(10 Marks)
### Lockout mechanism
A lockout mechanism should be in place on the login page on 5 unsuccessful authentication attempts within 5 minutes.								(10 Marks)
### Forgot Password
If the user cannot remember their password they should have a “Forgot Password” functionality to facilitate the end user to reset their password.
The “Forgot Password” option should generate a Single Use, Time Limited (5 minutes) reset password token that that enables the user to update their forgotten password.

Ideally this would be emailed to the end user however for this project it will be sufficient to return the Reset Password Token value to the user via a web page.
The application will then allow the user to reset their password by entering their Email address, date of birth and the Password Reset Token along with their updated password (entered twice), abiding by password complexity rules outlined at the following link.											(30 Marks)
https://technet.microsoft.com/en-us/library/cc786468(v=ws.10).aspx

Once the password has been reset the user should now be capable of authenticating with the system.
### Logging
The application should implement logging to a text file the following activities. 
•	Successful login attempts.
•	Unsuccessful login attempts.
•	Password resets.
•	Exceptions thrown whilst querying the underlying database
Log files should include the following information:
Who (User), When (Timestamp), Where (Context/ Action being performed), What (Command / Database query), Result (Exception, deny, success) 														(30 Marks)

