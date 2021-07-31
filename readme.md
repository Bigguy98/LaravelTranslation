
# Software translation interface

## General 

The interface allows you to make an online translation of software DB. The process is 100% automated and does not require anything else. All that translator need credentials for logging in. The main feature of the interface - translation autosaving. It happens every time when translator clicks outside the field he worked with before.

## Domain

The interface is available following the next URL [https://interface.iqualif.com](https://interface.iqualif.com)

## Technical part

The interface is implemented as SPA. It consists of the next parts:

- Frontend: **AngularJs** application working as independent SPA which communicates with a backend via HTTP requests.
- Backed: **Laravel** application working as an API interface that receives POST/GET requests and respond with JSON data

## Server

The application is driven using the Apache webserver. The physical path where the application is: `/var/www/sqlite-laravel/` The application is on the server for our different internal management tools. The server IP is `54.87.17.63` 


