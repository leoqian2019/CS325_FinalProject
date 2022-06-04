// File: login.js
// Name: Leo Qian
// Class: CS325, Jan 2022
// Final Project
// Due date: Last day of class

"use strict;"
$(document).ready(function(){
    // check on submit if any of the filed is empty
    $(".form").submit(function(){
        if($("#email").val()=="" || $("#password").val()==""){
            alert("one or more field on this page is empty!")
            return false;
        }
    })
})