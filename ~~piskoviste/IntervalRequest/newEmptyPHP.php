<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

https://www.sitepoint.com/community/t/calling-apis-every-x-seconds/432072

James_Hibbard
SitePoint Editor
Nov 2023

    Also on some online sources i noticed that setInveral() call is assigned to a variable?

This is only important if you want to cancel the timer. E.g.:

// Set an interval to log a message every second
const intervalId = setInterval(() => {
  console.log("Hello, World!");
}, 1000);

// Set a timeout to cancel the interval after 5 seconds
setTimeout(() => {
  clearInterval(intervalId);
  console.log("Interval has been cleared.");
}, 5000);

This will output:

Hello, World!
Hello, World!
Hello, World!
Hello, World!
Interval has been cleared.

If you don’t intend to clear the timer, you don’t need to worry about assigning it to a variable.

const interval = 30000

function sendApiRequest() {
  fetchData().finally(() => {
    setTimeout(sendApiRequest, interval)
  })
}

sendApiRequest()

    I guess it will get cancelled once the user closes the page.

Yup. :-)