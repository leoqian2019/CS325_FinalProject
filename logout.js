// File: logout.js
// Name: Leo Qian
// Class: CS325, Jan 2022
// Final Project
// Due date: Last day of class

"use strict;"
$(document).ready(function(){
    $("#signout").click(function(){
        alert("You are logged out, now redirecting to homepage");
        location.replace("logout.php");
    })
})