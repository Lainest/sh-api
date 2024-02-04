 # STEWARD HUB
 ## AN API FOR STEWARD ORGANIZATION
 
 ---
 
 ## Intro
 
 The aim of this project is to create an api for a web app that helps steward workers and organizers.
 For example organizers can create EVENTS at the endpoint */api-sh/events/create* and users can see them at */api-sh/ events/all*
 
 ## STACK
 + PHP
 + MySql
 + Postman
 
 
 ## SECURITY
 
 + ### AUTHENTICATION
     Users can login at */api-sh/users/login* by providing username and password. Users can only be registered by  organizers at */api-sh/users/login* with the requested fields. In both cases the server responds with two JWTs  tokens:
     - **Access token** sent with the body of the response, has a short time expiration 
     - **Refresh token** sent as an HTTPONLY cookie and SAMESITE policy, has a longer time expiration 
 
 + ### AUTHORIZATION
     Every request, apart from login and refresh, needs a JWT. The token stores the user's role, either 2 for normal  user or 1 for Admin (organizer)
 
 + ### VALIDATION
     Every request is validated with a simple custom class, in case the input is missing or is in the wrong format the  server responds with an error message.
 
 + ### SANITIZATION
     The server's responses are always sanitized to prevent XSS attacks
 
 + ### CRSF PROTECTION
     Currently missing
 
 
 